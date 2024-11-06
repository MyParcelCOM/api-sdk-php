<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Unit\Resources;

use MyParcelCom\ApiSdk\Exceptions\MyParcelComException;
use MyParcelCom\ApiSdk\Resources\Interfaces\AddressInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\CarrierInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\OpeningHourInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\PositionInterface;
use MyParcelCom\ApiSdk\Resources\PickUpDropOffLocation;
use MyParcelCom\ApiSdk\Utils\DistanceUtils;
use PHPUnit\Framework\TestCase;

class PickUpDropOffLocationTest extends TestCase
{
    /** @test */
    public function testId()
    {
        $pudoLocation = new PickUpDropOffLocation();
        $this->assertEquals('pudo-id', $pudoLocation->setId('pudo-id')->getId());
    }

    /** @test */
    public function testGetType()
    {
        $pudoLocation = new PickUpDropOffLocation();
        $this->assertEquals('pickup-dropoff-locations', $pudoLocation->getType());
    }

    /** @test */
    public function testCode()
    {
        $pudoLocation = new PickUpDropOffLocation();
        $this->assertEquals('A123', $pudoLocation->setCode('A123')->getCode());
    }

    /** @test */
    public function testAddress()
    {
        $address = $this->getMockBuilder(AddressInterface::class)->getMock();

        $pudoLocation = new PickUpDropOffLocation();
        $this->assertEquals($address, $pudoLocation->setAddress($address)->getAddress());
    }

    /** @test */
    public function testOpeningHours()
    {
        $pudoLocation = new PickUpDropOffLocation();

        $this->assertEmpty($pudoLocation->getOpeningHours());

        $mockBuilder = $this->getMockBuilder(OpeningHourInterface::class);
        $openingHours = [$mockBuilder->getMock(), $mockBuilder->getMock()];

        $pudoLocation->setOpeningHours($openingHours);
        $this->assertCount(2, $pudoLocation->getOpeningHours());
        $this->assertEquals($openingHours, $pudoLocation->getOpeningHours());

        $openingHour = $mockBuilder->getMock();
        $openingHours[] = $openingHour;
        $pudoLocation->addOpeningHour($openingHour);
        $this->assertCount(3, $pudoLocation->getOpeningHours());
        $this->assertEquals($openingHours, $pudoLocation->getOpeningHours());
    }

    /** @test */
    public function testPosition()
    {
        $pudoLocation = new PickUpDropOffLocation();
        $position = $this->getMockBuilder(PositionInterface::class)->getMock();

        $this->assertEquals($position, $pudoLocation->setPosition($position)->getPosition());
    }

    /** @test */
    public function testCarrier()
    {
        $pudoLocation = new PickUpDropOffLocation();
        $carrier = $this->getMockBuilder(CarrierInterface::class)->getMock();

        $this->assertEquals($carrier, $pudoLocation->setCarrier($carrier)->getCarrier());
    }

    /** @test */
    public function testDistance()
    {
        $position = new PickUpDropOffLocation();
        $this->assertEquals(900, $position->setDistance(900)->getDistance());
        $this->assertEquals(80, $position->setDistance(80, DistanceUtils::UNIT_METER)->getDistance());
        $this->assertEquals(3000, $position->setDistance(3, DistanceUtils::UNIT_KILOMETER)->getDistance());
        $this->assertEquals(1524, $position->setDistance(5000, DistanceUtils::UNIT_FOOT)->getDistance());
        $this->assertEquals(19312, $position->setDistance(12, DistanceUtils::UNIT_MILE)->getDistance());
        $this->assertEquals(80, $position->setDistance(80)->getDistance(DistanceUtils::UNIT_METER));
        $this->assertEquals(3, $position->setDistance(3000)->getDistance(DistanceUtils::UNIT_KILOMETER));
        $this->assertEquals(5000, $position->setDistance(1524)->getDistance(DistanceUtils::UNIT_FOOT));
        $this->assertEquals(12, $position->setDistance(19312)->getDistance(DistanceUtils::UNIT_MILE));
    }

    /** @test */
    public function testSetDistanceInvalidUnit()
    {
        $position = new PickUpDropOffLocation();

        $this->expectException(MyParcelComException::class);
        $position->setDistance(900, 'lightyears');
    }

    /** @test */
    public function testGetDistanceInvalidUnit()
    {
        $position = new PickUpDropOffLocation();
        $position->setDistance(900);

        $this->expectException(MyParcelComException::class);
        $position->getDistance('au');
    }

    /** @test */
    public function testItSetsAndGetsCategories()
    {
        $location = new PickUpDropOffLocation();

        $this->assertEquals(['drop-off'], $location->setCategories(['drop-off'])->getCategories());
    }

    /** @test */
    public function testJsonSerialize()
    {
        $address = $this->getMockBuilder(AddressInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $address->method('jsonSerialize')
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

        $openingHour = $this->getMockBuilder(OpeningHourInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $openingHour->method('jsonSerialize')
            ->willReturn([
                'day'    => 'Sunday',
                'open'   => '09:00',
                'closed' => '19:00',
            ]);

        $position = $this->getMockBuilder(PositionInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $position->method('jsonSerialize')
            ->willReturn([
                'latitude'  => 1.2345,
                'longitude' => 2.34567,
            ]);

        $pudoLocation = (new PickUpDropOffLocation())
            ->setId('pudo-id')
            ->setAddress($address)
            ->setOpeningHours([$openingHour])
            ->setPosition($position)
            ->setDistance(5000)
            ->setCategories(['drop-off', 'pick-up']);

        $this->assertEquals([
            'id'         => 'pudo-id',
            'type'       => 'pickup-dropoff-locations',
            'attributes' => [
                'address'       => [
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
                'opening_hours' => [
                    [
                        'day'    => 'Sunday',
                        'open'   => '09:00',
                        'closed' => '19:00',
                    ],
                ],
                'position'      => [
                    'latitude'  => 1.2345,
                    'longitude' => 2.34567,
                ],
                'categories'    => [
                    'drop-off',
                    'pick-up',
                ],
            ],
            'meta'       => [
                'distance' => 5000,
            ],
        ], $pudoLocation->jsonSerialize());
    }
}
