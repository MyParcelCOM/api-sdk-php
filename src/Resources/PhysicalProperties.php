<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources;

use MyParcelCom\ApiSdk\Exceptions\MyParcelComException;
use MyParcelCom\ApiSdk\Resources\Interfaces\PhysicalPropertiesInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;

class PhysicalProperties implements PhysicalPropertiesInterface
{
    use JsonSerializable;

    private ?int $weight = null;

    private ?int $length = null;

    private float|int|null $volume = null;

    private ?int $height = null;

    private ?int $width = null;

    private ?int $volumetricWeight = null;

    public static array $unitConversion = [
        self::WEIGHT_GRAM     => 1,
        self::WEIGHT_KILOGRAM => 1000,
        self::WEIGHT_OUNCE    => 28.349523125,
        self::WEIGHT_POUND    => 453.59237,
        self::WEIGHT_STONE    => 6350.29318,
    ];

    public function setWidth(?int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setHeight(?int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setLength(?int $length): self
    {
        $this->length = $length;

        return $this;
    }

    public function getLength(): ?int
    {
        return $this->length;
    }

    public function setWeight($weight, $unit = self::WEIGHT_GRAM): self
    {
        if (!isset(self::$unitConversion[$unit])) {
            throw new MyParcelComException('invalid unit: ' . $unit);
        }

        $this->weight = (int) round($weight * self::$unitConversion[$unit]);

        return $this;
    }

    public function getWeight(string $unit = self::WEIGHT_GRAM): ?int
    {
        if ($this->weight === null) {
            return $this->weight;
        }

        if (!isset(self::$unitConversion[$unit])) {
            throw new MyParcelComException('invalid unit: ' . $unit);
        }

        return (int) round($this->weight / self::$unitConversion[$unit]);
    }

    public function setVolume(float|int|null $volume): self
    {
        $this->volume = $volume;

        return $this;
    }

    public function getVolume(): float|int|null
    {
        return $this->volume;
    }

    public function getVolumetricWeight(): ?int
    {
        return $this->volumetricWeight;
    }

    public function setVolumetricWeight(?int $volumetricWeight): self
    {
        $this->volumetricWeight = $volumetricWeight;

        return $this;
    }

    /**
     * We do not send the volumetric_weight to the API when saving a shipment, since the API calculates this internally.
     */
    public function jsonSerialize(): array
    {
        $values = get_object_vars($this);
        unset($values['volumetricWeight']);

        $json = $this->arrayValuesToArray($values);

        foreach ($json as $property => $value) {
            if ($this->isEmpty($value)) {
                unset($json[$property]);
            }
        }

        return $json;
    }
}
