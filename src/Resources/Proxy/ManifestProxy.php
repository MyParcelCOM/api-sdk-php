<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Proxy;

use MyParcelCom\ApiSdk\Collection\ArrayCollection;
use MyParcelCom\ApiSdk\Resources\Interfaces\AddressInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\BrokerInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\FileInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\OrganizationInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\ApiSdk\Resources\Manifest;
use MyParcelCom\ApiSdk\Resources\Interfaces\ManifestInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceProxyInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\ProxiesResource;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;

/**
 * @method Manifest getResource()
 */
class ManifestProxy implements ManifestInterface, ResourceProxyInterface
{
    use JsonSerializable;
    use ProxiesResource;
    use Resource;

    private ?string $id = null;

    private string $type = ResourceInterface::TYPE_MANIFEST;


    public function getAddress(): ?AddressInterface
    {
        return $this->getResource()->getAddress();
    }

    public function setAddress(AddressInterface $address): self
    {
        $this->getResource()->setAddress($address);

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

    public function getFiles(): ArrayCollection|array
    {
        return $this->getResource()->getFiles();
    }

    public function setName(string $name): self
    {
        $this->getResource()->setName($name);

        return $this;
    }

    public function getName(): string
    {
        return $this->getResource()->getName();
    }

    public function setOwner(ShopInterface|BrokerInterface|OrganizationInterface $owner): self
    {
        $this->getResource()->setOwner($owner);

        return $this;
    }

    public function getOwner(): ShopInterface|BrokerInterface|OrganizationInterface|null
    {
        return $this->getResource()->getOwner();
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

    public function getShipments(): ArrayCollection|array
    {
        return $this->getResource()->getShipments();
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
