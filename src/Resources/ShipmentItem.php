<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources;

use MyParcelCom\ApiSdk\Enums\WeightUnitEnum;
use MyParcelCom\ApiSdk\Exceptions\MyParcelComException;
use MyParcelCom\ApiSdk\Helpers\WeightConverter;
use MyParcelCom\ApiSdk\Resources\Interfaces\PhysicalPropertiesInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentItemInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;

class ShipmentItem implements ShipmentItemInterface
{
    use JsonSerializable;

    const AMOUNT = 'amount';
    const CURRENCY = 'currency';

    private ?string $sku = null;

    private string $description;

    private ?string $imageUrl = null;

    private ?string $hsCode = null;

    private int $quantity;

    private array $itemValue = [
        self::AMOUNT   => null,
        self::CURRENCY => null,
    ];

    private ?int $vatPercentage = null;

    private ?string $originCountryCode = null;

    private ?int $itemWeight = null;

    private string $itemWeightUnit = WeightUnitEnum::GRAM;

    private bool $isPreferentialOrigin = false;

    public function setSku(?string $sku): self
    {
        $this->sku = $sku;

        return $this;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setHsCode(?string $hsCode): self
    {
        $this->hsCode = $hsCode;

        return $this;
    }

    public function getHsCode(): ?string
    {
        return $this->hsCode;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = (int) $quantity;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setItemValue(?int $value): self
    {
        $this->itemValue[self::AMOUNT] = (int) $value;

        return $this;
    }

    public function getItemValue(): ?int
    {
        return $this->itemValue[self::AMOUNT];
    }

    public function setCurrency(?string $currency): self
    {
        $this->itemValue[self::CURRENCY] = $currency;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->itemValue[self::CURRENCY];
    }

    /**
     * @param int|null $percentage Value between 0 and 100
     */
    public function setVatPercentage(?int $percentage): self
    {
        if (is_int($percentage) && ($percentage < 0 || $percentage > 100)) {
            throw new MyParcelComException('Invalid percentage: ' . $percentage . ' should be between 0 and 100.');
        }

        $this->vatPercentage = $percentage;

        return $this;
    }

    public function getVatPercentage(): ?int
    {
        return $this->vatPercentage;
    }

    public function setOriginCountryCode(?string $countryCode): self
    {
        $this->originCountryCode = $countryCode;

        return $this;
    }

    public function getOriginCountryCode(): ?string
    {
        return $this->originCountryCode;
    }

    /**
     * This method can also set the weight unit, so you do not have to call setItemWeightUnit() separately.
     */
    public function setItemWeight(?int $weight, ?string $unit = null): self
    {
        // Because we supported unit conversion in the SDK before we did this in the API, `grams` and `g` are supported.
        switch ($unit) {
            case null:
                // We do not want this function to alter $this->itemWeightUnit when it's called without a parameter.
                // In that case `grams` are implied, which is already the default item weight unit set on this class.
                break;
            case WeightUnitEnum::MILLIGRAM:
                $this->itemWeightUnit = WeightUnitEnum::MILLIGRAM;
                break;
            case PhysicalPropertiesInterface::WEIGHT_GRAM:
            case WeightUnitEnum::GRAM:
                $this->itemWeightUnit = WeightUnitEnum::GRAM;
                break;
            case PhysicalPropertiesInterface::WEIGHT_KILOGRAM:
            case WeightUnitEnum::KILOGRAM:
                $this->itemWeightUnit = WeightUnitEnum::KILOGRAM;
                break;
            case PhysicalPropertiesInterface::WEIGHT_OUNCE:
            case WeightUnitEnum::OUNCE:
                $this->itemWeightUnit = WeightUnitEnum::OUNCE;
                break;
            case PhysicalPropertiesInterface::WEIGHT_POUND:
            case WeightUnitEnum::POUND:
                $this->itemWeightUnit = WeightUnitEnum::POUND;
                break;
            case PhysicalPropertiesInterface::WEIGHT_STONE:
                // Our API does not support stones. If anyone is using stones, we will work with the old implementation.
                $this->itemWeight = (int) ceil($weight * PhysicalProperties::$unitConversion[$unit]);
                return $this;
            default:
                throw new MyParcelComException('invalid unit: ' . $unit);
        }

        $this->itemWeight = $weight === null ? $weight : (int) ceil($weight);

        return $this;
    }

    public function getItemWeight(?string $unit = null): ?int
    {
        if ($this->itemWeight === null) {
            return $this->itemWeight;
        }

        // Convert old PhysicalPropertiesInterface units to the new WeightUnitEnum used by our API and WeightConverter.
        switch ($unit) {
            case null:
                // We use the default item weight unit set on this class if this function is called without a parameter.
                $unit = $this->itemWeightUnit;
                break;
            case WeightUnitEnum::MILLIGRAM:
                $unit = WeightUnitEnum::MILLIGRAM;
                break;
            case PhysicalPropertiesInterface::WEIGHT_GRAM:
            case WeightUnitEnum::GRAM:
                $unit = WeightUnitEnum::GRAM;
                break;
            case PhysicalPropertiesInterface::WEIGHT_KILOGRAM:
            case WeightUnitEnum::KILOGRAM:
                $unit = WeightUnitEnum::KILOGRAM;
                break;
            case PhysicalPropertiesInterface::WEIGHT_OUNCE:
            case WeightUnitEnum::OUNCE:
                $unit = WeightUnitEnum::OUNCE;
                break;
            case PhysicalPropertiesInterface::WEIGHT_POUND:
            case WeightUnitEnum::POUND:
                $unit = WeightUnitEnum::POUND;
                break;
            case PhysicalPropertiesInterface::WEIGHT_STONE:
                // Our API does not support stones. If anyone is using stones, we will work with the old implementation.
                return (int) ceil($this->itemWeight / PhysicalProperties::$unitConversion[$unit]);
            default:
                throw new MyParcelComException('invalid unit: ' . $unit);
        }

        $convertedWeight = WeightConverter::convert(
            $this->itemWeight,
            $this->itemWeightUnit,
            $unit
        );

        return (int) ceil($convertedWeight);
    }

    public function setItemWeightUnit(string $weightUnit): self
    {
        if (!WeightUnitEnum::isValid($weightUnit)) {
            throw new MyParcelComException('$weightUnit should be one of: ' . implode(', ', WeightUnitEnum::toArray()));
        }

        $this->itemWeightUnit = $weightUnit;

        return $this;
    }

    public function getItemWeightUnit(): string
    {
        return $this->itemWeightUnit;
    }

    public function setIsPreferentialOrigin(bool $isPreferentialOrigin): self
    {
        $this->isPreferentialOrigin = $isPreferentialOrigin;

        return $this;
    }

    public function getIsPreferentialOrigin(): bool
    {
        return $this->isPreferentialOrigin;
    }
}
