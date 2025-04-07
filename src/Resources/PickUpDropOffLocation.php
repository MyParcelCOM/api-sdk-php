<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources;

use MyParcelCom\ApiSdk\Resources\Interfaces\AddressInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\CarrierInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\OpeningHourInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\PickUpDropOffLocationInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\PositionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;
use MyParcelCom\ApiSdk\Utils\DistanceUtils;

class PickUpDropOffLocation implements PickUpDropOffLocationInterface
{
    use JsonSerializable;
    use Resource;

    const ATTRIBUTE_CODE = 'code';
    const ATTRIBUTE_ADDRESS = 'address';
    const ATTRIBUTE_OPENING_HOURS = 'openingHours';
    const ATTRIBUTE_POSITION = 'position';
    const ATTRIBUTE_CATEGORIES = 'categories';
    const ATTRIBUTE_LOCATION_TYPE = 'location_type';

    const RELATIONSHIP_CARRIER = 'carrier';

    const META_DISTANCE = 'distance';

    private ?string $id = null;

    private string $type = ResourceInterface::TYPE_PUDO_LOCATION;

    private array $attributes = [
        self::ATTRIBUTE_CODE          => null,
        self::ATTRIBUTE_ADDRESS       => null,
        self::ATTRIBUTE_OPENING_HOURS => [],
        self::ATTRIBUTE_POSITION      => null,
        self::ATTRIBUTE_LOCATION_TYPE => null,
        self::ATTRIBUTE_CATEGORIES    => [],
    ];

    private array $relationships = [
        self::RELATIONSHIP_CARRIER => [
            'data' => null,
        ],
    ];

    private array $meta = [
        self::META_DISTANCE => null,
    ];

    public function setCode(string $code): self
    {
        $this->attributes[self::ATTRIBUTE_CODE] = $code;

        return $this;
    }

    public function getCode(): string
    {
        return $this->attributes[self::ATTRIBUTE_CODE];
    }

    public function setAddress(AddressInterface $address): self
    {
        $this->attributes[self::ATTRIBUTE_ADDRESS] = $address;

        return $this;
    }

    public function getAddress(): AddressInterface
    {
        return $this->attributes[self::ATTRIBUTE_ADDRESS];
    }

    public function setOpeningHours(array $openingHours): self
    {
        $this->attributes[self::ATTRIBUTE_OPENING_HOURS] = [];

        array_walk($openingHours, function ($openingHour) {
            $this->addOpeningHour($openingHour);
        });

        return $this;
    }

    public function addOpeningHour(OpeningHourInterface $openingHour): self
    {
        $this->attributes[self::ATTRIBUTE_OPENING_HOURS][] = $openingHour;

        return $this;
    }

    public function getOpeningHours(): array
    {
        return $this->attributes[self::ATTRIBUTE_OPENING_HOURS];
    }

    public function setPosition(PositionInterface $position): self
    {
        $this->attributes[self::ATTRIBUTE_POSITION] = $position;

        return $this;
    }

    public function getPosition(): PositionInterface
    {
        return $this->attributes[self::ATTRIBUTE_POSITION];
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

    public function setDistance(float|int $distance, string $unit = DistanceUtils::UNIT_METER): self
    {
        $this->meta[self::META_DISTANCE] = round(DistanceUtils::convertDistance($distance, $unit, DistanceUtils::UNIT_METER));

        return $this;
    }

    public function getDistance($unit = DistanceUtils::UNIT_METER): float|int|null
    {
        if ($this->meta[self::META_DISTANCE] === null) {
            return null;
        }

        return round(DistanceUtils::convertDistance($this->meta[self::META_DISTANCE], DistanceUtils::UNIT_METER, $unit));
    }

    public function setCategories(array $categories): self
    {
        $this->attributes[self::ATTRIBUTE_CATEGORIES] = $categories;

        return $this;
    }

    public function getCategories(): array
    {
        return $this->attributes[self::ATTRIBUTE_CATEGORIES];
    }

    public function setLocationType(string $type): self
    {
        $this->attributes[self::ATTRIBUTE_LOCATION_TYPE] = $type;

        return $this;
    }

    public function getLocationType(): string
    {
        return $this->attributes[self::ATTRIBUTE_LOCATION_TYPE];
    }
}
