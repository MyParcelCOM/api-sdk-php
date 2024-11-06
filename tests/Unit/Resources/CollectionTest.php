<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Unit\Resources;

use MyParcelCom\ApiSdk\Resources\Collection;
use MyParcelCom\ApiSdk\Resources\Interfaces\AddressInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\CollectionTimeInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\FileInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\StatusInterface;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    public function testId(): void
    {
        $collection = new Collection();

        $this->assertEquals('collection-id', $collection->setId('collection-id')->getId());
    }

    public function testType(): void
    {
        $collection = new Collection();

        $this->assertEquals('collections', $collection->getType());
    }

    public function testDescription(): void
    {
        $collection = new Collection();

        $this->assertEquals(
            'collection-description',
            $collection->setDescription('collection-description')->getDescription()
        );
    }

    public function testAddress(): void
    {
        $collection = new Collection();
        $address = $this->createMock(AddressInterface::class);

        $this->assertEquals($address, $collection->setAddress($address)->getAddress());
    }

    public function testRegistered(): void
    {
        $collection = new Collection();

        $this->assertTrue($collection->setRegister(true)->getRegister());
    }

    public function testCollectionTime(): void
    {
        $collection = new Collection();
        $collectionTime = $this->createMock(CollectionTimeInterface::class);

        $this->assertEquals(
            $collectionTime,
            $collection->setCollectionTime($collectionTime)->getCollectionTime()
        );
    }

    public function testTrackingCode(): void
    {
        $collection = new Collection();

        $this->assertEquals(
            'tracking-code',
            $collection->setTrackingCode('tracking-code')->getTrackingCode()
        );
    }

    public function testContract(): void
    {
        $collection = new Collection();
        $contract = $this->createMock(ContractInterface::class);

        $this->assertEquals($contract, $collection->setContract($contract)->getContract());
    }

    public function testFiles(): void
    {
        $collection = new Collection();

        $this->assertEmpty($collection->getFiles());

        $mockBuilder = $this->getMockBuilder(FileInterface::class);

        $files = [
            $mockBuilder->getMock(),
            $mockBuilder->getMock(),
        ];
        $collection->setFiles($files);
        $this->assertCount(2, $collection->getFiles());
        $this->assertEquals($files, $collection->getFiles());

        $file = $mockBuilder->getMock();
        $collection->addFile($file);
        $files[] = $file;
        $this->assertCount(3, $collection->getFiles());
        $this->assertEquals($files, $collection->getFiles());
    }

    public function testShipments(): void
    {
        $collection = new Collection();

        $this->assertEmpty($collection->getShipments());

        $mockBuilder = $this->getMockBuilder(ShipmentInterface::class);

        $shipments = [
            $mockBuilder->getMock(),
            $mockBuilder->getMock(),
        ];
        $collection->setShipments($shipments);
        $this->assertCount(2, $collection->getShipments());
        $this->assertEquals($shipments, $collection->getShipments());

        $shipment = $mockBuilder->getMock();
        $collection->addShipment($shipment);
        $shipments[] = $shipment;
        $this->assertCount(3, $collection->getShipments());
        $this->assertEquals($shipments, $collection->getShipments());
    }

    public function testShop(): void
    {
        $collection = new Collection();
        $shop = $this->createMock(ShopInterface::class);

        $this->assertEquals($shop, $collection->setShop($shop)->getShop());
    }

    public function testStatus(): void
    {
        $collection = new Collection();
        $status = $this->createMock(StatusInterface::class);

        $this->assertEquals($status, $collection->setStatus($status)->getStatus());
    }
}
