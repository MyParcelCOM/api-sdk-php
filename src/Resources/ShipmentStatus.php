<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources;

use DateTime;
use MyParcelCom\ApiSdk\Resources\Interfaces\CarrierStatusInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ErrorInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentStatusInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\StatusInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\ProcessIncludes;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;
use MyParcelCom\ApiSdk\Utils\DateUtils;

class ShipmentStatus implements ShipmentStatusInterface
{
    use JsonSerializable;
    use ProcessIncludes;
    use Resource;

    const ATTRIBUTE_CARRIER_STATUSES = 'carrier_statuses';
    const ATTRIBUTE_ERRORS = 'errors';
    const ATTRIBUTE_CREATED_AT = 'created_at';

    const RELATIONSHIP_STATUS = 'status';
    const RELATIONSHIP_SHIPMENT = 'shipment';

    const INCLUDES = [
        ResourceInterface::TYPE_SHIPMENT => self::RELATIONSHIP_SHIPMENT,
        ResourceInterface::TYPE_STATUS   => self::RELATIONSHIP_STATUS,
    ];

    private ?string $id = null;

    private string $type = ResourceInterface::TYPE_SHIPMENT_STATUS;

    private array $attributes = [
        self::ATTRIBUTE_CARRIER_STATUSES => [],
        self::ATTRIBUTE_ERRORS           => [],
        self::ATTRIBUTE_CREATED_AT       => null,
    ];

    private array $relationships = [
        self::RELATIONSHIP_STATUS   => [
            'data' => null,
        ],
        self::RELATIONSHIP_SHIPMENT => [
            'data' => null,
        ],
    ];

    public function setCarrierStatuses(array $carrierStatuses): self
    {
        $this->attributes[self::ATTRIBUTE_CARRIER_STATUSES] = [];
        foreach ($carrierStatuses as $carrierStatus) {
            $this->addCarrierStatus($carrierStatus);
        }

        return $this;
    }

    public function addCarrierStatus(CarrierStatusInterface $carrierStatus): self
    {
        $this->attributes[self::ATTRIBUTE_CARRIER_STATUSES][] = $carrierStatus;

        return $this;
    }

    public function getCarrierStatuses(): array
    {
        return $this->attributes[self::ATTRIBUTE_CARRIER_STATUSES];
    }

    public function setErrors(array $errors): self
    {
        $this->attributes[self::ATTRIBUTE_ERRORS] = [];
        foreach ($errors as $error) {
            $this->addError($error);
        }

        return $this;
    }

    public function addError(ErrorInterface $error): self
    {
        $this->attributes[self::ATTRIBUTE_ERRORS][] = $error;

        return $this;
    }

    public function getErrors(): array
    {
        return $this->attributes[self::ATTRIBUTE_ERRORS];
    }

    public function setCreatedAt(DateTime|int $createdAt): self
    {
        $this->attributes[self::ATTRIBUTE_CREATED_AT] = DateUtils::toTimestamp($createdAt);

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return (new DateTime())->setTimestamp($this->attributes[self::ATTRIBUTE_CREATED_AT]);
    }

    public function setShipment(ShipmentInterface $shipment): self
    {
        $this->relationships[self::RELATIONSHIP_SHIPMENT]['data'] = $shipment;

        return $this;
    }

    public function getShipment(): ShipmentInterface
    {
        return $this->relationships[self::RELATIONSHIP_SHIPMENT]['data'];
    }

    public function setStatus(StatusInterface $status): self
    {
        $this->relationships[self::RELATIONSHIP_STATUS]['data'] = $status;

        return $this;
    }

    public function getStatus(): StatusInterface
    {
        return $this->relationships[self::RELATIONSHIP_STATUS]['data'];
    }
}
