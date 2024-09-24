<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources;

use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentSurchargeInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;

class ShipmentSurcharge implements ShipmentSurchargeInterface
{
    use JsonSerializable;
    use Resource;

    const AMOUNT = 'amount';
    const CURRENCY = 'currency';

    private ?string $id = null;

    private string $type = ResourceInterface::TYPE_SHIPMENT_SURCHARGE;

    private array $attributes = [
        'name'        => null,
        'description' => null,
        'fee'         => [
            self::AMOUNT   => null,
            self::CURRENCY => null,
        ],
    ];

    private array $relationships = [
        'shipment' => [
            'data' => null,
        ],
    ];

    public function setName(?string $name): self
    {
        $this->attributes['name'] = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->attributes['name'];
    }

    public function setDescription(?string $description): self
    {
        $this->attributes['description'] = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->attributes['description'];
    }

    public function setFeeAmount(int $amount): self
    {
        $this->attributes['fee'][self::AMOUNT] = $amount;

        return $this;
    }

    public function getFeeAmount(): int
    {
        return $this->attributes['fee'][self::AMOUNT];
    }

    public function setFeeCurrency(string $currency): self
    {
        $this->attributes['fee'][self::CURRENCY] = $currency;

        return $this;
    }

    public function getFeeCurrency(): string
    {
        return $this->attributes['fee'][self::CURRENCY];
    }

    public function getShipment(): ?ShipmentInterface
    {
        return $this->relationships['shipment']['data'];
    }

    public function setShipment(?ShipmentInterface $shipment): self
    {
        $this->relationships['shipment']['data'] = $shipment;

        return $this;
    }
}
