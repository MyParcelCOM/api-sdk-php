<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Unit\Resources;

use MyParcelCom\ApiSdk\Resources\Address;
use PHPUnit\Framework\TestCase;

class AddressTest extends TestCase
{
    /** @test */
    public function testStreet1()
    {
        $address = new Address();
        $this->assertEquals('Diagonally', $address->setStreet1('Diagonally')->getStreet1());
    }

    /** @test */
    public function testStreet2()
    {
        $address = new Address();
        $this->assertEquals('Apartment 4', $address->setStreet2('Apartment 4')->getStreet2());
    }

    /** @test */
    public function testStreetNumber()
    {
        $address = new Address();
        $this->assertEquals(4, $address->setStreetNumber(4)->getStreetNumber());
    }

    /** @test */
    public function testStreetNumberSuffix()
    {
        $address = new Address();
        $this->assertEquals('A', $address->setStreetNumberSuffix('A')->getStreetNumberSuffix());
    }

    /** @test */
    public function testPostalCode()
    {
        $address = new Address();
        $this->assertEquals('1AR BR2', $address->setPostalCode('1AR BR2')->getPostalCode());
    }

    /** @test */
    public function testCity()
    {
        $address = new Address();
        $this->assertEquals('London', $address->setCity('London')->getCity());
    }

    /** @test */
    public function testRegionCode()
    {
        $address = new Address();
        $this->assertEquals('NH', $address->setRegionCode('NH')->getRegionCode());
    }

    /** @test */
    public function testStateCode()
    {
        $address = new Address();
        $this->assertNull($address->getStateCode());
        $this->assertEquals('CA', $address->setStateCode('CA')->getStateCode());
    }

    /** @test */
    public function testCountryCode()
    {
        $address = new Address();
        $this->assertEquals('AF', $address->setCountryCode('AF')->getCountryCode());
    }

    /** @test */
    public function testFirstName()
    {
        $address = new Address();
        $this->assertEquals('Robert', $address->setFirstName('Robert')->getFirstName());
    }

    /** @test */
    public function testLastName()
    {
        $address = new Address();
        $this->assertEquals('Drop Tables', $address->setLastName('Drop Tables')->getLastName());
    }

    /** @test */
    public function testCompany()
    {
        $address = new Address();
        $this->assertEquals('ACME co.', $address->setCompany('ACME co.')->getCompany());
    }

    /** @test */
    public function testEmail()
    {
        $address = new Address();
        $this->assertEquals('rob@tables.com', $address->setEmail('rob@tables.com')->getEmail());
    }

    /** @test */
    public function testPhoneNumber()
    {
        $address = new Address();
        $this->assertEquals('+31 (0)234 567 890', $address->setPhoneNumber('+31 (0)234 567 890')->getPhoneNumber());
    }

    /** @test */
    public function testJsonSerialize()
    {
        $address = (new Address())
            ->setStreet1('Diagonally')
            ->setStreet2('Apartment 4')
            ->setStreetNumber(4)
            ->setStreetNumberSuffix('A')
            ->setPostalCode('1AR BR2')
            ->setCity('London')
            ->setRegionCode('NH')
            ->setStateCode('CA')
            ->setCountryCode('AF')
            ->setFirstName('Robert')
            ->setLastName('Drop Tables')
            ->setCompany('ACME co.')
            ->setEmail('rob@tables.com')
            ->setPhoneNumber('+31 (0)234 567 890');

        $this->assertEquals([
            'street_1'             => 'Diagonally',
            'street_2'             => 'Apartment 4',
            'street_number'        => 4,
            'street_number_suffix' => 'A',
            'postal_code'          => '1AR BR2',
            'city'                 => 'London',
            'region_code'          => 'NH',
            'state_code'           => 'CA',
            'country_code'         => 'AF',
            'first_name'           => 'Robert',
            'last_name'            => 'Drop Tables',
            'company'              => 'ACME co.',
            'email'                => 'rob@tables.com',
            'phone_number'         => '+31 (0)234 567 890',
        ], $address->jsonSerialize());
    }
}
