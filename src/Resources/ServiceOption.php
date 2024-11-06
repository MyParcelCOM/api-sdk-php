<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources;

use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceOptionInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;

class ServiceOption implements ServiceOptionInterface
{
    use JsonSerializable;
    use Resource;

    const ATTRIBUTE_NAME = 'name';
    const ATTRIBUTE_CODE = 'code';
    const ATTRIBUTE_CATEGORY = 'category';
    const ATTRIBUTE_VALUES_FORMAT = 'values_format';

    const META_PRICE = 'price';
    const META_PRICE_AMOUNT = 'amount';
    const META_PRICE_CURRENCY = 'currency';
    const META_INCLUDED = 'included';
    const META_VALUES = 'values';

    private ?string $id = null;

    private string $type = ResourceInterface::TYPE_SERVICE_OPTION;

    private array $attributes = [
        self::ATTRIBUTE_NAME          => null,
        self::ATTRIBUTE_CODE          => null,
        self::ATTRIBUTE_CATEGORY      => null,
        self::ATTRIBUTE_VALUES_FORMAT => null,
    ];

    private array $meta = [
        // Relationships posted to the service-rates endpoints can have a price and indication if they are included.
        self::META_PRICE    => [
            self::META_PRICE_AMOUNT   => null,
            self::META_PRICE_CURRENCY => null,
        ],
        self::META_INCLUDED => null,
        // Relationships posted to the shipment endpoints can have values (according to the defined values_format).
        self::META_VALUES   => null,
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

    public function setCode(string $code): self
    {
        $this->attributes[self::ATTRIBUTE_CODE] = $code;

        return $this;
    }

    public function getCode(): string
    {
        return $this->attributes[self::ATTRIBUTE_CODE];
    }

    public function setCategory(?string $category): self
    {
        $this->attributes[self::ATTRIBUTE_CATEGORY] = $category;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->attributes[self::ATTRIBUTE_CATEGORY];
    }

    public function setValuesFormat(?array $valuesFormat): self
    {
        $this->attributes[self::ATTRIBUTE_VALUES_FORMAT] = $valuesFormat;

        return $this;
    }

    public function getValuesFormat(): ?array
    {
        return $this->attributes[self::ATTRIBUTE_VALUES_FORMAT];
    }

    public function setPrice(?int $price): self
    {
        $this->meta[self::META_PRICE][self::META_PRICE_AMOUNT] = $price;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->meta[self::META_PRICE][self::META_PRICE_AMOUNT];
    }

    public function setCurrency(?string $currency): self
    {
        $this->meta[self::META_PRICE][self::META_PRICE_CURRENCY] = $currency;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->meta[self::META_PRICE][self::META_PRICE_CURRENCY];
    }

    public function setIncluded(bool $included): self
    {
        $this->meta[self::META_INCLUDED] = $included;

        return $this;
    }

    public function isIncluded(): bool
    {
        return (bool) $this->meta[self::META_INCLUDED];
    }

    public function setValues(?array $values): self
    {
        $this->meta[self::META_VALUES] = $values;

        return $this;
    }

    public function getValues(): ?array
    {
        return $this->meta[self::META_VALUES];
    }
}
