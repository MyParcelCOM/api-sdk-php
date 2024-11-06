<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Unit\Resources;

use MyParcelCom\ApiSdk\Resources\OpeningHour;
use PHPUnit\Framework\TestCase;

class OpeningHourTest extends TestCase
{
    /** @test */
    public function testDay()
    {
        $openingHour = new OpeningHour();
        $this->assertEquals('Sunday', $openingHour->setDay('Sunday')->getDay());
    }

    /** @test */
    public function testOpen()
    {
        $openingHour = new OpeningHour();
        $open = new \DateTime('19:00');
        $this->assertEquals('19:00', $openingHour->setOpen('19:00')->getOpen()->format('H:i'));
        $this->assertEquals($open, $openingHour->setOpen($open)->getOpen());
    }

    /** @test */
    public function testClosed()
    {
        $openingHour = new OpeningHour();
        $closed = new \DateTime('09:00');
        $this->assertEquals('09:00', $openingHour->setClosed('09:00')->getClosed()->format('H:i'));
        $this->assertEquals($closed, $openingHour->setClosed($closed)->getClosed());
    }

    /** @test */
    public function testJsonSerialize()
    {
        $openingHour = (new OpeningHour())
            ->setDay('Sunday')
            ->setOpen('09:00')
            ->setClosed('19:00');

        $this->assertEquals([
            'day'    => 'Sunday',
            'open'   => '09:00',
            'closed' => '19:00',
        ], $openingHour->jsonSerialize());
    }
}
