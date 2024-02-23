<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources;

use MyParcelCom\ApiSdk\Resources\Interfaces\BrokerInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;

class Broker implements BrokerInterface
{
    use JsonSerializable;
    use Resource;

    private ?string $id = null;
    private string $type = ResourceInterface::TYPE_BROKER;
}
