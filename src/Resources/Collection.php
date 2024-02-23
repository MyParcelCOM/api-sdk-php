<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources;

use MyParcelCom\ApiSdk\Collection\ArrayCollection;
use MyParcelCom\ApiSdk\Resources\Interfaces\AddressInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\CollectionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\CollectionTimeInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\FileInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ManifestInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\StatusInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;

class Collection implements CollectionInterface
{
    use JsonSerializable;
    use Resource;

    private ?string $id = null;
    private string $type = ResourceInterface::TYPE_COLLECTION;

    private array $attributes = [
        'description'     => null,
        'address'         => null,
        'register'      => null,
        'collection_time' => null,
        'tracking_code'   => null,
    ];

    private array $relationships = [
        'contract'  => [
            'data' => null,
        ],
        'files'     => [
            'data' => [],
        ],
        'manifest'  => [
            'data' => null,
        ],
        'shipments' => [
            'data' => [],
        ],
        'shop'      => [
            'data' => null,
        ],
        'status'    => [
            'data' => null,
        ],
    ];

    public function getDescription(): ?string
    {
        return $this->attributes['description'];
    }

    public function setDescription(?string $description): self
    {
        $this->attributes['description'] = $description;

        return $this;
    }

    public function getAddress(): ?AddressInterface
    {
        return $this->attributes['address'];
    }

    public function setAddress(AddressInterface $address): self
    {
        $this->attributes['address'] = $address;

        return $this;
    }

    public function getRegister(): ?bool
    {
        return $this->attributes['register'];
    }

    public function setRegister(?bool $register): self
    {
        $this->attributes['register'] = $register;

        return $this;
    }

    public function getCollectionTime(): ?CollectionTimeInterface
    {
        return $this->attributes['collection_time'];
    }

    public function setCollectionTime(CollectionTimeInterface $collectionTime): self
    {
        $this->attributes['collection_time'] = $collectionTime;

        return $this;
    }

    public function getTrackingCode(): ?string
    {
        return $this->attributes['tracking_code'];
    }

    public function setTrackingCode(?string $trackingCode): self
    {
        $this->attributes['tracking_code'] = $trackingCode;

        return $this;
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

    public function getFiles(): ArrayCollection|array
    {
        return $this->relationships['files']['data'];
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

    public function getManifest(): ?ManifestInterface
    {
        return $this->relationships['manifest']['data'];
    }

    public function setManifest(?ManifestInterface $manifest): self
    {
        $this->relationships['manifest']['data'] = $manifest;

        return $this;
    }

    public function getShipments(): ArrayCollection|array
    {
        return $this->relationships['shipments']['data'];
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

    public function getShop(): ?ShopInterface
    {
        return $this->relationships['shop']['data'];
    }

    public function setShop(ShopInterface $shop): self
    {
        $this->relationships['shop']['data'] = $shop;

        return $this;
    }

    public function getStatus(): ?StatusInterface
    {
        return $this->relationships['status']['data'];
    }

    public function setStatus(StatusInterface $status): self
    {
        $this->relationships['status']['data'] = $status;

        return $this;
    }
}
