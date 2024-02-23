<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Validators;

use MyParcelCom\ApiSdk\Resources\Interfaces\CollectionInterface;
use MyParcelCom\ApiSdk\Traits\HasErrors;

class CollectionValidator implements ValidatorInterface
{
    use HasErrors;

    public function __construct(private CollectionInterface $collection)
    {
    }

    public function isValid(): bool
    {
        $this->clearErrors();

        $this->validateAttributes();
        $this->validateRelationships();

        return !$this->hasErrors();
    }

    /**
     * @return void
     */
    public function validateAttributes(): void
    {
        if ($this->collection->getAddress()?->getStreet1() === null) {
            $this->addError('Attribute address.street_1 is required');
        }
        if ($this->collection->getAddress()?->getCity() === null) {
            $this->addError('Attribute address.city is required');
        }
        if ($this->collection->getAddress()?->getCountryCode() === null) {
            $this->addError('Attribute address.country_code is required');
        }

        if ($this->collection->getCollectionTime()?->getFrom() === null) {
            $this->addError('Attribute collection_time.from is required');
        }
        if ($this->collection->getCollectionTime()?->getTo() === null) {
            $this->addError('Attribute collection_time.to is required');
        }
    }

    private function validateRelationships(): void
    {
        if ($this->collection->getShop()?->getId() === null) {
            $this->addError('Attribute shop.id is required');
        }

        if (
            $this->collection->getContract()
            && !$this->collection->getContract()->getCarrier()->getOffersCollections()
        ) {
            $this->addError('The carrier of the contract does not offer collections');
        }
    }
}
