<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Unit;

use MyParcelCom\ApiSdk\Resources\Carrier;
use PHPUnit\Framework\TestCase;

class CarrierTest extends TestCase
{
    public function testId()
    {
        $carrier = new Carrier();

        $this->assertNull($carrier->getId());

        $this->assertEquals('carrier-id', $carrier->setId('carrier-id')->getId());
    }

    public function testName()
    {
        $carrier = new Carrier();

        $this->assertEquals('MyParcel.com Carrier', $carrier->setName('MyParcel.com Carrier')->getName());
    }

    public function testGetType()
    {
        $carrier = new Carrier();

        $this->assertEquals('carriers', $carrier->getType());
    }

    public function testCode()
    {
        $carrier = new Carrier();

        $this->assertEquals('some-code', $carrier->setCode('some-code')->getCode());
    }

    public function testCredentialsFormat()
    {
        $carrier = new Carrier();

        $this->assertEquals([], $carrier->getCredentialsFormat());

        $carrier->setCredentialsFormat([
            "additionalProperties" => false,
            "required"             => [
                "api_secret",
            ],
            "properties"           => [
                "api_secret" => [
                    "type" => "string",
                ],
            ],
        ]);

        $this->assertEquals([
            "additionalProperties" => false,
            "required"             => [
                "api_secret",
            ],
            "properties"           => [
                "api_secret" => [
                    "type" => "string",
                ],
            ],
        ], $carrier->getCredentialsFormat());
    }

    public function testOffersCollections()
    {
        $carrier = new Carrier();

        $this->assertTrue($carrier->setOffersCollections(true)->getOffersCollections());
        $this->assertFalse($carrier->setOffersCollections(false)->getOffersCollections());
    }

    public function testVoidsRegisteredCollections()
    {
        $carrier = new Carrier();

        $this->assertTrue($carrier->setVoidsRegisteredCollections(true)->getVoidsRegisteredCollections());
        $this->assertFalse($carrier->setVoidsRegisteredCollections(false)->getVoidsRegisteredCollections());
    }

    public function testAllowsAddingRegisteredShipmentsToCollection()
    {
        $carrier = new Carrier();

        $this->assertTrue(
            $carrier->setAllowsAddingRegisteredShipmentsToCollection(true)
                ->getAllowsAddingRegisteredShipmentsToCollection(),
        );
        $this->assertFalse(
            $carrier->setAllowsAddingRegisteredShipmentsToCollection(false)
                ->getAllowsAddingRegisteredShipmentsToCollection(),
        );
    }

    public function testJsonSerialize()
    {
        $carrier = (new Carrier())
            ->setId('carrier-id')
            ->setName('MyParcel.com Carrier')
            ->setCode('carrier-code')
            ->setCredentialsFormat([
                "additionalProperties" => false,
                "required"             => [
                    "api_user",
                    "api_password",
                ],
                "properties"           => [
                    "api_user"     => [
                        "type" => "string",
                    ],
                    "api_password" => [
                        "type" => "string",
                    ],
                ],
            ]);

        $this->assertEquals([
            'id'         => 'carrier-id',
            'type'       => 'carriers',
            'attributes' => [
                'name'               => 'MyParcel.com Carrier',
                'code'               => 'carrier-code',
                'credentials_format' => [
                    "additionalProperties" => false,
                    "required"             => [
                        "api_user",
                        "api_password",
                    ],
                    "properties"           => [
                        "api_user"     => [
                            "type" => "string",
                        ],
                        "api_password" => [
                            "type" => "string",
                        ],
                    ],
                ],
            ],
        ], $carrier->jsonSerialize());
    }
}
