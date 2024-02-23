<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Validators;

use MyParcelCom\ApiSdk\Resources\Interfaces\ManifestInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Traits\HasErrors;

class ManifestValidator implements ValidatorInterface
{
    use HasErrors;

    public function __construct(private ManifestInterface $manifest)
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
     * Check if the required properties are set. Add any errors to the errors array.
     */
    private function validateAttributes(): void
    {
        if ($this->manifest->getName() === null) {
            $this->addError('Attribute "name" is required');
        }

        $address = $this->manifest->getAddress();
        if ($address?->getStreet1() === null) {
            $this->addError('Attribute "address.street1" is required');
        }
        if ($address?->getCity() === null) {
            $this->addError('Attribute "address.city" is required');
        }
        if ($address?->getCountryCode() === null) {
            $this->addError('Attribute "address.countryCode" is required');
        }
    }

    private function validateRelationships(): void
    {
        $owner = $this->manifest->getOwner();
        $ownerTypes = [
            ResourceInterface::TYPE_SHOP,
            ResourceInterface::TYPE_ORGANIZATION,
            ResourceInterface::TYPE_BROKER,
        ];
        if ($owner?->getType() === null || !in_array($owner?->getType(), $ownerTypes)) {
            $this->addError('Attribute "owner.type" must be one of: ' . implode(', ', $ownerTypes));
        }
        if ($owner?->getId() === null) {
            $this->addError('Attribute "owner.id" is required');
        }

        $shipments = $this->manifest->getShipments();
        // empty arrays are removed during JSON serialization which causes the request to fail schema validation
        if (empty($shipments)) {
            $this->addError('Relationship shipments is required to contain at least 1 Shipment');
        }
        foreach ($shipments as $shipment) {
            if ($shipment->getId() === null) {
                $this->addError('Attribute shipment.id is required');

                break;
            }
        }
    }
}
