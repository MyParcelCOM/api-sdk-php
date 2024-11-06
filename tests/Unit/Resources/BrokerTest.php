<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Unit\Resources;

use MyParcelCom\ApiSdk\Resources\Broker;
use PHPUnit\Framework\TestCase;

class BrokerTest extends TestCase
{
    public function testId()
    {
        $broker = new Broker();

        $this->assertNull($broker->getId());

        $this->assertEquals('broker-id', $broker->setId('broker-id')->getId());
    }

    public function testType()
    {
        $broker = new Broker();

        $this->assertEquals('brokers', $broker->getType());
    }

    public function testJsonSerialize()
    {
        $broker = (new Broker())->setId('broker-id');

        $this->assertEquals([
            'id'   => 'broker-id',
            'type' => 'brokers',
        ], $broker->jsonSerialize());
    }
}
