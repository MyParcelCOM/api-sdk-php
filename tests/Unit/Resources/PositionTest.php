<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Unit\Resources;

use MyParcelCom\ApiSdk\Resources\Position;
use PHPUnit\Framework\TestCase;

class PositionTest extends TestCase
{
    /** @test */
    public function testLatitude()
    {
        $position = new Position();
        $this->assertEquals(2.56784948, $position->setLatitude(2.56784948)->getLatitude());
    }

    /** @test */
    public function testLongitude()
    {
        $position = new Position();
        $this->assertEquals(1.123465498, $position->setLongitude(1.123465498)->getLongitude());
    }

    /** @test */
    public function testJsonSerialize()
    {
        $position = (new Position())
            ->setLatitude(2.56784948)
            ->setLongitude(1.123465498);

        $this->assertEquals([
            'latitude'  => 2.56784948,
            'longitude' => 1.123465498,
        ], $position->jsonSerialize());
    }
}
