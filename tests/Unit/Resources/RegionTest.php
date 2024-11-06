<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Unit\Resources;

use MyParcelCom\ApiSdk\Resources\Interfaces\RegionInterface;
use MyParcelCom\ApiSdk\Resources\Region;
use PHPUnit\Framework\TestCase;

class RegionTest extends TestCase
{
    /** @test */
    public function testId()
    {
        $region = new Region();
        $this->assertEquals('region-id', $region->setId('region-id')->getId());
    }

    /** @test */
    public function testType()
    {
        $region = new Region();
        $this->assertEquals('regions', $region->getType());
    }

    /** @test */
    public function testCountryCode()
    {
        $region = new Region();
        $this->assertEquals('NL', $region->setCountryCode('NL')->getCountryCode());
    }

    /** @test */
    public function testRegionCode()
    {
        $region = new Region();
        $this->assertEquals('ZH', $region->setRegionCode('ZH')->getRegionCode());
    }

    /** @test */
    public function testCurrency()
    {
        $region = new Region();
        $this->assertEquals('EUR', $region->setCurrency('EUR')->getCurrency());
    }

    /** @test */
    public function testName()
    {
        $region = new Region();
        $this->assertEquals('Rotterdam', $region->setName('Rotterdam')->getName());
    }

    /** @test */
    public function testItSetsAndGetsCategory()
    {
        $region = new Region();
        $this->assertEquals('country', $region->setCategory('country')->getCategory());
    }

    /** @test */
    public function testItSetsAndGetsParentRegion()
    {
        $region = new Region();

        $mock = $this->createMock(RegionInterface::class);
        $this->assertEquals($mock, $region->setParent($mock)->getParent());
    }

    /** @test */
    public function testJsonSerialize()
    {
        $region = (new Region())
            ->setId('region-id-1')
            ->setCountryCode('NL')
            ->setRegionCode('ZH')
            ->setCurrency('EUR')
            ->setName('Rotterdam')
            ->setCategory('country');

        $parent = $this->createMock(RegionInterface::class);
        $parent->method('jsonSerialize')->willReturn([
            'type' => 'regions',
            'id'   => 'region-id-2',
        ]);
        $region->setParent($parent);

        $this->assertEquals([
            'id'            => 'region-id-1',
            'type'          => 'regions',
            'attributes'    => [
                'country_code' => 'NL',
                'region_code'  => 'ZH',
                'currency'     => 'EUR',
                'name'         => 'Rotterdam',
                'category'     => 'country',
            ],
            'relationships' => [
                'parent' => [
                    'data' => [
                        'type' => 'regions',
                        'id'   => 'region-id-2',
                    ],
                ],
            ],
        ], $region->jsonSerialize());
    }
}
