<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Interfaces;

interface ContractInterface extends ResourceInterface
{
    public function setName(string $name): self;

    public function getName(): string;

    public function setCurrency(string $currency): self;

    public function getCurrency(): string;

    public function setCarrier(CarrierInterface $carrier): self;

    public function getCarrier(): CarrierInterface;

    public function setStatus(string $status): self;

    public function getStatus(): string;

    public function setVolumetricWeightDivisorFactor(float $factor): self;

    public function getVolumetricWeightDivisorFactor(): float;
}
