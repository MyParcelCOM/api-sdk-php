<?php

declare(strict_types=1);

namespace Feature\MyParcelComApi;

use MyParcelCom\ApiSdk\Collection\CollectionInterface;
use MyParcelCom\ApiSdk\Resources\Address;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceRateInterface;
use MyParcelCom\ApiSdk\Resources\Organization;
use MyParcelCom\ApiSdk\Resources\PhysicalProperties;
use MyParcelCom\ApiSdk\Resources\Shipment;
use MyParcelCom\ApiSdk\Resources\Shop;
use MyParcelCom\ApiSdk\Tests\TestCase;

/**
 * @group ServiceRates
 */
class ServiceRatesTest extends TestCase
{
    public function testItRetrievesServiceRates()
    {
        $serviceRates = $this->api->getServiceRates();

        $this->assertInstanceOf(CollectionInterface::class, $serviceRates);
        foreach ($serviceRates as $serviceRate) {
            $this->assertInstanceOf(ServiceRateInterface::class, $serviceRate);
        }
    }

    public function testItRetrievesServiceRatesForAService()
    {
        $services = $this->api->getServices()->get();
        $service = reset($services);

        $serviceRates = $service->getServiceRates();
        foreach ($serviceRates as $serviceRate) {
            $this->assertInstanceOf(ServiceRateInterface::class, $serviceRate);
        }
    }

    public function testItRetrievesServiceRatesForShipment()
    {
        $recipient = (new Address())
            ->setFirstName('Hank')
            ->setLastName('Mozzy')
            ->setCity('London')
            ->setStreet1('Allen Street')
            ->setStreetNumber(1)
            ->setPostalCode('W8 6UX')
            ->setCountryCode('GB');

        $shop = (new Shop())
            ->setId('shop-id')
            ->setOrganization((new Organization())->setId('org-id'));

        $shipment = (new Shipment())
            ->setShop($shop)
            ->setWeight(500)
            ->setRecipientAddress($recipient);

        $serviceRates = $this->api->getServiceRatesForShipment($shipment);
        $this->assertInstanceOf(CollectionInterface::class, $serviceRates);
        foreach ($serviceRates as $serviceRate) {
            $this->assertInstanceOf(ServiceRateInterface::class, $serviceRate);
            $this->assertGreaterThanOrEqual(500, $serviceRate->getWeightMax());
            $this->assertLessThanOrEqual(500, $serviceRate->getWeightMin());
            $this->assertEquals('Letter Test', $serviceRate->getService()->getName(), 'Included service name');
            $this->assertEquals('letter-test', $serviceRate->getService()->getCode(), 'Included service code');
            $this->assertTrue(in_array($serviceRate->getContract()->getName(), [
                'Contract X',
                'Contract Y',
            ]), 'Included contract name');
        }
    }

    public function testItRetrievesDynamicServiceRatesForShipment()
    {
        $recipient = (new Address())
            ->setFirstName('Steven')
            ->setLastName('Frayne')
            ->setCity('Vancouver')
            ->setStreet1('1st Street')
            ->setPostalCode('W8 6UX')
            ->setCountryCode('CA');

        $shop = (new Shop())
            ->setId('shop-id')
            ->setOrganization((new Organization())->setId('org-id'));

        $shipment = (new Shipment())
            ->setShop($shop)
            ->setWeight(500)
            ->setRecipientAddress($recipient);

        $serviceRates = $this->api->getServiceRatesForShipment($shipment);
        $this->assertInstanceOf(CollectionInterface::class, $serviceRates);
        foreach ($serviceRates as $serviceRate) {
            $this->assertInstanceOf(ServiceRateInterface::class, $serviceRate);
            $this->assertEquals(0, $serviceRate->getWeightMin());
            $this->assertEquals(10000, $serviceRate->getWeightMax());
            $this->assertEquals('Dynamic Test', $serviceRate->getService()->getName(), 'Included service name');
            $this->assertEquals('dynamic-test', $serviceRate->getService()->getCode(), 'Included service code');
            $this->assertEquals('Contract Z', $serviceRate->getContract()->getName(), 'Included contract name');
            $this->assertEquals(321, $serviceRate->getPrice());
        }
    }

    public function testItResolvesServiceRatesWithWeightBracketForShipment()
    {
        $recipient = (new Address())
            ->setFirstName('Steven')
            ->setLastName('Frayne')
            ->setCity('Vancouver')
            ->setStreet1('1st Street')
            ->setPostalCode('W8 6UX')
            ->setCountryCode('CA');

        $shop = (new Shop())
            ->setId('shop-id')
            ->setOrganization((new Organization())->setId('org-id'));

        $shipment = (new Shipment())
            ->setShop($shop)
            ->setPhysicalProperties((new PhysicalProperties())->setWeight(9000))
            ->setRecipientAddress($recipient);

        $serviceRates = $this->api->getServiceRatesForShipment($shipment);
        $this->assertInstanceOf(CollectionInterface::class, $serviceRates);
        foreach ($serviceRates as $serviceRate) {
            $this->assertInstanceOf(ServiceRateInterface::class, $serviceRate);
            $this->assertEquals(0, $serviceRate->getWeightMin());
            $this->assertEquals(30000, $serviceRate->getWeightMax());
            $this->assertEquals([
                'start'        => 6000,
                'start_amount' => 500,
                'size'         => 1000,
                'size_amount'  => 50,
            ], $serviceRate->getWeightBracket());
            $this->assertEquals(9500, $serviceRate->getPrice(), 'Should be the meta.bracket_price.amount');
            $this->assertEquals('EUR', $serviceRate->getCurrency(), 'Should be the meta.bracket_price.currency');

            $this->assertEquals(500, $serviceRate->calculateBracketPrice(0));
            $this->assertEquals(500, $serviceRate->calculateBracketPrice(6000));
            $this->assertEquals(550, $serviceRate->calculateBracketPrice(6001));
            $this->assertEquals(650, $serviceRate->calculateBracketPrice(9000));
            $this->assertEquals(700, $serviceRate->calculateBracketPrice(9001));
        }
    }

    public function testRetrievingServiceRatesConsidersVolumetricWeight()
    {
        $recipient = (new Address())
            ->setFirstName('Hank')
            ->setLastName('Mozzy')
            ->setCity('London')
            ->setStreet1('Allen Street')
            ->setStreetNumber(1)
            ->setPostalCode('W8 6UX')
            ->setCountryCode('GB');

        $physicalProperties = new PhysicalProperties();
        $physicalProperties
            ->setWeight(500)
            ->setLength(400)
            ->setHeight(400)
            ->setWidth(400);

        $shipment = (new Shipment())
            ->setPhysicalProperties($physicalProperties)
            ->setRecipientAddress($recipient);

        $serviceRates = $this->api->getServiceRatesForShipment($shipment);
        $this->assertInstanceOf(CollectionInterface::class, $serviceRates);
        foreach ($serviceRates as $serviceRate) {
            $this->assertInstanceOf(ServiceRateInterface::class, $serviceRate);
            $this->assertGreaterThanOrEqual($volumetricWeight, $serviceRate->getWeightMax());
            $this->assertLessThanOrEqual($volumetricWeight, $serviceRate->getWeightMin());
        }
    }
}
