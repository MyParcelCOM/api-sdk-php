<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources;

use MyParcelCom\ApiSdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceOptionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceRateInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\ProcessIncludes;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;

class ServiceRate implements ServiceRateInterface
{
    use JsonSerializable;
    use ProcessIncludes;
    use Resource;

    const ATTRIBUTE_PRICE = 'price';
    const ATTRIBUTE_FUEL_SURCHARGE = 'fuel_surcharge';
    const ATTRIBUTE_CURRENCY = 'currency';
    const ATTRIBUTE_AMOUNT = 'amount';
    const ATTRIBUTE_WEIGHT_MIN = 'weight_min';
    const ATTRIBUTE_WEIGHT_MAX = 'weight_max';
    const ATTRIBUTE_WEIGHT_BRACKET = 'weight_bracket';
    const ATTRIBUTE_WIDTH_MAX = 'width_max';
    const ATTRIBUTE_LENGTH_MAX = 'length_max';
    const ATTRIBUTE_HEIGHT_MAX = 'height_max';
    const ATTRIBUTE_VOLUME_MAX = 'volume_max';
    const ATTRIBUTE_IS_DYNAMIC = 'is_dynamic';

    const RELATIONSHIP_SERVICE = 'service';
    const RELATIONSHIP_CONTRACT = 'contract';
    const RELATIONSHIP_SERVICE_OPTIONS = 'service_options';

    const META_BRACKET_PRICE = 'bracket_price';

    const INCLUDES = [
        ResourceInterface::TYPE_CONTRACT => self::RELATIONSHIP_CONTRACT,
        ResourceInterface::TYPE_SERVICE  => self::RELATIONSHIP_SERVICE,
    ];

    const WEIGHT_BRACKET_START = 'start';
    const WEIGHT_BRACKET_START_AMOUNT = 'start_amount';
    const WEIGHT_BRACKET_SIZE = 'size';
    const WEIGHT_BRACKET_SIZE_AMOUNT = 'size_amount';

    private ?string $id = null;

    private string $type = ResourceInterface::TYPE_SERVICE_RATE;

    private array $attributes = [
        self::ATTRIBUTE_PRICE          => [
            self::ATTRIBUTE_AMOUNT   => null,
            self::ATTRIBUTE_CURRENCY => null,
        ],
        self::ATTRIBUTE_FUEL_SURCHARGE => [
            self::ATTRIBUTE_AMOUNT   => null,
            self::ATTRIBUTE_CURRENCY => null,
        ],
        self::ATTRIBUTE_WEIGHT_MIN     => null,
        self::ATTRIBUTE_WEIGHT_MAX     => null,
        self::ATTRIBUTE_WEIGHT_BRACKET => [
            self::WEIGHT_BRACKET_START        => null,
            self::WEIGHT_BRACKET_START_AMOUNT => null,
            self::WEIGHT_BRACKET_SIZE         => null,
            self::WEIGHT_BRACKET_SIZE_AMOUNT  => null,
        ],
        self::ATTRIBUTE_WIDTH_MAX      => null,
        self::ATTRIBUTE_LENGTH_MAX     => null,
        self::ATTRIBUTE_HEIGHT_MAX     => null,
        self::ATTRIBUTE_VOLUME_MAX     => null,
        self::ATTRIBUTE_IS_DYNAMIC     => null,
    ];

    private array $relationships = [
        self::RELATIONSHIP_SERVICE         => [
            'data' => null,
        ],
        self::RELATIONSHIP_CONTRACT        => [
            'data' => null,
        ],
        self::RELATIONSHIP_SERVICE_OPTIONS => [
            'data' => [],
        ],
    ];

    private array $meta = [
        self::META_BRACKET_PRICE => [
            self::ATTRIBUTE_AMOUNT   => null,
            self::ATTRIBUTE_CURRENCY => null,
        ],
    ];

    /** @var callable */
    private $resolveDynamicRateForShipmentCallback;

    public function setWeightMin(int $weightMin): self
    {
        $this->attributes[self::ATTRIBUTE_WEIGHT_MIN] = $weightMin;

        return $this;
    }

    public function getWeightMin(): int
    {
        return $this->attributes[self::ATTRIBUTE_WEIGHT_MIN];
    }

    public function setWeightMax(int $weightMax): self
    {
        $this->attributes[self::ATTRIBUTE_WEIGHT_MAX] = $weightMax;

        return $this;
    }

    public function getWeightMax(): int
    {
        return $this->attributes[self::ATTRIBUTE_WEIGHT_MAX];
    }

    public function setWeightBracket(array $weightBracket): self
    {
        $this->attributes[self::ATTRIBUTE_WEIGHT_BRACKET] = $weightBracket;

        return $this;
    }

    public function getWeightBracket(): array
    {
        return $this->attributes[self::ATTRIBUTE_WEIGHT_BRACKET];
    }

    public function setBracketPrice(?int $bracketPrice): self
    {
        $this->meta[self::META_BRACKET_PRICE][self::ATTRIBUTE_AMOUNT] = $bracketPrice;

        return $this;
    }

    /**
     * This will only return a value when this ServiceRate is retrieved for a shipment with a specific weight.
     */
    public function getBracketPrice(): ?int
    {
        return $this->meta[self::META_BRACKET_PRICE][self::ATTRIBUTE_AMOUNT];
    }

    public function setBracketCurrency(?string $currency): self
    {
        $this->attributes[self::META_BRACKET_PRICE][self::ATTRIBUTE_CURRENCY] = $currency;

        return $this;
    }

    /**
     * This will only return a value when this ServiceRate is retrieved for a shipment with a specific weight.
     */
    public function getBracketCurrency(): ?string
    {
        return $this->attributes[self::META_BRACKET_PRICE][self::ATTRIBUTE_CURRENCY];
    }

    public function calculateBracketPrice(int $weight): ?int
    {
        $weightBracket = $this->getWeightBracket();
        $price = $weightBracket[self::WEIGHT_BRACKET_START_AMOUNT];

        if ($price === null) {
            return null;
        }

        $remainingWeight = $weight - $weightBracket[self::WEIGHT_BRACKET_START];
        while ($remainingWeight > 0) {
            $price += $weightBracket[self::WEIGHT_BRACKET_SIZE_AMOUNT];
            $remainingWeight -= $weightBracket[self::WEIGHT_BRACKET_SIZE];
        }

        return $price;
    }

    public function setLengthMax(?int $lengthMax): self
    {
        $this->attributes[self::ATTRIBUTE_LENGTH_MAX] = $lengthMax;

        return $this;
    }

    public function getLengthMax(): ?int
    {
        return $this->attributes[self::ATTRIBUTE_LENGTH_MAX];
    }

    public function setHeightMax(?int $heightMax): self
    {
        $this->attributes[self::ATTRIBUTE_HEIGHT_MAX] = $heightMax;

        return $this;
    }

    public function getHeightMax(): ?int
    {
        return $this->attributes[self::ATTRIBUTE_HEIGHT_MAX];
    }

    public function setWidthMax(?int $widthMax): self
    {
        $this->attributes[self::ATTRIBUTE_WIDTH_MAX] = $widthMax;

        return $this;
    }

    public function getWidthMax(): ?int
    {
        return $this->attributes[self::ATTRIBUTE_WIDTH_MAX];
    }

    public function setVolumeMax(float|int|null $volumeMax): self
    {
        $this->attributes[self::ATTRIBUTE_VOLUME_MAX] = $volumeMax;

        return $this;
    }

    public function getVolumeMax(): float|int|null
    {
        return $this->attributes[self::ATTRIBUTE_VOLUME_MAX];
    }

    public function setCurrency(?string $currency): self
    {
        $this->attributes[self::ATTRIBUTE_PRICE][self::ATTRIBUTE_CURRENCY] = $currency;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->attributes[self::ATTRIBUTE_PRICE][self::ATTRIBUTE_CURRENCY];
    }

    public function setPrice(?int $price): self
    {
        $this->attributes[self::ATTRIBUTE_PRICE][self::ATTRIBUTE_AMOUNT] = $price;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->attributes[self::ATTRIBUTE_PRICE][self::ATTRIBUTE_AMOUNT];
    }

    public function setFuelSurchargeAmount(?int $amount): self
    {
        $this->attributes[self::ATTRIBUTE_FUEL_SURCHARGE][self::ATTRIBUTE_AMOUNT] = $amount;

        return $this;
    }

    public function getFuelSurchargeAmount(): ?int
    {
        return $this->attributes[self::ATTRIBUTE_FUEL_SURCHARGE][self::ATTRIBUTE_AMOUNT];
    }

    public function setFuelSurchargeCurrency(?string $currency): self
    {
        $this->attributes[self::ATTRIBUTE_FUEL_SURCHARGE][self::ATTRIBUTE_CURRENCY] = $currency;

        return $this;
    }

    public function getFuelSurchargeCurrency(): ?string
    {
        return $this->attributes[self::ATTRIBUTE_FUEL_SURCHARGE][self::ATTRIBUTE_CURRENCY];
    }

    public function setService(ServiceInterface $service): self
    {
        $this->relationships[self::RELATIONSHIP_SERVICE]['data'] = $service;

        return $this;
    }

    public function getService(): ServiceInterface
    {
        return $this->relationships[self::RELATIONSHIP_SERVICE]['data'];
    }

    public function setContract(ContractInterface $contract): self
    {
        $this->relationships[self::RELATIONSHIP_CONTRACT]['data'] = $contract;

        return $this;
    }

    public function getContract(): ContractInterface
    {
        return $this->relationships[self::RELATIONSHIP_CONTRACT]['data'];
    }

    public function setServiceOptions(array $serviceOptions): self
    {
        $this->relationships[self::RELATIONSHIP_SERVICE_OPTIONS]['data'] = [];

        foreach ($serviceOptions as $serviceOption) {
            $this->addServiceOption($serviceOption);
        }

        return $this;
    }

    public function addServiceOption(ServiceOptionInterface $serviceOption): self
    {
        $this->relationships[self::RELATIONSHIP_SERVICE_OPTIONS]['data'][] = $serviceOption;

        return $this;
    }

    public function getServiceOptions(): array
    {
        return $this->relationships[self::RELATIONSHIP_SERVICE_OPTIONS]['data'];
    }

    public function setIsDynamic(bool $isDynamic): self
    {
        $this->attributes[self::ATTRIBUTE_IS_DYNAMIC] = $isDynamic;

        return $this;
    }

    public function isDynamic(): bool
    {
        return (bool) $this->attributes[self::ATTRIBUTE_IS_DYNAMIC];
    }

    public function resolveDynamicRateForShipment(ShipmentInterface $shipment): self
    {
        if (isset($this->resolveDynamicRateForShipmentCallback)) {
            return call_user_func_array($this->resolveDynamicRateForShipmentCallback, [$shipment, $this]);
        }

        return $this;
    }

    public function setResolveDynamicRateForShipmentCallback(callable $callback): self
    {
        $this->resolveDynamicRateForShipmentCallback = $callback;

        return $this;
    }
}
