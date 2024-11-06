<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Unit\Resources;

use MyParcelCom\ApiSdk\Enums\TaxTypeEnum;
use MyParcelCom\ApiSdk\Resources\TaxIdentificationNumber;
use PHPUnit\Framework\TestCase;

class TaxIdentificationNumberTest extends TestCase
{
    /** @test */
    public function testCountryCode()
    {
        $number = new TaxIdentificationNumber();
        $this->assertEquals('GB', $number->setCountryCode('GB')->getCountryCode());
    }

    /** @test */
    public function testNumber()
    {
        $number = new TaxIdentificationNumber();
        $this->assertEquals('IOSS123', $number->setNumber('IOSS123')->getNumber());
    }

    /** @test */
    public function testDescription()
    {
        $number = new TaxIdentificationNumber();
        $this->assertNull($number->getDescription());
        $this->assertEquals('Test number', $number->setDescription('Test number')->getDescription());
    }

    /** @test */
    public function testType()
    {
        $number = new TaxIdentificationNumber();
        $this->assertEquals('eori', $number->setType(TaxTypeEnum::EORI())->getType()->getValue());
    }

    /** @test */
    public function testJsonSerialize()
    {
        $number = (new TaxIdentificationNumber())
            ->setCountryCode('GB')
            ->setNumber('IOSS123')
            ->setDescription('Test number')
            ->setType(TaxTypeEnum::IOSS());
        $this->assertEquals(
            [
                'country_code' => 'GB',
                'number'       => 'IOSS123',
                'description'  => 'Test number',
                'type'         => 'ioss',
            ],
            $number->jsonSerialize()
        );
    }
}
