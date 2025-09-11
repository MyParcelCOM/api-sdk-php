<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources;

use DateTime;
use MyParcelCom\ApiSdk\Resources\Interfaces\AddressInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\OrganizationInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\ProcessIncludes;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;
use MyParcelCom\ApiSdk\Utils\DateUtils;

class Shop implements ShopInterface
{
    use JsonSerializable;
    use ProcessIncludes;
    use Resource;

    const ATTRIBUTE_NAME = 'name';
    const ATTRIBUTE_WEBSITE = 'website';
    const ATTRIBUTE_SENDER_ADDRESS = 'sender_address';
    const ATTRIBUTE_RETURN_ADDRESS = 'return_address';
    const ATTRIBUTE_CREATED_AT = 'created_at';

    const RELATIONSHIP_ORGANIZATION = 'organization';

    const INCLUDES = [
        ResourceInterface::TYPE_ORGANIZATION => self::RELATIONSHIP_ORGANIZATION,
    ];

    private ?string $id = null;

    private string $type = ResourceInterface::TYPE_SHOP;

    private array $attributes = [
        self::ATTRIBUTE_NAME           => null,
        self::ATTRIBUTE_WEBSITE        => null,
        self::ATTRIBUTE_SENDER_ADDRESS => null,
        self::ATTRIBUTE_RETURN_ADDRESS => null,
        self::ATTRIBUTE_CREATED_AT     => null,
    ];

    private array $relationships = [
        self::RELATIONSHIP_ORGANIZATION => [
            'data' => null,
        ],
    ];

    public function setName(string $name): self
    {
        $this->attributes[self::ATTRIBUTE_NAME] = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->attributes[self::ATTRIBUTE_NAME];
    }

    public function setWebsite(?string $website): self
    {
        $this->attributes[self::ATTRIBUTE_WEBSITE] = $website;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->attributes[self::ATTRIBUTE_WEBSITE];
    }

    public function setSenderAddress(AddressInterface $senderAddress): self
    {
        $this->attributes[self::ATTRIBUTE_SENDER_ADDRESS] = $senderAddress;

        return $this;
    }

    public function getSenderAddress(): AddressInterface
    {
        return $this->attributes[self::ATTRIBUTE_SENDER_ADDRESS];
    }

    public function setReturnAddress(AddressInterface $returnAddress): self
    {
        $this->attributes[self::ATTRIBUTE_RETURN_ADDRESS] = $returnAddress;

        return $this;
    }

    public function getReturnAddress(): AddressInterface
    {
        return $this->attributes[self::ATTRIBUTE_RETURN_ADDRESS];
    }

    public function setCreatedAt(DateTime|int $createdAt): self
    {
        $this->attributes[self::ATTRIBUTE_CREATED_AT] = DateUtils::toTimestamp($createdAt);

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return (new DateTime())->setTimestamp($this->attributes[self::ATTRIBUTE_CREATED_AT]);
    }

    public function setOrganization(OrganizationInterface $organization): self
    {
        $this->relationships[self::RELATIONSHIP_ORGANIZATION]['data'] = $organization;

        return $this;
    }

    public function getOrganization(): OrganizationInterface
    {
        return $this->relationships[self::RELATIONSHIP_ORGANIZATION]['data'];
    }
}
