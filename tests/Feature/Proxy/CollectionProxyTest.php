<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Feature\Proxy;

use MyParcelCom\ApiSdk\MyParcelComApi;
use MyParcelCom\ApiSdk\MyParcelComApiInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\AddressInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\CollectionTimeInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\FileInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ManifestInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\StatusInterface;
use MyParcelCom\ApiSdk\Resources\Proxy\CollectionProxy;
use MyParcelCom\ApiSdk\Tests\Traits\MocksApiCommunication;
use PHPUnit\Framework\TestCase;

class CollectionProxyTest extends TestCase
{
    use MocksApiCommunication;

    private MyParcelComApiInterface $api;
    private CollectionProxy $collectionProxy;

    protected function setUp(): void
    {
        parent::setUp();

        $client = $this->getClientMock();
        $authenticator = $this->getAuthenticatorMock();
        $this->api = (new MyParcelComApi('https://api', $client))
            ->setCache($this->getNullCache())
            ->authenticate($authenticator);

        $this->collectionProxy = (new CollectionProxy())
            ->setMyParcelComApi($this->api)
            ->setId('8d8d63aa-032b-4674-990b-706551a2bf23');
    }

    public function testAccessors(): void
    {
        $this->assertEquals(
            'Collection description',
            $this->collectionProxy->setDescription('Collection description')->getDescription()
        );
        $this->assertEquals(
            'an-id-for-a-collection',
            $this->collectionProxy->setId('an-id-for-a-collection')->getId()
        );

        /** @var AddressInterface $senderAddress */
        $senderAddress = $this->getMockBuilder(AddressInterface::class)->getMock();
        $this->assertEquals(
            $senderAddress,
            $this->collectionProxy->setAddress($senderAddress)->getAddress()
        );

        $this->assertFalse($this->collectionProxy->setRegister(false)->getRegister());

        $collectionTimeMock = $this->createMock(CollectionTimeInterface::class);
        $this->assertEquals(
            $collectionTimeMock,
            $this->collectionProxy->setCollectionTime($collectionTimeMock)->getCollectionTime()
        );

        $this->assertEquals(
            'some-tracking-code',
            $this->collectionProxy->setTrackingCode('some-tracking-code')->getTrackingCode()
        );

        $contractMock = $this->createMock(ContractInterface::class);
        $this->assertEquals(
            $contractMock,
            $this->collectionProxy->setContract($contractMock)->getContract()
        );

        $fileBuilder = $this->getMockBuilder(FileInterface::class);
        /** @var FileInterface $fileA */
        $fileA = $fileBuilder->getMock();
        $this->assertEquals(
            [$fileA],
            $this->collectionProxy->setFiles([$fileA])->getFiles(),
        );
        /** @var FileInterface $fileB */
        $fileB = $fileBuilder->getMock();
        $this->assertEquals(
            [$fileA, $fileB],
            $this->collectionProxy->addFile($fileB)->getFiles(),
        );

        $shipmentBuilder = $this->getMockBuilder(ShipmentInterface::class);
        /** @var ShipmentInterface $shipmentA */
        $shipmentA = $shipmentBuilder->getMock();
        $this->assertEquals(
            [$shipmentA],
            $this->collectionProxy->setShipments([$shipmentA])->getShipments(),
        );
        /** @var ShipmentInterface $shipmentB */
        $shipmentB = $shipmentBuilder->getMock();
        $this->assertEquals(
            [$shipmentA, $shipmentB],
            $this->collectionProxy->addShipment($shipmentB)->getShipments(),
        );

        $shopMock = $this->createMock(ShopInterface::class);
        $this->assertEquals(
            $shopMock,
            $this->collectionProxy->setShop($shopMock)->getShop()
        );

        $statusMock = $this->createMock(StatusInterface::class);
        $this->assertEquals(
            $statusMock,
            $this->collectionProxy->setStatus($statusMock)->getStatus()
        );
    }

    public function testAttributes(): void
    {
        $this->assertEquals('Test', $this->collectionProxy->getDescription());

        $this->assertInstanceOf(AddressInterface::class, $this->collectionProxy->getAddress());
        $this->assertEquals('200 Westminster Bridge Rd', $this->collectionProxy->getAddress()->getStreet1());
        $this->assertEquals('SE1 7UT', $this->collectionProxy->getAddress()->getPostalCode());
        $this->assertEquals('London', $this->collectionProxy->getAddress()->getCity());
        $this->assertEquals('GB', $this->collectionProxy->getAddress()->getCountryCode());
        $this->assertEquals('MP', $this->collectionProxy->getAddress()->getFirstName());
        $this->assertEquals('Sender', $this->collectionProxy->getAddress()->getLastName());

        $this->assertFalse($this->collectionProxy->getRegister());

        $this->assertInstanceOf(CollectionTimeInterface::class, $this->collectionProxy->getCollectionTime());
        $this->assertEquals(1708085160, $this->collectionProxy->getCollectionTime()->getFrom());
        $this->assertEquals(1708096680, $this->collectionProxy->getCollectionTime()->getTo());

        $this->assertEquals('TRK-1234567890', $this->collectionProxy->getTrackingCode());
    }

    public function testContractRelationship(): void
    {
        $this->assertInstanceOf(ContractInterface::class, $this->collectionProxy->getContract());
        $this->assertEquals(ResourceInterface::TYPE_CONTRACT, $this->collectionProxy->getContract()->getType());
        $this->assertEquals('be5c82fd-e936-440a-a5d8-9a3124d86fcf', $this->collectionProxy->getContract()->getId());
    }

    public function testShopRelationship(): void
    {
        $this->assertInstanceOf(ShopInterface::class, $this->collectionProxy->getShop());
        $this->assertEquals(ResourceInterface::TYPE_SHOP, $this->collectionProxy->getShop()->getType());
        $this->assertEquals('1ebabb0e-9036-4259-b58e-2b42742bb86a', $this->collectionProxy->getShop()->getId());
    }

    public function testStatusRelationship(): void
    {
        $this->assertInstanceOf(StatusInterface::class, $this->collectionProxy->getStatus());
        $this->assertEquals(ResourceInterface::TYPE_STATUS, $this->collectionProxy->getStatus()->getType());
        $this->assertEquals('583e311f-8f03-4b34-84dd-5a82305ce5a8', $this->collectionProxy->getStatus()->getId());
    }

    public function testManifestRelationship(): void
    {
        $this->assertInstanceOf(ManifestInterface::class, $this->collectionProxy->getManifest());
        $this->assertEquals(ResourceInterface::TYPE_MANIFEST, $this->collectionProxy->getManifest()->getType());
        $this->assertEquals('59b4daab-0b08-42ca-9b13-b13fb397af89', $this->collectionProxy->getManifest()->getId());
    }

    public function testFilesRelationship()
    {
        $files = $this->collectionProxy->getFiles();
        foreach ($files as $file) {
            $this->assertInstanceOf(FileInterface::class, $file);
            $this->assertEquals(ResourceInterface::TYPE_FILE, $file->getType());
        }
        $fileIds = array_map(fn (FileInterface $file) => $file->getId(), $files);
        $this->assertEqualsCanonicalizing([
            'cbf0973b-9bf6-49b9-a8c3-fd69a51a0457',
            '289928ba-76a7-4601-9af0-33c46c0abea4',
        ], $fileIds);

        // First setting the files should overwrite
        // potential old files.
        $file_A = $this->createMock(FileInterface::class);
        $file_A
            ->method('getId')
            ->willReturn('file-id-2');
        $file_B = $this->createMock(FileInterface::class);
        $file_B
            ->method('getId')
            ->willReturn('file-id-3');

        $files = $this->collectionProxy
            ->setFiles([$file_A, $file_B])
            ->getFiles();

        foreach ($files as $file) {
            $this->assertInstanceOf(FileInterface::class, $file);
        }
        $fileIds = array_map(fn (FileInterface $file) => $file->getId(), $files);
        $this->assertEqualsCanonicalizing(['file-id-2', 'file-id-3'], $fileIds);
        $this->assertCount(2, $files);

        // Adding a file should keep the old files
        // and add a new one to the array.
        $file_C = $this->createMock(FileInterface::class);
        $files = $this->collectionProxy->addFile($file_C)->getFiles();

        $this->assertCount(3, $files);
    }

    public function testShipmentsRelationship()
    {
        $shipments = $this->collectionProxy->getShipments();
        foreach ($shipments as $shipment) {
            $this->assertInstanceOf(ShipmentInterface::class, $shipment);
            $this->assertEquals(ResourceInterface::TYPE_SHIPMENT, $shipment->getType());
        }
        $shipmentIds = array_map(fn (ShipmentInterface $shipment) => $shipment->getId(), $shipments);
        $this->assertEqualsCanonicalizing([
            '65e10ca7-5e34-40da-9da5-928f8aa57f97',
            '872960ef-2a2c-4ab5-9a36-01478fd20276',
        ], $shipmentIds);

        // First setting the shipments should overwrite
        // potential old shipments.
        $shipment_A = $this->createMock(ShipmentInterface::class);
        $shipment_A
            ->method('getId')
            ->willReturn('shipment-id-2');
        $shipment_B = $this->createMock(ShipmentInterface::class);
        $shipment_B
            ->method('getId')
            ->willReturn('shipment-id-3');

        $shipments = $this->collectionProxy
            ->setShipments([$shipment_A, $shipment_B])
            ->getShipments();

        foreach ($shipments as $shipment) {
            $this->assertInstanceOf(ShipmentInterface::class, $shipment);
        }
        $shipmentIds = array_map(fn (ShipmentInterface $shipment) => $shipment->getId(), $shipments);
        $this->assertEqualsCanonicalizing(['shipment-id-2', 'shipment-id-3'], $shipmentIds);
        $this->assertCount(2, $shipments);

        // Adding a shipment should keep the old shipments
        // and add a new one to the array.
        $shipment_C = $this->createMock(ShipmentInterface::class);
        $shipments = $this->collectionProxy->addShipment($shipment_C)->getShipments();

        $this->assertCount(3, $shipments);
    }

    public function testJsonSerialize()
    {
        $collectionProxy = new CollectionProxy();
        $collectionProxy
            ->setMyParcelComApi($this->api)
            ->setResourceUri('https://api/collections/292d0a63-b1e6-465c-8064-2d78f164f8da')
            ->setId('292d0a63-b1e6-465c-8064-2d78f164f8da');

        $this->assertEquals([
            'id'   => '292d0a63-b1e6-465c-8064-2d78f164f8da',
            'type' => ResourceInterface::TYPE_COLLECTION,
        ], $collectionProxy->jsonSerialize());
    }
}
