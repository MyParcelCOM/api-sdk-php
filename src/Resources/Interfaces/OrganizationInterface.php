<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Interfaces;

interface OrganizationInterface extends ResourceInterface
{
    public function setName(string $name): self;

    public function getName(): ?string;
}
