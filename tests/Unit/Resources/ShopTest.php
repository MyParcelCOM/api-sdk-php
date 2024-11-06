<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Unit\Resources;

use DateTime;
use MyParcelCom\ApiSdk\Resources\Interfaces\AddressInterface;
use MyParcelCom\ApiSdk\Resources\Shop;
use PHPUnit\Framework\TestCase;

class ShopTest extends TestCase
{
    /** @test */
    public function testId()
    {
        $shop = new Shop();
        $this->assertEquals('shop-id', $shop->setId('shop-id')->getId());
    }

    /** @test */
    public function testGetType()
    {
        $shop = new Shop();
        $this->assertEquals('shops', $shop->getType());
    }

    /** @test */
    public function testName()
    {
        $shop = new Shop();
        $this->assertEquals('MyParcel.com Test Shop', $shop->setName('MyParcel.com Test Shop')->getName());
    }

    /** @test */
    public function testWebsite()
    {
        $shop = new Shop();

        $this->assertNull($shop->getWebsite());

        $this->assertEquals('https://test.shop', $shop->setWebsite('https://test.shop')->getWebsite());
    }

    /** @test */
    public function testSenderAddress()
    {
        $shop = new Shop();
        $address = $this->getMockBuilder(AddressInterface::class)->getMock();

        $this->assertEquals($address, $shop->setSenderAddress($address)->getSenderAddress());
    }

    /** @test */
    public function testReturnAddress()
    {
        $shop = new Shop();
        $address = $this->getMockBuilder(AddressInterface::class)->getMock();

        $this->assertEquals($address, $shop->setReturnAddress($address)->getReturnAddress());
    }

    /** @test */
    public function testCreatedAd()
    {
        $shop = new Shop();

        $this->assertEquals(1509001337, $shop->setCreatedAt(1509001337)->getCreatedAt()->getTimestamp());
        $dateTime = (new DateTime())->setTimestamp(1509009001);
        $this->assertEquals($dateTime, $shop->setCreatedAt($dateTime)->getCreatedAt());
    }

    /** @test */
    public function testJsonSerialize()
    {
        $returnAddress = $this->getMockBuilder(AddressInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $returnAddress->method('jsonSerialize')
            ->willReturn([
                'street_1'             => 'Diagonally',
                'street_2'             => 'Apartment 4',
                'street_number'        => 4,
                'street_number_suffix' => 'A',
                'postal_code'          => '1AR BR2',
                'city'                 => 'London',
                'region_code'          => 'NH',
                'country_code'         => 'AF',
                'first_name'           => 'Robert',
                'last_name'            => 'Drop Tables',
                'company'              => 'ACME co.',
                'email'                => 'rob@tables.com',
                'phone_number'         => '+31 (0)234 567 890',
            ]);

        $senderAddress = $this->getMockBuilder(AddressInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $senderAddress->method('jsonSerialize')
            ->willReturn([
                'street_1'             => 'Diagonally',
                'street_2'             => 'Apartment 4',
                'street_number'        => 2,
                'street_number_suffix' => 'A',
                'postal_code'          => '1AR BR2',
                'city'                 => 'London',
                'region_code'          => 'NH',
                'country_code'         => 'AF',
                'first_name'           => 'Robert',
                'last_name'            => 'Drop Tables',
                'company'              => 'ACME co.',
                'email'                => 'rob@tables.com',
                'phone_number'         => '+31 (0)234 567 890',
            ]);

        $shop = (new Shop())
            ->setId('shop-id')
            ->setName('MyParcel.com Test Shop')
            ->setWebsite('https://test.shop')
            ->setReturnAddress($returnAddress)
            ->setSenderAddress($senderAddress)
            ->setCreatedAt(1509001337);

        $this->assertEquals([
            'id'         => 'shop-id',
            'type'       => 'shops',
            'attributes' => [
                'name'           => 'MyParcel.com Test Shop',
                'website'        => 'https://test.shop',
                'sender_address' => [
                    'street_1'             => 'Diagonally',
                    'street_2'             => 'Apartment 4',
                    'street_number'        => 2,
                    'street_number_suffix' => 'A',
                    'postal_code'          => '1AR BR2',
                    'city'                 => 'London',
                    'region_code'          => 'NH',
                    'country_code'         => 'AF',
                    'first_name'           => 'Robert',
                    'last_name'            => 'Drop Tables',
                    'company'              => 'ACME co.',
                    'email'                => 'rob@tables.com',
                    'phone_number'         => '+31 (0)234 567 890',
                ],
                'return_address' => [
                    'street_1'             => 'Diagonally',
                    'street_2'             => 'Apartment 4',
                    'street_number'        => 4,
                    'street_number_suffix' => 'A',
                    'postal_code'          => '1AR BR2',
                    'city'                 => 'London',
                    'region_code'          => 'NH',
                    'country_code'         => 'AF',
                    'first_name'           => 'Robert',
                    'last_name'            => 'Drop Tables',
                    'company'              => 'ACME co.',
                    'email'                => 'rob@tables.com',
                    'phone_number'         => '+31 (0)234 567 890',
                ],
                'created_at'     => 1509001337,
            ],
        ], $shop->jsonSerialize());
    }
}
