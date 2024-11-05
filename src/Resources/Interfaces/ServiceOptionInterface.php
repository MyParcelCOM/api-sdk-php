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

    public function setPrice(?int $price): self;

    public function getPrice(): ?int;

    public function setCurrency(?string $currency): self;

    public function getCurrency(): ?string;

    public function setIncluded(bool $included): self;

    public function isIncluded(): bool;

    public function setValues(?array $values): self;

    public function getValues(): ?array;
}
