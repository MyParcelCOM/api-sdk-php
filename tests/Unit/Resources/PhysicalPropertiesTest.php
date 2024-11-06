<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Unit\Resources;

use MyParcelCom\ApiSdk\Resources\Interfaces\PhysicalPropertiesInterface;
use MyParcelCom\ApiSdk\Resources\PhysicalProperties;
use PHPUnit\Framework\TestCase;

class PhysicalPropertiesTest extends TestCase
{
    /** @test */
    public function testWeight()
    {
        $physicalProperties = new PhysicalProperties();
        $this->assertEquals(8000, $physicalProperties->setWeight(8000)->getWeight());
        $this->assertEquals(
            500,
            $physicalProperties->setWeight(500, PhysicalPropertiesInterface::WEIGHT_GRAM)->getWeight(),
        );
        $this->assertEquals(
            3000,
            $physicalProperties->setWeight(3, PhysicalPropertiesInterface::WEIGHT_KILOGRAM)->getWeight(),
        );
        $this->assertEquals(
            1701,
            $physicalProperties->setWeight(60, PhysicalPropertiesInterface::WEIGHT_OUNCE)->getWeight(),
        );
        $this->assertEquals(
            2268,
            $physicalProperties->setWeight(5, PhysicalPropertiesInterface::WEIGHT_POUND)->getWeight(),
        );
        $this->assertEquals(
            12701,
            $physicalProperties->setWeight(2, PhysicalPropertiesInterface::WEIGHT_STONE)->getWeight(),
        );
        $this->assertEquals(
            500,
            $physicalProperties->setWeight(500)->getWeight(PhysicalPropertiesInterface::WEIGHT_GRAM),
        );
        $this->assertEquals(
            3,
            $physicalProperties->setWeight(3000)->getWeight(PhysicalPropertiesInterface::WEIGHT_KILOGRAM),
        );
        $this->assertEquals(
            60,
            $physicalProperties->setWeight(1701)->getWeight(PhysicalPropertiesInterface::WEIGHT_OUNCE),
        );
        $this->assertEquals(
            5,
            $physicalProperties->setWeight(2268)->getWeight(PhysicalPropertiesInterface::WEIGHT_POUND),
        );
        $this->assertEquals(
            2,
            $physicalProperties->setWeight(12701)->getWeight(PhysicalPropertiesInterface::WEIGHT_STONE),
        );
    }

    /** @test */
    public function testLength()
    {
        $physicalProperties = new PhysicalProperties();
        $this->assertEquals(1100, $physicalProperties->setLength(1100)->getLength());
    }

    /** @test */
    public function testVolume()
    {
        $physicalProperties = new PhysicalProperties();
        $this->assertEquals(1200, $physicalProperties->setVolume(1200)->getVolume());
    }

    /** @test */
    public function testHeight()
    {
        $physicalProperties = new PhysicalProperties();
        $this->assertEquals(1300, $physicalProperties->setHeight(1300)->getHeight());
    }

    /** @test */
    public function testWidth()
    {
        $physicalProperties = new PhysicalProperties();
        $this->assertEquals(1400, $physicalProperties->setWidth(1400)->getWidth());
    }

    /** @test */
    public function testVolumetricWeight()
    {
        $physicalProperties = new PhysicalProperties();
        $this->assertNull($physicalProperties->getVolumetricWeight());

        $physicalProperties->setVolumetricWeight(1500);
        $this->assertEquals(1500, $physicalProperties->getVolumetricWeight());
    }

    /** @test */
    public function testJsonSerializeDoesNotIncludeVolumetricWeight()
    {
        $physicalProperties = (new PhysicalProperties())
            ->setWeight(1000)
            ->setLength(1100)
            ->setVolume(1200)
            ->setHeight(1300)
            ->setWidth(1400)
            ->setVolumetricWeight(1500);

        $this->assertEquals([
            'weight' => 1000,
            'length' => 1100,
            'volume' => 1200,
            'height' => 1300,
            'width'  => 1400,
        ], $physicalProperties->jsonSerialize());
    }
}
