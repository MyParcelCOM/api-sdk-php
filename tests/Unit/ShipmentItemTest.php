<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Unit;

use MyParcelCom\ApiSdk\Enums\WeightUnitEnum;
use MyParcelCom\ApiSdk\Exceptions\MyParcelComException;
use MyParcelCom\ApiSdk\Resources\Interfaces\PhysicalPropertiesInterface;
use MyParcelCom\ApiSdk\Resources\ShipmentItem;
use PHPUnit\Framework\TestCase;

class ShipmentItemTest extends TestCase
{
    /** @test */
    public function testSku()
    {
        $item = new ShipmentItem();
        $this->assertEquals('CM01-W', $item->setSku('CM01-W')->getSku());
    }

    /** @test */
    public function testDescription()
    {
        $item = new ShipmentItem();
        $this->assertEquals('Tea cup', $item->setDescription('Tea cup')->getDescription());
    }

    /** @test */
    public function testImageUrl()
    {
        $item = new ShipmentItem();
        $this->assertEquals('//tea.cup', $item->setImageUrl('//tea.cup')->getImageUrl());
    }

    /** @test */
    public function testHsCode()
    {
        $item = new ShipmentItem();
        $this->assertEquals('8321.21.28', $item->setHsCode('8321.21.28')->getHsCode());
    }

    /** @test */
    public function testQuantity()
    {
        $item = new ShipmentItem();
        $this->assertEquals(12, $item->setQuantity(12)->getQuantity());
    }

    /** @test */
    public function testItemValue()
    {
        $item = new ShipmentItem();
        $this->assertEquals(349, $item->setItemValue(349)->getItemValue());
    }

    /** @test */
    public function testCurrency()
    {
        $item = new ShipmentItem();
        $this->assertEquals('GBP', $item->setCurrency('GBP')->getCurrency());
    }

    /** @test */
    public function testOriginCountryCode()
    {
        $item = new ShipmentItem();
        $this->assertEquals('GB', $item->setOriginCountryCode('GB')->getOriginCountryCode());
    }

    /** @test */
    public function testPreferentialOrigin()
    {
        $item = new ShipmentItem();
        $this->assertEquals(false, $item->getIsPreferentialOrigin());
        $this->assertEquals(true, $item->setIsPreferentialOrigin(true)->getIsPreferentialOrigin());
    }

    /** @test */
    public function testItemWeight()
    {
        $item = new ShipmentItem();
        $this->assertEquals(3000, $item->setItemWeight(3000)->getItemWeight());
        $this->assertNull($item->setItemWeight(null)->getItemWeight());
    }

    /** @test */
    public function testItemWeightSetterUpdatesItemWeightUnit()
    {
        $item = new ShipmentItem();
        $this->assertEquals('g', $item->getItemWeightUnit(), 'The default unit should be grams');

        $this->assertEquals(3, $item->setItemWeight(3000)->getItemWeight(PhysicalPropertiesInterface::WEIGHT_KILOGRAM));
        $this->assertEquals('g', $item->getItemWeightUnit(), 'Using the weight getter should not alter the weight unit');

        $this->assertEquals(3, $item->setItemWeight(3, PhysicalPropertiesInterface::WEIGHT_KILOGRAM)->getItemWeight());
        $this->assertEquals('kg', $item->getItemWeightUnit(), 'Using the weight setter should alter the weight unit');

        $this->assertEquals(3, $item->setItemWeight(3, WeightUnitEnum::MILLIGRAM)->getItemWeight());
        $this->assertEquals('mg', $item->getItemWeightUnit(), 'Using mg should be possible with the WeightUnitEnum');
    }

    /** @test */
    public function testItemWeightGetterConversion()
    {
        $item = new ShipmentItem();

        $this->assertEquals(3000, $item->setItemWeight(3, WeightUnitEnum::KILOGRAM)->getItemWeight(WeightUnitEnum::GRAM));
        $this->assertEquals(1, $item->setItemWeight(3, WeightUnitEnum::MILLIGRAM)->getItemWeight(WeightUnitEnum::GRAM));
        $this->assertEquals(85049, $item->setItemWeight(3, WeightUnitEnum::OUNCE)->getItemWeight(WeightUnitEnum::MILLIGRAM));
    }

    /** @test */
    public function testItemWeightStillSupportsStones()
    {
        $item = new ShipmentItem();

        $item->setItemWeight(1, PhysicalPropertiesInterface::WEIGHT_STONE);

        $this->assertEquals(6351, $item->getItemWeight());
        $this->assertEquals('g', $item->getItemWeightUnit(), 'The unit should be grams because stones are not supported');

        $item->setItemWeight(30000, PhysicalPropertiesInterface::WEIGHT_KILOGRAM);

        $this->assertEquals(5, $item->getItemWeight(PhysicalPropertiesInterface::WEIGHT_STONE));
        $this->assertEquals('kg', $item->getItemWeightUnit());
    }

    /** @test */
    public function testItemWeightUnit()
    {
        $item = new ShipmentItem();
        $this->assertEquals('mg', $item->setItemWeightUnit(WeightUnitEnum::MILLIGRAM)->getItemWeightUnit());
    }

    /** @test */
    public function testItemWeightValueException()
    {
        $this->expectException(MyParcelComException::class);

        (new ShipmentItem())->setItemWeightUnit('boo');
    }

    /** @test */
    public function testVatPercentage()
    {
        $item = new ShipmentItem();
        $this->assertNull($item->getVatPercentage());
        $this->assertEquals(20, $item->setVatPercentage(20)->getVatPercentage());
    }

    /** @test */
    public function testInvalidVatPercentageException()
    {
        $this->expectException(MyParcelComException::class);

        (new ShipmentItem())->setVatPercentage(101);
    }

    /** @test */
    public function testNegativeVatPercentageException()
    {
        $this->expectException(MyParcelComException::class);

        (new ShipmentItem())->setVatPercentage(-1);
    }

    /** @test */
    public function testJsonSerialize()
    {
        $item = (new ShipmentItem())
            ->setSku('CM01-W')
            ->setDescription('Tea cup')
            ->setImageUrl('//tea.cup')
            ->setHsCode('8321.21.28')
            ->setQuantity(12)
            ->setItemValue(349)
            ->setItemWeight(128)
            ->setCurrency('GBP')
            ->setVatPercentage(20)
            ->setOriginCountryCode('GB')
            ->setIsPreferentialOrigin(true);
        $this->assertEquals(
            [
                'sku'                    => 'CM01-W',
                'description'            => 'Tea cup',
                'image_url'              => '//tea.cup',
                'hs_code'                => '8321.21.28',
                'quantity'               => 12,
                'item_value'             => [
                    'amount'   => 349,
                    'currency' => 'GBP',
                ],
                'item_weight'            => 128,
                'item_weight_unit'       => 'g',
                'vat_percentage'         => 20,
                'origin_country_code'    => 'GB',
                'is_preferential_origin' => true,
            ],
            $item->jsonSerialize()
        );
    }
}
