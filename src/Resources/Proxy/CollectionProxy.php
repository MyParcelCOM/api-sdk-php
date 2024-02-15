<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Proxy;

use MyParcelCom\ApiSdk\Collection\ArrayCollection;
use MyParcelCom\ApiSdk\Resources\Collection;
use MyParcelCom\ApiSdk\Resources\Interfaces\AddressInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\CollectionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\CollectionTimeInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\FileInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ManifestInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceProxyInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\StatusInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\ProxiesResource;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;

/**
 * @method Collection getResource()
 */
class CollectionProxy implements CollectionInterface, ResourceProxyInterface
{
    use JsonSerializable;
    use ProxiesResource;
    use Resource;

    private ?string $id = null;

    private string $type = ResourceInterface::TYPE_COLLECTION;

    public function getDescription(): ?string
    {
        return $this->getResource()->getDescription();
    }

    public function setDescription(?string $description): self
    {
        $this->getResource()->setDescription($description);

        return $this;
    }

    public function getAddress(): ?AddressInterface
    {
        return $this->getResource()->getAddress();
    }

    public function setAddress(AddressInterface $address): self
    {
        $this->getResource()->setAddress($address);

        return $this;
    }

    public function getRegister(): ?bool
    {
        return $this->getResource()->getRegister();
    }

    public function setRegister(?bool $register): self
    {
        $this->getResource()->setRegister($register);

        return $this;
    }

    public function getCollectionTime(): ?CollectionTimeInterface
    {
        return $this->getResource()->getCollectionTime();
    }

    public function setCollectionTime(CollectionTimeInterface $collectionTime): self
    {
        $this->getResource()->setCollectionTime($collectionTime);

        return $this;
    }

    public function getTrackingCode(): ?string
    {
        return $this->getResource()->getTrackingCode();
    }

    public function setTrackingCode(?string $trackingCode): self
    {
        $this->getResource()->setTrackingCode($trackingCode);

        return $this;
    }

    public function getContract(): ?ContractInterface
    {
        return $this->getResource()->getContract();
    }

    public function setContract(?ContractInterface $contract): self
    {
        $this->getResource()->setContract($contract);

        return $this;
    }

    public function getFiles(): ArrayCollection|array
    {
        return $this->getResource()->getFiles();
    }

    public function setFiles(ArrayCollection|array $files): self
    {
        $this->getResource()->setFiles($files);

        return $this;
    }

    public function addFile(FileInterface $file): self
    {
        $this->getResource()->addFile($file);

        return $this;
    }

    public function getManifest(): ?ManifestInterface
    {
        return $this->getResource()->getManifest();
    }

    public function setManifest(?ManifestInterface $manifest): self
    {
        $this->getResource()->setManifest($manifest);

        return $this;
    }

    public function getShipments(): ArrayCollection|array
    {
        return $this->getResource()->getShipments();
    }

    public function setShipments(ArrayCollection|array $shipments): self
    {
        $this->getResource()->setShipments($shipments);

        return $this;
    }

    public function addShipment(ShipmentInterface $shipment): self
    {
        $this->getResource()->addShipment($shipment);

        return $this;
    }

    public function getShop(): ?ShopInterface
    {
        return $this->getResource()->getShop();
    }

    public function setShop(ShopInterface $shop): self
    {
        $this->getResource()->setShop($shop);

        return $this;
    }

    public function getStatus(): ?StatusInterface
    {
        return $this->getResource()->getStatus();
    }

    public function setStatus(StatusInterface $status): self
    {
        $this->getResource()->setStatus($status);

        return $this;
    }

    /**
     * This function puts all object properties in an array and returns it.
     */
    public function jsonSerialize(): array
    {
        $values = get_object_vars($this);
        unset($values['resource']);
        unset($values['api']);
        unset($values['uri']);

        return $this->arrayValuesToArray($values);
    }
}
