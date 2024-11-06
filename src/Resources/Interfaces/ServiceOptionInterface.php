<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Interfaces;

interface ServiceOptionInterface extends ResourceInterface
{
    public function setName(string $name): self;

    public function getName(): string;

    public function setCode(string $code): self;

    public function getCode(): string;

    public function setCategory(?string $category): self;

    public function getCategory(): ?string;

    public function setValuesFormat(?array $valuesFormat): self;

    /**
     * Defines if this ServiceOption requires a `values` array to be set when using it to create a shipment.
     */
    public function getValuesFormat(): ?array;

    public function setPrice(?int $price): self;

    public function getPrice(): ?int;

    public function setCurrency(?string $currency): self;

    public function getCurrency(): ?string;

    public function setIncluded(bool $included): self;

    public function isIncluded(): bool;

    /**
     * When adding a ServiceOption to a Shipment, it might require values such as an `amount` and a `currency`.
     * This is defined in the `values_format` of the ServiceOption, which returns these requirements as JSON Schema.
     */
    public function setValues(?array $values): self;

    public function getValues(): ?array;
}
