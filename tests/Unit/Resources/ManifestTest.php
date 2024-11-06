<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Unit\Resources;

use MyParcelCom\ApiSdk\Resources\Interfaces\AddressInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\BrokerInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\FileInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\OrganizationInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\ApiSdk\Resources\Manifest;
use PHPUnit\Framework\TestCase;

class ManifestTest extends TestCase
{
    public function testId(): void
    {
        $manifest = new Manifest();
        $this->assertEquals('manifest-id', $manifest->setId('manifest-id')->getId());
    }

    public function testType(): void
    {
        $manifest = new Manifest();
        $this->assertEquals('manifests', $manifest->getType());
    }

    public function testName(): void
    {
        $manifest = new Manifest();
        $this->assertEquals('Manifest Name', $manifest->setName('Manifest Name')->getName());
    }

    public function testAddress(): void
    {
        $manifest = new Manifest();
        $address = $this->getMockBuilder(AddressInterface::class)->getMock();

        $this->assertEquals($address, $manifest->setAddress($address)->getAddress());
    }

    public function testFiles(): void
    {
        $manifest = new Manifest();

        $this->assertEmpty($manifest->getFiles());

        $mockBuilder = $this->getMockBuilder(FileInterface::class);

        $files = [
            $mockBuilder->getMock(),
            $mockBuilder->getMock(),
        ];
        $manifest->setFiles($files);
        $this->assertCount(2, $manifest->getFiles());
        $this->assertEquals($files, $manifest->getFiles());

        $file = $mockBuilder->getMock();
        $manifest->addFile($file);
        $files[] = $file;
        $this->assertCount(3, $manifest->getFiles());
        $this->assertEquals($files, $manifest->getFiles());
    }

    public function testShipments(): void
    {
        $manifest = new Manifest();

        $this->assertEmpty($manifest->getShipments());

        $mockBuilder = $this->getMockBuilder(ShipmentInterface::class);

        $shipments = [
            $mockBuilder->getMock(),
            $mockBuilder->getMock(),
        ];
        $manifest->setShipments($shipments);
        $this->assertCount(2, $manifest->getShipments());
        $this->assertEquals($shipments, $manifest->getShipments());

        $shipment = $mockBuilder->getMock();
        $manifest->addShipment($shipment);
        $shipments[] = $shipment;
        $this->assertCount(3, $manifest->getShipments());
        $this->assertEquals($shipments, $manifest->getShipments());
    }

    public function testOwner(): void
    {
        $manifest = new Manifest();

        $shop = $this->getMockBuilder(ShopInterface::class)->getMock();
        $this->assertEquals($shop, $manifest->setOwner($shop)->getOwner());

        $organization = $this->getMockBuilder(OrganizationInterface::class)->getMock();
        $this->assertEquals($organization, $manifest->setOwner($organization)->getOwner());

        $broker = $this->getMockBuilder(BrokerInterface::class)->getMock();
        $this->assertEquals($broker, $manifest->setOwner($broker)->getOwner());
    }

    public function testUpdatesShipmentStatuses(): void
    {
        $manifest = new Manifest();
        $this->assertNull($manifest->getUpdatesShipmentStatuses());
        $this->assertFalse($manifest->setUpdatesShipmentStatuses(false)->getUpdatesShipmentStatuses());
        $this->assertTrue($manifest->setUpdatesShipmentStatuses(true)->getUpdatesShipmentStatuses());
    }
}
