<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Unit\Resources;

use DateTime;
use MyParcelCom\ApiSdk\Resources\Interfaces\CarrierStatusInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ErrorInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\StatusInterface;
use MyParcelCom\ApiSdk\Resources\ShipmentStatus;
use PHPUnit\Framework\TestCase;

class ShipmentStatusTest extends TestCase
{
    /** @test */
    public function testId()
    {
        $shipmentStatus = new ShipmentStatus();
        $this->assertEquals('shipment-status-id', $shipmentStatus->setId('shipment-status-id')->getId());
    }

    /** @test */
    public function testType()
    {
        $shipmentStatus = new ShipmentStatus();
        $this->assertEquals('shipment-statuses', $shipmentStatus->getType());
    }

    /** @test */
    public function testCarrierStatus()
    {
        $carrierStatus = $this->getMockBuilder(CarrierStatusInterface::class)->getMock();

        $shipmentStatus = new ShipmentStatus();
        $this->assertEquals([$carrierStatus], $shipmentStatus->setCarrierStatuses([$carrierStatus])->getCarrierStatuses());
        $this->assertEquals(
            [
                $carrierStatus,
                $carrierStatus,
            ],
            $shipmentStatus->addCarrierStatus($carrierStatus)->getCarrierStatuses()
        );
    }

    /** @test */
    public function testErrors()
    {
        $error = $this->getMockBuilder(ErrorInterface::class)->getMock();

        $shipmentStatus = new ShipmentStatus();
        $this->assertEquals([$error, $error], $shipmentStatus->setErrors([$error, $error])->getErrors());
        $this->assertEquals(
            [
                $error,
                $error,
                $error,
            ],
            $shipmentStatus->addError($error)->getErrors()
        );
    }

    /** @test */
    public function testCreatedAt()
    {
        $shipmentStatus = new ShipmentStatus();
        $this->assertEquals((new DateTime())->setTimestamp(1504801719), $shipmentStatus->setCreatedAt(1504801719)->getCreatedAt());
        $this->assertEquals((new DateTime())->setTimestamp(1504801720), $shipmentStatus->setCreatedAt((new DateTime())->setTimestamp(1504801720))->getCreatedAt());
    }

    /** @test */
    public function testShipment()
    {
        $shipment = $this->getMockBuilder(ShipmentInterface::class)->getMock();

        $shipmentStatus = new ShipmentStatus();
        $this->assertEquals($shipment, $shipmentStatus->setShipment($shipment)->getShipment());
    }

    /** @test */
    public function testStatus()
    {
        $status = $this->getMockBuilder(StatusInterface::class)->getMock();

        $shipmentStatus = new ShipmentStatus();
        $this->assertEquals($status, $shipmentStatus->setStatus($status)->getStatus());
    }

    /** @test */
    public function testJsonSerialize()
    {
        $shipment = $this->getMockBuilder(ShipmentInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $shipment->method('jsonSerialize')
            ->willReturn([
                'id'   => 'shipment-id-1',
                'type' => 'shipments',
            ]);

        $carrierStatus = $this->getMockBuilder(CarrierStatusInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $carrierStatus->method('jsonSerialize')
            ->willReturn([
                'code'        => 'A01',
                'description' => 'We have received the shipment',
                'assigned_At' => 1504801719,
            ]);

        $status = $this->getMockBuilder(StatusInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $status->method('jsonSerialize')
            ->willReturn([
                'id'   => 'status-id-1',
                'type' => 'statuses',
            ]);

        $shipmentStatus = (new ShipmentStatus())
            ->setId('shipment-status-id')
            ->setCarrierStatuses([$carrierStatus])
            ->setCreatedAt(1504801799)
            ->setShipment($shipment)
            ->setStatus($status);

        $this->assertEquals([
            'id'            => 'shipment-status-id',
            'type'          => 'shipment-statuses',
            'attributes'    => [
                'carrier_statuses' => [
                    [
                        'code'        => 'A01',
                        'description' => 'We have received the shipment',
                        'assigned_At' => 1504801719,
                    ],
                ],
                'created_at'       => 1504801799,
            ],
            'relationships' => [
                'status'   => [
                    'data' => [
                        'id'   => 'status-id-1',
                        'type' => 'statuses',
                    ],
                ],
                'shipment' => [
                    'data' => [
                        'id'   => 'shipment-id-1',
                        'type' => 'shipments',
                    ],
                ],
            ],
        ], $shipmentStatus->jsonSerialize());
    }
}
