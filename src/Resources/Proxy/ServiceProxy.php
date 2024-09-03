<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Proxy;

use MyParcelCom\ApiSdk\Resources\Interfaces\CarrierInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceProxyInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceRateInterface;
use MyParcelCom\ApiSdk\Resources\Service;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\ProxiesResource;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;

/**
 * @method Service getResource()
 */
class ServiceProxy implements ServiceInterface, ResourceProxyInterface
{
    use JsonSerializable;
    use ProxiesResource;
    use Resource;

    private ?string $id = null;

    private string $type = ResourceInterface::TYPE_SERVICE;

    public function setName(string $name): self
    {
        $this->getResource()->setName($name);

        return $this;
    }

    public function getName(): string
    {
        return $this->getResource()->getName();
    }

    public function setCode(string $code): self
    {
        $this->getResource()->setCode($code);

        return $this;
    }

    public function getCode(): string
    {
        return $this->getResource()->getCode();
    }

    public function setPackageType(string $packageType): self
    {
        $this->getResource()->setPackageType($packageType);

        return $this;
    }

    public function getPackageType(): string
    {
        return $this->getResource()->getPackageType();
    }

    public function getTransitTimeMin(): ?int
    {
        return $this->getResource()->getTransitTimeMin();
    }

    public function setTransitTimeMin(?int $transitTimeMin): self
    {
        $this->getResource()->setTransitTimeMin($transitTimeMin);

        return $this;
    }

    public function getTransitTimeMax(): ?int
    {
        return $this->getResource()->getTransitTimeMax();
    }

    public function setTransitTimeMax(?int $transitTimeMax): self
    {
        $this->getResource()->setTransitTimeMax($transitTimeMax);

        return $this;
    }

    public function setCarrier(CarrierInterface $carrier): self
    {
        $this->getResource()->setCarrier($carrier);

        return $this;
    }

    public function getCarrier(): CarrierInterface
    {
        return $this->getResource()->getCarrier();
    }

    public function setHandoverMethod(string $handoverMethod): self
    {
        $this->getResource()->setHandoverMethod($handoverMethod);

        return $this;
    }

    public function getHandoverMethod(): string
    {
        return $this->getResource()->getHandoverMethod();
    }

    public function setDeliveryDays(array $deliveryDays): self
    {
        $this->getResource()->setDeliveryDays($deliveryDays);

        return $this;
    }

    public function addDeliveryDay(string $deliveryDay): self
    {
        $this->getResource()->addDeliveryDay($deliveryDay);

        return $this;
    }

    public function getDeliveryDays(): array
    {
        return $this->getResource()->getDeliveryDays();
    }

    public function getDeliveryMethod(): string
    {
        return $this->getResource()->getDeliveryMethod();
    }

    public function setDeliveryMethod(string $deliveryMethod): self
    {
        $this->getResource()->setDeliveryMethod($deliveryMethod);

        return $this;
    }

    public function setRegionsFrom(array $regions): self
    {
        $this->getResource()->setRegionsFrom($regions);

        return $this;
    }

    public function getRegionsFrom(): array
    {
        return $this->getResource()->getRegionsFrom();
    }

    public function setRegionsTo(array $regions): self
    {
        $this->getResource()->setRegionsTo($regions);

        return $this;
    }

    public function getRegionsTo(): array
    {
        return $this->getResource()->getRegionsTo();
    }

    public function setUsesVolumetricWeight(bool $usesVolumetricWeight): self
    {
        $this->getResource()->setUsesVolumetricWeight($usesVolumetricWeight);

        return $this;
    }

    public function usesVolumetricWeight(): bool
    {
        return $this->getResource()->usesVolumetricWeight();
    }

    public function setVolumetricWeightDivisor(?int $divisor): self
    {
        $this->getResource()->setVolumetricWeightDivisor($divisor);

        return $this;
    }

    public function getVolumetricWeightDivisor(): ?int
    {
        return $this->getResource()->getVolumetricWeightDivisor();
    }

    /**
     * @param ServiceRateInterface[] $serviceRates
     */
    public function setServiceRates(array $serviceRates): self
    {
        $this->getResource()->setServiceRates($serviceRates);

        return $this;
    }

    public function addServiceRate(ServiceRateInterface $serviceRate): self
    {
        $this->getResource()->addServiceRate($serviceRate);

        return $this;
    }

    /**
     * @return ServiceRateInterface[]
     */
    public function getServiceRates(array $filters = ['has_active_contract' => 'true']): array
    {
        return $this->getResource()->getServiceRates($filters);
    }

    /**
     * This function puts all object properties in an array and returns it.
     */
    public function jsonSerialize(): array
    {
        $values = get_object_vars($this);
        unset($values['resource']);
        unset($values['api']);
        unset($values['uri']);

        return $this->arrayValuesToArray($values);
    }
}
