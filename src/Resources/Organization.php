<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources;

use MyParcelCom\ApiSdk\Resources\Interfaces\OrganizationInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;

class Organization implements OrganizationInterface
{
    use JsonSerializable;
    use Resource;

    const ATTRIBUTE_NAME = 'name';

    private ?string $id = null;

    private string $type = ResourceInterface::TYPE_ORGANIZATION;

    private array $attributes = [
        self::ATTRIBUTE_NAME => null,
    ];

    public function setName(string $name): self
    {
        $this->attributes[self::ATTRIBUTE_NAME] = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->attributes[self::ATTRIBUTE_NAME];
    }
}
