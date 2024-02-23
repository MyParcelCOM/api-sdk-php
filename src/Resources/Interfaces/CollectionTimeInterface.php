<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Interfaces;

use JsonSerializable;

interface CollectionTimeInterface extends JsonSerializable
{
    /**
     * @return string|int|null Either a unix timestamp or ISO 8601 formatted datetime string.
     */
    public function getFrom(): string|int|null;

    /**
     * @param string|int $from Either a unix timestamp or ISO 8601 formatted datetime string.
     */
    public function setFrom(string|int $from): self;

    /**
     * @return string|int|null Either a unix timestamp or ISO 8601 formatted datetime string.
     */
    public function getTo(): string|int|null;

    /**
     * @param string|int $to Either a unix timestamp or ISO 8601 formatted datetime string.
     */
    public function setTo(string|int $to): self;
}
