<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Feature\MyParcelComApi;

use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentSurchargeInterface;
use MyParcelCom\ApiSdk\Tests\TestCase;

/**
 * @group Shipments
 */
class ShipmentSurchargesTest extends TestCase
{
    public function testGetShipmentSurcharges()
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
        $this->assertEquals('shipment-id-1', $shipmentSurcharge->getShipment()->getId());

        $shipmentSurcharge = $shipmentSurcharges[1];
        $this->assertInstanceOf(ShipmentSurchargeInterface::class, $shipmentSurcharge);
        $this->assertEquals('asdasd', $shipmentSurcharge->getName());
        $this->assertNull($shipmentSurcharge->getDescription());
        $this->assertEquals(345, $shipmentSurcharge->getFeeAmount());
        $this->assertEquals('AMD', $shipmentSurcharge->getFeeCurrency());
        $this->assertEquals('shipment-id-1', $shipmentSurcharge->getShipment()->getId());
    }
}
