<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Interfaces;

use MyParcelCom\ApiSdk\Collection\ArrayCollection;

interface CollectionInterface extends ResourceInterface
{
    public function getDescription(): ?string;

    public function setDescription(?string $description): self;

    public function getAddress(): ?AddressInterface;

    public function setAddress(AddressInterface $address): self;

    public function getRegister(): ?bool;

    public function setRegister(?bool $register): self;

    public function getCollectionTime(): ?CollectionTimeInterface;

    public function setCollectionTime(CollectionTimeInterface $collectionTime): self;

    public function getTrackingCode(): ?string;

    public function setTrackingCode(?string $trackingCode): self;

    public function getContract(): ?ContractInterface;

    public function setContract(?ContractInterface $contract): self;

    public function getFiles(): ArrayCollection|array;

    public function setFiles(ArrayCollection|array $files): self;

    public function addFile(FileInterface $file): self;

    public function getManifest(): ?ManifestInterface;

    public function setManifest(?ManifestInterface $manifest): self;

    public function getShipments(): ArrayCollection|array;

    public function setShipments(ArrayCollection|array $shipments): self;

    public function addShipment(ShipmentInterface $shipment): self;

    public function getShop(): ?ShopInterface;

    public function setShop(ShopInterface $shop): self;

    public function getStatus(): ?StatusInterface;

    public function setStatus(StatusInterface $status): self;
}
