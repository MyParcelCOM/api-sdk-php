<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Feature\MyParcelComApi;

use MyParcelCom\ApiSdk\Exceptions\InvalidResourceException;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentSurchargeInterface;
use MyParcelCom\ApiSdk\Resources\Shipment;
use MyParcelCom\ApiSdk\Resources\ShipmentSurcharge;
use MyParcelCom\ApiSdk\Tests\TestCase;

/**
 * @group Shipments
 */
class ShipmentSurchargesTest extends TestCase
{
    public function testItRetrievesAShipmentSurcharge(): void
    {
        $shipmentSurcharge = $this->api->getShipmentSurcharge('4afa3758-c425-48bc-96ef-ba43c5da4082');

        $this->assertInstanceOf(ShipmentSurchargeInterface::class, $shipmentSurcharge);
        $this->assertEquals('4afa3758-c425-48bc-96ef-ba43c5da4082', $shipmentSurcharge->getId());
        $this->assertEquals('test', $shipmentSurcharge->getName());
    }

    public function testItCreatesAMinimalShipmentSurcharge(): void
    {
        $shipmentSurcharge = new ShipmentSurcharge();
        $shipmentSurcharge->setName('test');
        $shipmentSurcharge->setFeeAmount(100);
        $shipmentSurcharge->setFeeCurrency('EUR');
        $shipmentSurcharge->setShipment((new Shipment())->setId('872960ef-2a2c-4ab5-9a36-01478fd20276'));

        $createdShipmentSurcharge = $this->api->createShipmentSurcharge($shipmentSurcharge);

        $this->assertInstanceOf(ShipmentSurchargeInterface::class, $createdShipmentSurcharge);
        $this->assertEquals('4afa3758-c425-48bc-96ef-ba43c5da4082', $createdShipmentSurcharge->getId());
    }

    public function testItFailsWhenNoNameOrFeeIsSet(): void
    {
        $shipmentSurcharge = new ShipmentSurcharge();
        $shipmentSurcharge->setShipment((new Shipment())->setId('872960ef-2a2c-4ab5-9a36-01478fd20276'));

        $this->expectException(InvalidResourceException::class);
        $this->expectExceptionMessage(
            'This shipment surcharge contains invalid data. Attribute "name" is required. Attribute "fee" is required.',
        );
        $this->api->createShipmentSurcharge($shipmentSurcharge);
    }

    public function testItUpdatesAShipmentSurcharge(): void
    {
        $shipmentSurcharge = new ShipmentSurcharge();
        $shipmentSurcharge->setId('4afa3758-c425-48bc-96ef-ba43c5da4082');
        $shipmentSurcharge->setName('updated name');

        $updatedShipmentSurcharge = $this->api->updateShipmentSurcharge($shipmentSurcharge);

        $this->assertInstanceOf(ShipmentSurchargeInterface::class, $updatedShipmentSurcharge);
        $this->assertEquals('updated name', $updatedShipmentSurcharge->getName());
    }

    public function testItCannotUpdateAShipmentSurchargeWithoutAnId(): void
    {
        $shipmentSurcharge = new ShipmentSurcharge();
        $shipmentSurcharge->setName('updated name');

        $this->expectException(InvalidResourceException::class);
        $this->expectExceptionMessage(
            'Could not update shipment surcharge. This shipment surcharge does not have an id, use createShipmentSurcharge() to save it.',
        );
        $this->api->updateShipmentSurcharge($shipmentSurcharge);
    }

    public function testItDeletesAShipmentSurcharge(): void
    {
        $shipmentSurcharge = new ShipmentSurcharge();
        $shipmentSurcharge->setId('4afa3758-c425-48bc-96ef-ba43c5da4082');

        $this->assertTrue($this->api->deleteShipmentSurcharge($shipmentSurcharge));
    }

    public function testItCannotDeleteAShipmentSurchargeWithoutAnId(): void
    {
        $shipmentSurcharge = new ShipmentSurcharge();

        $this->expectException(InvalidResourceException::class);
        $this->expectExceptionMessage(
            'Could not delete shipment surcharge. This shipment surcharge does not have an id.',
        );
        $this->api->deleteShipmentSurcharge($shipmentSurcharge);
    }

    public function testGetShipmentSurchargesFromShipment()
    {
        $shipment = $this->api->getShipment('shipment-id-1');

        $shipmentSurcharges = $shipment->getShipmentSurcharges();
        $this->assertCount(2, $shipmentSurcharges);

        $shipmentSurcharge = $shipmentSurcharges[0];
        $this->assertInstanceOf(ShipmentSurchargeInterface::class, $shipmentSurcharge);
        $this->assertEquals('test', $shipmentSurcharge->getName());
        $this->assertEquals('desc', $shipmentSurcharge->getDescription());
        $this->assertEquals(123, $shipmentSurcharge->getFeeAmount());
        $this->assertEquals('ALL', $shipmentSurcharge->getFeeCurrency());
        $this->assertInstanceOf(ShipmentInterface::class, $shipmentSurcharge->getShipment());
        $this->assertEquals('shipment-id-1', $shipmentSurcharge->getShipment()->getId());

        $shipmentSurcharge = $shipmentSurcharges[1];
        $this->assertInstanceOf(ShipmentSurchargeInterface::class, $shipmentSurcharge);
        $this->assertEquals('asdasd', $shipmentSurcharge->getName());
        $this->assertNull($shipmentSurcharge->getDescription());
        $this->assertEquals(345, $shipmentSurcharge->getFeeAmount());
        $this->assertEquals('AMD', $shipmentSurcharge->getFeeCurrency());
        $this->assertInstanceOf(ShipmentInterface::class, $shipmentSurcharge->getShipment());
        $this->assertEquals('shipment-id-1', $shipmentSurcharge->getShipment()->getId());
    }
}
