<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Interfaces;

interface CarrierInterface extends ResourceInterface
{
    public function setName(string $name): self;

    public function getName(): string;

    public function setCode(string $code): self;

    public function getCode(): string;

    public function setCredentialsFormat(array $format): self;

    public function getCredentialsFormat(): array;

    public function setLabelMimeTypes(array $labelMimeTypes): self;

    public function getLabelMimeTypes(): array;

    public function setOffersCollections(bool $offersCollections): self;

    public function getOffersCollections(): bool;

    public function setVoidsRegisteredCollections(bool $voidsRegisteredCollections): self;

    public function getVoidsRegisteredCollections(): bool;

    public function setAllowsAddingRegisteredShipmentsToCollection(bool $allowsAddingRegisteredShipments): self;

    public function getAllowsAddingRegisteredShipmentsToCollection(): bool;
}
