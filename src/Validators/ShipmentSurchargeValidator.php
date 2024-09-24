<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Validators;

use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentSurchargeInterface;
use MyParcelCom\ApiSdk\Traits\HasErrors;

class ShipmentSurchargeValidator implements ValidatorInterface
{
    use HasErrors;

    public function __construct(
        private ShipmentSurchargeInterface $shipmentSurcharge,
    ) {
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
        if ($this->shipmentSurcharge->getName() === null) {
            $this->addError('Attribute "name" is required');
        }
        if ($this->shipmentSurcharge->getFeeAmount() === null || $this->shipmentSurcharge->getFeeCurrency() === null) {
            $this->addError('Attribute "fee" is required');
        }
    }

    private function validateRelationships(): void
    {
        if ($this->shipmentSurcharge->getShipment()?->getId() === null) {
            $this->addError('Attribute shipment.id is required');
        }
    }
}
