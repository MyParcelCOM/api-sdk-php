<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Interfaces;

interface ShipmentSurchargeInterface extends ResourceInterface
{
    public function setName(?string $name): self;

    public function getName(): ?string;

    public function setDescription(?string $description): self;

    public function getDescription(): ?string;

    public function setFeeAmount(int $amount): self;

    public function getFeeAmount(): int;

    public function setFeeCurrency(string $currency): self;

    public function getFeeCurrency(): string;

    public function getShipment(): ?ShipmentInterface;

    public function setShipment(?ShipmentInterface $shipment): self;
}
