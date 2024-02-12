<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Interfaces;

use MyParcelCom\ApiSdk\Collection\ArrayCollection;
use MyParcelCom\ApiSdk\Resources\File;
use MyParcelCom\ApiSdk\Resources\Shipment;

interface ManifestInterface extends ResourceInterface
{
    public function getAddress(): ?AddressInterface;

    public function setAddress(AddressInterface $address): self;

    public function getContract(): ?ContractInterface;

    public function setContract(?ContractInterface $contract): self;

    /** @param ArrayCollection<File>|array $files Array of ids, or a File ArrayCollection or a File model. */
    public function setFiles(ArrayCollection|array $files): self;

    public function addFile(FileInterface $file): self;

    public function getFiles(): ArrayCollection|array;

    public function setName(string $name): self;

    public function getName(): ?string;

    public function setOwner(ShopInterface|BrokerInterface|OrganizationInterface $owner): self;

    public function getOwner(): ShopInterface|BrokerInterface|OrganizationInterface|null;

    /** @param ArrayCollection<Shipment>|array $shipments Array of ids, or a Shipment ArrayCollection or a Shipment model. */
    public function setShipments(ArrayCollection|array $shipments): self;

    public function addShipment(ShipmentInterface $shipment): self;

    public function getShipments(): ArrayCollection|array;

    public function setUpdatesShipmentStatuses(bool $updatesShipmentStatuses): self;

    public function getUpdatesShipmentStatuses(): ?bool;
}
