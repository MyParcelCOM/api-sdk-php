<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Proxy;

use DateTime;
use MyParcelCom\ApiSdk\Resources\Interfaces\AddressInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\OrganizationInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceProxyInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\ApiSdk\Resources\Shop;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\ProxiesResource;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;

/**
 * @method Shop getResource()
 */
class ShopProxy implements ShopInterface, ResourceProxyInterface
{
    use JsonSerializable;
    use ProxiesResource;
    use Resource;

    private ?string $id = null;

    private string $type = ResourceInterface::TYPE_SHOP;

    public function setName(string $name): self
    {
        $this->getResource()->setName($name);

        return $this;
    }

    public function getName(): string
    {
        return $this->getResource()->getName();
    }

    public function setWebsite(?string $website): self
    {
        $this->getResource()->setWebsite($website);

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->getResource()->getWebsite();
    }

    public function setSenderAddress(AddressInterface $senderAddress): self
    {
        $this->getResource()->setSenderAddress($senderAddress);

        return $this;
    }

    public function getSenderAddress(): AddressInterface
    {
        return $this->getResource()->getSenderAddress();
    }

    public function setReturnAddress(AddressInterface $returnAddress): self
    {
        $this->getResource()->setReturnAddress($returnAddress);

        return $this;
    }

    public function getReturnAddress(): AddressInterface
    {
        return $this->getResource()->getReturnAddress();
    }

    public function setCreatedAt(DateTime|int $createdAt): self
    {
        $this->getResource()->setCreatedAt($createdAt);

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->getResource()->getCreatedAt();
    }

    public function setOrganization(OrganizationInterface $organization): self
    {
        $this->getResource()->setOrganization($organization);

        return $this;
    }

    public function getOrganization(): OrganizationInterface
    {
        return $this->getResource()->getOrganization();
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
