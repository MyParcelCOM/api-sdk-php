<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources;

use MyParcelCom\ApiSdk\Collection\ArrayCollection;
use MyParcelCom\ApiSdk\Resources\Interfaces\AddressInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\BrokerInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\FileInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ManifestInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\OrganizationInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\ProcessIncludes;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;

class Manifest implements ManifestInterface
{
    use JsonSerializable;
    use ProcessIncludes;
    use Resource;

    const INCLUDES = [
        ResourceInterface::TYPE_CONTRACT => 'contract',
    ];

    private ?string $id = null;
    private string $type = ResourceInterface::TYPE_MANIFEST;
    private array $attributes = [
        'address' => null,
        'name'    => null,
    ];

    private array $relationships = [
        'contract'  => [
            'data' => null,
        ],
        'owner'     => [
            'data' => null,
        ],
        'shipments' => [
            'data' => [],
        ],
        'files'     => [
            'data' => [],
        ],
    ];

    private array $meta = [
        'update_shipment_statuses' => null,
    ];

    public function setAddress(AddressInterface $address): self
    {
        $this->attributes['address'] = $address;

        return $this;
    }

    public function getAddress(): ?AddressInterface
    {
        return $this->attributes['address'];
    }

    public function getContract(): ?ContractInterface
    {
        return $this->relationships['contract']['data'];
    }

    public function setContract(?ContractInterface $contract): self
    {
        $this->relationships['contract']['data'] = $contract;

        return $this;
    }

    public function setFiles(ArrayCollection|array $files): self
    {
        $this->relationships['files']['data'] = $files;

        return $this;
    }

    public function addFile(FileInterface $file): self
    {
        $this->relationships['files']['data'][] = $file;

        return $this;
    }

    public function getFiles(): ArrayCollection|array
    {
        return $this->relationships['files']['data'];
    }

    public function setName(string $name): self
    {
        $this->attributes['name'] = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->attributes['name'];
    }

    public function setShipments(ArrayCollection|array $shipments): self
    {
        $this->relationships['shipments']['data'] = $shipments;

        return $this;
    }

    public function addShipment(ShipmentInterface $shipment): self
    {
        $this->relationships['shipments']['data'][] = $shipment;

        return $this;
    }

    public function getShipments(): ArrayCollection|array
    {
        return $this->relationships['shipments']['data'];
    }

    public function setOwner(ShopInterface|BrokerInterface|OrganizationInterface $owner): self
    {
        $this->relationships['owner']['data'] = $owner;

        return $this;
    }

    public function getOwner(): ShopInterface|BrokerInterface|OrganizationInterface|null
    {
        return $this->relationships['owner']['data'];
    }

    public function setUpdatesShipmentStatuses(bool $updatesShipmentStatuses): self
    {
        $this->meta['update_shipment_statuses'] = $updatesShipmentStatuses;

        return $this;
    }

    public function getUpdatesShipmentStatuses(): ?bool
    {
        return $this->meta['update_shipment_statuses'];
    }
}
