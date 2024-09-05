<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Interfaces;

interface ServiceInterface extends ResourceInterface
{
    const PACKAGE_TYPE_PARCEL = 'parcel';
    const PACKAGE_TYPE_LETTER = 'letter';
    const PACKAGE_TYPE_LETTERBOX = 'letterbox';

    const DELIVERY_METHOD_DELIVERY = 'delivery';
    const DELIVERY_METHOD_PICKUP = 'pick-up';

    public function setName(string $name): self;

    public function getName(): string;

    public function setCode(string $code): self;

    public function getCode(): string;

    public function setPackageType(string $packageType): self;

    public function getPackageType(): string;

    public function getTransitTimeMin(): ?int;

    public function setTransitTimeMin(?int  $transitTimeMin): self;

    public function getTransitTimeMax(): ?int;

    public function setTransitTimeMax(?int $transitTimeMax): self;

    public function setCarrier(CarrierInterface $carrier): self;

    public function getCarrier(): CarrierInterface;

    public function setHandoverMethod(string $handoverMethod): self;

    public function getHandoverMethod(): string;

    public function setUsesVolumetricWeight(bool $usesVolumetricWeight): self;

    public function usesVolumetricWeight(): bool;

    public function setVolumetricWeightDivisor(?int $divisor): self;

    public function getVolumetricWeightDivisor(): ?int;

    /**
     * @param string[] $deliveryDays
     */
    public function setDeliveryDays(array $deliveryDays): self;

    public function addDeliveryDay(string $deliveryDay): self;

    /**
     * @return string[]
     */
    public function getDeliveryDays(): array;

    public function getDeliveryMethod(): string;

    public function setDeliveryMethod(string $deliveryMethod): self;

    public function setRegionsFrom(array $regions): self;

    public function getRegionsFrom(): array;

    public function setRegionsTo(array $regions): self;

    public function getRegionsTo(): array;

    /**
     * @param ServiceRateInterface[] $serviceRates
     */
    public function setServiceRates(array $serviceRates): self;

    public function addServiceRate(ServiceRateInterface $serviceRate): self;

    /**
     * @return ServiceRateInterface[]
     */
    public function getServiceRates(array $filters = ['has_active_contract' => 'true']): array;
}
