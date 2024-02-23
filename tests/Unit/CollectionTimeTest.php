<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Unit;

use MyParcelCom\ApiSdk\Resources\CollectionTime;
use PHPUnit\Framework\TestCase;

class CollectionTimeTest extends TestCase
{
    public function testFrom(): void
    {
        $collectionTime = new CollectionTime();

        $this->assertNull($collectionTime->getFrom());
        $this->assertEquals(123456789, $collectionTime->setFrom(123456789)->getFrom());
        $this->assertEquals(
            '2023-01-01T00:00:00+00:00',
            $collectionTime->setFrom('2023-01-01T00:00:00+00:00')->getFrom()
        );
    }

    public function testTo(): void
    {
        $collectionTime = new CollectionTime();

        $this->assertNull($collectionTime->getTo());
        $this->assertEquals(123456789, $collectionTime->setTo(123456789)->getTo());
        $this->assertEquals(
            '2024-01-01T00:00:00+00:00',
            $collectionTime->setTo('2024-01-01T00:00:00+00:00')->getTo()
        );
    }

    /** @test */
    public function testJsonSerialize()
    {
        $collectionTime = (new CollectionTime())
            ->setFrom(12345678)
            ->setTo('2024-01-01T00:00:00+00:00');

        $this->assertEquals([
            'from' => 12345678,
            'to'   => '2024-01-01T00:00:00+00:00',
        ], $collectionTime->jsonSerialize());
    }
}
