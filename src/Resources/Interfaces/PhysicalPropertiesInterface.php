<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Interfaces;

use JsonSerializable;

interface PhysicalPropertiesInterface extends JsonSerializable
{
    const WEIGHT_GRAM = 'grams';
    const WEIGHT_KILOGRAM = 'kilograms';
    const WEIGHT_POUND = 'pounds';
    const WEIGHT_OUNCE = 'ounces';
    const WEIGHT_STONE = 'stones';

    public function setWidth(?int $width): self;

    public function getWidth(): ?int;

    public function setHeight(?int $height): self;

    public function getHeight(): ?int;

    public function setLength(?int $length): self;

    public function getLength(): ?int;

    public function setWeight(int $weight, string $unit = self::WEIGHT_GRAM): self;

    public function getWeight(string $unit = self::WEIGHT_GRAM): ?int;

    public function setVolume(float|int|null $volume): self;

    public function getVolume(): float|int|null;

    /**
     * @deprecated Your code should not rely on this function.
     * Do not calculate your own volumetric weight. This function is needed to populate the value received from our API.
     */
    public function setVolumetricWeight(?int $volumetricWeight): self;

    /**
     * Returns the volumetric weight if calculated by our API (after saving the shipment).
     */
    public function getVolumetricWeight(): ?int;
}
