<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Proxy;

use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceProxyInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceOptionInterface;
use MyParcelCom\ApiSdk\Resources\ServiceOption;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\ProxiesResource;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;

/**
 * @method ServiceOption getResource()
 */
class ServiceOptionProxy implements ServiceOptionInterface, ResourceProxyInterface
{
    use JsonSerializable;
    use ProxiesResource;
    use Resource;

    const META_PRICE = 'price';
    const META_PRICE_AMOUNT = 'amount';
    const META_PRICE_CURRENCY = 'currency';
    const META_INCLUDED = 'included';
    const META_VALUES = 'values';

    private ?string $id = null;

    private string $type = ResourceInterface::TYPE_SERVICE_OPTION;

    private array $meta = [
        // Relationships returned from the service-rates endpoints can have a price and indication if they are included.
        self::META_PRICE    => [
            self::META_PRICE_AMOUNT   => null,
            self::META_PRICE_CURRENCY => null,
        ],
        self::META_INCLUDED => null,
        // Relationships returned from the shipment endpoints can have values (according to the defined values_format).
        self::META_VALUES => null,
    ];

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

    public function setCategory(?string $category): self
    {
        $this->getResource()->setCategory($category);

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->getResource()->getCategory();
    }

    public function setValuesFormat(?array $valuesFormat): self
    {
        $this->getResource()->setValuesFormat($valuesFormat);

        return $this;
    }

    public function getValuesFormat(): ?array
    {
        return $this->getResource()->getValuesFormat();
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

    /**
     * This function puts all object properties in an array and returns it.
     */
    public function jsonSerialize(): array
    {
        $values = get_object_vars($this);
        unset($values['resource']);
        unset($values['api']);
        unset($values['uri']);

        $json = $this->arrayValuesToArray($values);

        if (isset($json['meta']) && $this->isEmpty($json['meta'])) {
            unset($json['meta']);
        }

        return $json;
    }
}
