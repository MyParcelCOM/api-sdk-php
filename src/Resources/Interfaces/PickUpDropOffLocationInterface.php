<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Interfaces;

use MyParcelCom\ApiSdk\Utils\DistanceUtils;

interface PickUpDropOffLocationInterface extends ResourceInterface
{
    public function setCode(string $code): self;

    public function getCode(): string;

    public function setAddress(AddressInterface $address): self;

    public function getAddress(): AddressInterface;

    /**
     * @param OpeningHourInterface[] $openingHours
     */
    public function setOpeningHours(array $openingHours): self;

    public function addOpeningHour(OpeningHourInterface $openingHour): self;

    /**
     * @return OpeningHourInterface[]
     */
    public function getOpeningHours(): array;

    public function setPosition(PositionInterface $position): self;

    public function getPosition(): PositionInterface;

    public function setCarrier(CarrierInterface $carrier): self;

    public function getCarrier(): CarrierInterface;

    public function setDistance(float|int $distance, string $unit = DistanceUtils::UNIT_METER): self;

    public function getDistance(string $unit = DistanceUtils::UNIT_METER): float|int|null;

    public function setCategories(array $categories): self;

    public function getCategories(): array;

    public function setLocationType(string $type): self;

    public function getLocationType(): string;
}
