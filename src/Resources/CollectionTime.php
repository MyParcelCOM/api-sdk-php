<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources;

use MyParcelCom\ApiSdk\Resources\Interfaces\CollectionTimeInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;

class CollectionTime implements CollectionTimeInterface
{
    use JsonSerializable;

    private string|int|null $from = null;
    private string|int|null $to = null;

    public function getFrom(): string|int|null
    {
        return $this->from;
    }

    public function setFrom(int|string $from): self
    {
        $this->from = $from;

        return $this;
    }

    public function getTo(): string|int|null
    {
        return $this->to;
    }

    public function setTo(int|string $to): self
    {
        $this->to = $to;

        return $this;
    }
}
