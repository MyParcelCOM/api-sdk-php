<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources;

use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentSurchargeInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;

class ShipmentSurcharge implements ShipmentSurchargeInterface
{
    use JsonSerializable;
    use Resource;

    const AMOUNT = 'amount';
    const CURRENCY = 'currency';

    private ?string $name = null;

    private ?string $description;

    private array $fee = [
        self::AMOUNT   => null,
        self::CURRENCY => null,
    ];

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setFeeAmount(int $amount): self
    {
        $this->fee[self::AMOUNT] = $amount;

        return $this;
    }

    public function getFeeAmount(): int
    {
        return $this->fee[self::AMOUNT];
    }

    public function setFeeCurrency(string $currency): self
    {
        $this->fee[self::CURRENCY] = $currency;

        return $this;
    }

    public function getFeeCurrency(): string
    {
        return $this->fee[self::CURRENCY];
    }
}
