<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Feature\MyParcelComApi;

use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentStatusInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\StatusInterface;
use MyParcelCom\ApiSdk\Tests\TestCase;

/**
 * @group Shipments
 */
class ShipmentStatusesTest extends TestCase
{
    public function testGetShipmentStatus()
    {
        $shipment = $this->api->getShipment('shipment-id-1');

        $shipmentStatus = $shipment->getShipmentStatus();
        $carrierStatuses = $shipmentStatus->getCarrierStatuses();

        $this->assertInstanceOf(ShipmentStatusInterface::class, $shipmentStatus);
        $this->assertCount(1, $carrierStatuses);
        $this->assertEquals('9001', $carrierStatuses[0]->getCode());
        $this->assertEquals('Confirmed at destination', $carrierStatuses[0]->getDescription());
        $this->assertEquals(1504801719, $carrierStatuses[0]->getAssignedAt()->getTimestamp());

        $status = $shipmentStatus->getStatus();

        $this->assertInstanceOf(StatusInterface::class, $status);
        $this->assertEquals('shipment-delivered', $status->getCode());
        $this->assertEquals('success', $status->getLevel());
        $this->assertEquals('Delivered', $status->getName());
        $this->assertEquals('The shipment has been delivered', $status->getDescription());
    }

    public function testGetShipmentStatusHistory()
    {
        $shipment = $this->api->getShipment('shipment-id-1');

        $shipmentStatuses = $shipment->getStatusHistory();
        $this->assertCount(5, $shipmentStatuses);

        $shipmentStatus = $shipmentStatuses[0];
        $carrierStatuses = $shipmentStatus->getCarrierStatuses();
        $this->assertInstanceOf(ShipmentStatusInterface::class, $shipmentStatus);
        $this->assertEquals(1504801719, $shipmentStatus->getCreatedAt()->getTimestamp());
        $this->assertCount(1, $carrierStatuses);
        $this->assertEquals('9001', $carrierStatuses[0]->getCode());
        $this->assertEquals('Confirmed at destination', $carrierStatuses[0]->getDescription());
        $this->assertEquals(1504801719, $carrierStatuses[0]->getAssignedAt()->getTimestamp());

        $status = $shipmentStatus->getStatus();
        $this->assertInstanceOf(StatusInterface::class, $status);
        $this->assertEquals('shipment-delivered', $status->getCode());
        $this->assertEquals('success', $status->getLevel());
        $this->assertEquals('Delivered', $status->getName());
        $this->assertEquals('The shipment has been delivered', $status->getDescription());

        $shipmentStatus = $shipmentStatuses[1];
        $carrierStatuses = $shipmentStatus->getCarrierStatuses();
        $this->assertInstanceOf(ShipmentStatusInterface::class, $shipmentStatus);
        $this->assertEquals(1504701719, $shipmentStatus->getCreatedAt()->getTimestamp());
        $this->assertCount(2, $carrierStatuses);
        $this->assertEquals('4567-1', $carrierStatuses[0]->getCode());
        $this->assertEquals('Delivery moved a couple of meters', $carrierStatuses[0]->getDescription());
        $this->assertEquals(1504701750, $carrierStatuses[0]->getAssignedAt()->getTimestamp());
        $this->assertEquals('4567', $carrierStatuses[1]->getCode());
        $this->assertEquals('Delivery on it\'s way', $carrierStatuses[1]->getDescription());
        $this->assertEquals(1504701719, $carrierStatuses[1]->getAssignedAt()->getTimestamp());

        $status = $shipmentStatus->getStatus();
        $this->assertInstanceOf(StatusInterface::class, $status);
        $this->assertEquals('shipment-with-courier', $status->getCode());
        $this->assertEquals('success', $status->getLevel());
        $this->assertEquals('At courier', $status->getName());
        $this->assertEquals('The shipment is at the courier', $status->getDescription());

        $shipmentStatus = $shipmentStatuses[2];
        $carrierStatuses = $shipmentStatus->getCarrierStatuses();
        $this->assertInstanceOf(ShipmentStatusInterface::class, $shipmentStatus);
        $this->assertEquals(1504501720, $shipmentStatus->getCreatedAt()->getTimestamp());
        $this->assertCount(1, $carrierStatuses);
        $this->assertEquals('10001', $carrierStatuses[0]->getCode());
        $this->assertEquals('Parcel received', $carrierStatuses[0]->getDescription());
        $this->assertEquals(1504501719, $carrierStatuses[0]->getAssignedAt()->getTimestamp());

        $status = $shipmentStatus->getStatus();
        $this->assertInstanceOf(StatusInterface::class, $status);
        $this->assertEquals('shipment-received-by-carrier', $status->getCode());
        $this->assertEquals('success', $status->getLevel());
        $this->assertEquals('At carrier', $status->getName());
        $this->assertEquals('The shipment is at the carrier', $status->getDescription());

        $shipmentStatus = $shipmentStatuses[3];
        $carrierStatuses = $shipmentStatus->getCarrierStatuses();
        $this->assertInstanceOf(ShipmentStatusInterface::class, $shipmentStatus);
        $this->assertEquals(1504101722, $shipmentStatus->getCreatedAt()->getTimestamp());
        $this->assertCount(1, $carrierStatuses);
        $this->assertEquals('10000', $carrierStatuses[0]->getCode());
        $this->assertEquals('Pre-alert confirmed', $carrierStatuses[0]->getDescription());
        $this->assertEquals(1504101719, $carrierStatuses[0]->getAssignedAt()->getTimestamp());

        $status = $shipmentStatus->getStatus();
        $this->assertInstanceOf(StatusInterface::class, $status);
        $this->assertEquals('shipment-registered', $status->getCode());
        $this->assertEquals('success', $status->getLevel());
        $this->assertEquals('Registered', $status->getName());
        $this->assertEquals('The shipment has been registered at the carrier', $status->getDescription());

        $shipmentStatus = $shipmentStatuses[4];
        $carrierStatuses = $shipmentStatus->getCarrierStatuses();
        $this->assertInstanceOf(ShipmentStatusInterface::class, $shipmentStatus);
        $this->assertEquals(1504101718, $shipmentStatus->getCreatedAt()->getTimestamp());
        $this->assertCount(1, $carrierStatuses);
        $this->assertEquals('00000', $carrierStatuses[0]->getCode());
        $this->assertEquals('Pre-alerted', $carrierStatuses[0]->getDescription());
        $this->assertEquals(1504101718, $carrierStatuses[0]->getAssignedAt()->getTimestamp());

        $status = $shipmentStatus->getStatus();
        $this->assertInstanceOf(StatusInterface::class, $status);
        $this->assertEquals('shipment-concept', $status->getCode());
        $this->assertEquals('concept', $status->getLevel());
        $this->assertEquals('Concept', $status->getName());
        $this->assertEquals('The shipment is a concept', $status->getDescription());
    }}
