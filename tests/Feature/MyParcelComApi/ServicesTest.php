<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Feature\MyParcelComApi;

use MyParcelCom\ApiSdk\Collection\CollectionInterface;
use MyParcelCom\ApiSdk\Resources\Address;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\ApiSdk\Resources\Shipment;
use MyParcelCom\ApiSdk\Tests\TestCase;

/**
 * @group Services
 */
class ServicesTest extends TestCase
{
    public function testGetServices()
    {
        $services = $this->api->getServices();

        $this->assertInstanceOf(CollectionInterface::class, $services);
        foreach ($services as $service) {
            $this->assertInstanceOf(ServiceInterface::class, $service);
        }
    }

    public function testGetServicesForShipment()
    {
        $recipient = (new Address())
            ->setFirstName('Bobby')
            ->setLastName('Tables')
            ->setCity('Birmingham')
            ->setStreet1('Newbourne Hill')
            ->setStreetNumber(12)
            ->setPostalCode('B48 7QN')
            ->setCountryCode('GB');
        $shipment = (new Shipment())
            ->setWeight(500)
            ->setRecipientAddress($recipient);

        $services = $this->api->getServices($shipment);

        $this->assertInstanceOf(CollectionInterface::class, $services);
        foreach ($services as $service) {
            $this->assertInstanceOf(ServiceInterface::class, $service);
        }
    }

    public function testGetServicesForCarrier()
    {
        $carriers = $this->api->getCarriers();

        foreach ($carriers as $carrier) {
            $services = $this->api->getServicesForCarrier($carrier);

            foreach ($services as $service) {
                $this->assertInstanceOf(ServiceInterface::class, $service);
                $this->assertEquals($carrier->getId(), $service->getCarrier()->getId());
            }
        }
    }}
