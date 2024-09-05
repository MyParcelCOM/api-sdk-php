<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources;

use MyParcelCom\ApiSdk\Resources\Interfaces\CarrierInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;

class Contract implements ContractInterface
{
    use JsonSerializable;
    use Resource;

    const ATTRIBUTE_CURRENCY = 'currency';
    const ATTRIBUTE_NAME = 'name';
    const ATTRIBUTE_STATUS = 'status';
    const ATTRIBUTE_VOLUMETRIC_WEIGHT_DIVISOR_FACTOR = 'volumetric_weight_divisor_factor';

    const RELATIONSHIP_CARRIER = 'carrier';

    private ?string $id = null;

    private string $type = ResourceInterface::TYPE_CONTRACT;

    private array $attributes = [
        self::ATTRIBUTE_CURRENCY                         => null,
        self::ATTRIBUTE_NAME                             => null,
        self::ATTRIBUTE_STATUS                           => null,
        self::ATTRIBUTE_VOLUMETRIC_WEIGHT_DIVISOR_FACTOR => 1.0,
    ];

    private array $relationships = [
        self::RELATIONSHIP_CARRIER => [
            'data' => null,
        ],
    ];

    public function setName(string $name): self
    {
        $this->attributes[self::ATTRIBUTE_NAME] = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->attributes[self::ATTRIBUTE_NAME];
    }

    public function setCurrency(string $currency): self
    {
        $this->attributes[self::ATTRIBUTE_CURRENCY] = $currency;

        return $this;
    }

    public function getCurrency(): string
    {
        return $this->attributes[self::ATTRIBUTE_CURRENCY];
    }

    public function setCarrier(CarrierInterface $carrier): self
    {
        $this->relationships[self::RELATIONSHIP_CARRIER]['data'] = $carrier;

        return $this;
    }

    public function getCarrier(): CarrierInterface
    {
        return $this->relationships[self::RELATIONSHIP_CARRIER]['data'];
    }

    public function setStatus(string $status): self
    {
        $this->attributes[self::ATTRIBUTE_STATUS] = $status;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->attributes[self::ATTRIBUTE_STATUS];
    }

    public function setVolumetricWeightDivisorFactor(float $factor): self
    {
        $this->attributes[self::ATTRIBUTE_VOLUMETRIC_WEIGHT_DIVISOR_FACTOR] = $factor;

        return $this;
    }

    public function getVolumetricWeightDivisorFactor(): float
    {
        return $this->attributes[self::ATTRIBUTE_VOLUMETRIC_WEIGHT_DIVISOR_FACTOR];
    }
}
