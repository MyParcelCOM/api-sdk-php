<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources;

use MyParcelCom\ApiSdk\Resources\Interfaces\CarrierInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceRateInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;

class Service implements ServiceInterface
{
    use JsonSerializable;
    use Resource;

    const ATTRIBUTE_NAME = 'name';
    const ATTRIBUTE_CODE = 'code';
    const ATTRIBUTE_PACKAGE_TYPE = 'package_type';
    const ATTRIBUTE_TRANSIT_TIME = 'transit_time';
    const ATTRIBUTE_DELIVERY_DAYS = 'delivery_days';
    const ATTRIBUTE_TRANSIT_TIME_MIN = 'min';
    const ATTRIBUTE_TRANSIT_TIME_MAX = 'max';
    const ATTRIBUTE_HANDOVER_METHOD = 'handover_method';
    const ATTRIBUTE_DELIVERY_METHOD = 'delivery_method';
    const ATTRIBUTE_REGIONS_FROM = 'regions_from';
    const ATTRIBUTE_REGIONS_TO = 'regions_to';
    const ATTRIBUTE_USES_VOLUMETRIC_WEIGHT = 'uses_volumetric_weight';
    const ATTRIBUTE_VOLUMETRIC_WEIGHT_DIVISOR = 'volumetric_weight_divisor';

    const RELATIONSHIP_CARRIER = 'carrier';

    private ?string $id = null;

    private string $type = ResourceInterface::TYPE_SERVICE;

    private array $attributes = [
        self::ATTRIBUTE_NAME                      => null,
        self::ATTRIBUTE_CODE                      => null,
        self::ATTRIBUTE_PACKAGE_TYPE              => null,
        self::ATTRIBUTE_REGIONS_FROM              => [],
        self::ATTRIBUTE_REGIONS_TO                => [],
        self::ATTRIBUTE_TRANSIT_TIME              => [
            self::ATTRIBUTE_TRANSIT_TIME_MIN => null,
            self::ATTRIBUTE_TRANSIT_TIME_MAX => null,
        ],
        self::ATTRIBUTE_HANDOVER_METHOD           => null,
        self::ATTRIBUTE_DELIVERY_DAYS             => [],
        self::ATTRIBUTE_DELIVERY_METHOD           => null,
        self::ATTRIBUTE_USES_VOLUMETRIC_WEIGHT    => false,
        self::ATTRIBUTE_VOLUMETRIC_WEIGHT_DIVISOR => null,
    ];

    private array $relationships = [
        self::RELATIONSHIP_CARRIER => [
            'data' => null,
        ],
    ];

    /** @var ServiceRateInterface[] */
    private array $serviceRates = [];

    /** @var callable */
    private $serviceRatesCallback = null;

    public function setName(string $name): self
    {
        $this->attributes[self::ATTRIBUTE_NAME] = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->attributes[self::ATTRIBUTE_NAME];
    }

    public function setCode(string $code): self
    {
        $this->attributes[self::ATTRIBUTE_CODE] = $code;

        return $this;
    }

    public function getCode(): string
    {
        return $this->attributes[self::ATTRIBUTE_CODE];
    }

    public function setPackageType(string $packageType): self
    {
        $this->attributes[self::ATTRIBUTE_PACKAGE_TYPE] = $packageType;

        return $this;
    }

    public function getPackageType(): string
    {
        return $this->attributes[self::ATTRIBUTE_PACKAGE_TYPE];
    }

    public function getTransitTimeMin(): ?int
    {
        return $this->attributes[self::ATTRIBUTE_TRANSIT_TIME][self::ATTRIBUTE_TRANSIT_TIME_MIN];
    }

    public function setTransitTimeMin(?int $transitTimeMin): self
    {
        $this->attributes[self::ATTRIBUTE_TRANSIT_TIME][self::ATTRIBUTE_TRANSIT_TIME_MIN] = $transitTimeMin;

        return $this;
    }

    public function getTransitTimeMax(): ?int
    {
        return $this->attributes[self::ATTRIBUTE_TRANSIT_TIME][self::ATTRIBUTE_TRANSIT_TIME_MAX];
    }

    public function setTransitTimeMax(?int $transitTimeMax): self
    {
        $this->attributes[self::ATTRIBUTE_TRANSIT_TIME][self::ATTRIBUTE_TRANSIT_TIME_MAX] = $transitTimeMax;

        return $this;
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

    public function setHandoverMethod(string $handoverMethod): self
    {
        $this->attributes[self::ATTRIBUTE_HANDOVER_METHOD] = $handoverMethod;

        return $this;
    }

    public function getHandoverMethod(): string
    {
        return $this->attributes[self::ATTRIBUTE_HANDOVER_METHOD];
    }

    public function setDeliveryDays(array $deliveryDays): self
    {
        $this->attributes[self::ATTRIBUTE_DELIVERY_DAYS] = [];

        array_walk($deliveryDays, function ($deliveryDay) {
            $this->addDeliveryDay($deliveryDay);
        });

        return $this;
    }

    public function addDeliveryDay(string $deliveryDay): self
    {
        $this->attributes[self::ATTRIBUTE_DELIVERY_DAYS][] = $deliveryDay;

        return $this;
    }

    public function getDeliveryDays(): array
    {
        return $this->attributes[self::ATTRIBUTE_DELIVERY_DAYS];
    }

    public function getDeliveryMethod(): string
    {
        return $this->attributes[self::ATTRIBUTE_DELIVERY_METHOD];
    }

    public function setDeliveryMethod(string $deliveryMethod): self
    {
        $this->attributes[self::ATTRIBUTE_DELIVERY_METHOD] = $deliveryMethod;

        return $this;
    }

    public function setRegionsFrom(array $regions): self
    {
        $this->attributes[self::ATTRIBUTE_REGIONS_FROM] = $regions;

        return $this;
    }

    public function getRegionsFrom(): array
    {
        return $this->attributes[self::ATTRIBUTE_REGIONS_FROM];
    }

    public function setRegionsTo(array $regions): self
    {
        $this->attributes[self::ATTRIBUTE_REGIONS_TO] = $regions;

        return $this;
    }

    public function getRegionsTo(): array
    {
        return $this->attributes[self::ATTRIBUTE_REGIONS_TO];
    }

    public function setUsesVolumetricWeight(bool $usesVolumetricWeight): self
    {
        $this->attributes[self::ATTRIBUTE_USES_VOLUMETRIC_WEIGHT] = $usesVolumetricWeight;

        return $this;
    }

    public function usesVolumetricWeight(): bool
    {
        return $this->attributes[self::ATTRIBUTE_USES_VOLUMETRIC_WEIGHT];
    }

    public function setVolumetricWeightDivisor(?int $divisor): self
    {
        $this->attributes[self::ATTRIBUTE_VOLUMETRIC_WEIGHT_DIVISOR] = $divisor;

        return $this;
    }

    public function getVolumetricWeightDivisor(): ?int
    {
        return $this->attributes[self::ATTRIBUTE_VOLUMETRIC_WEIGHT_DIVISOR];
    }

    public function setServiceRates(array $serviceRates): self
    {
        $this->serviceRates = [];

        array_walk($serviceRates, function ($serviceRate) {
            $this->addServiceRate($serviceRate);
        });

        return $this;
    }

    public function addServiceRate(ServiceRateInterface $serviceRate): self
    {
        $this->serviceRates[] = $serviceRate;

        return $this;
    }

    public function getServiceRates(array $filters = ['has_active_contract' => 'true']): array
    {
        if (empty($this->serviceRates) && isset($this->serviceRatesCallback)) {
            $this->setServiceRates(call_user_func_array($this->serviceRatesCallback, [$filters]));
        }

        return $this->serviceRates;
    }

    public function setServiceRatesCallback(callable $callback): self
    {
        $this->serviceRatesCallback = $callback;

        return $this;
    }

    public function jsonSerialize(): array
    {
        $values = get_object_vars($this);
        unset($values['serviceRates']);

        $json = $this->arrayValuesToArray($values);

        if (isset($json['attributes']) && $this->isEmpty($json['attributes'])) {
            unset($json['attributes']);
        }
        if (isset($json['relationships']) && $this->isEmpty($json['relationships'])) {
            unset($json['relationships']);
        }

        return $json;
    }
}
