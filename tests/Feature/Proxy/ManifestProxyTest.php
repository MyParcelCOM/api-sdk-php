<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Feature\Proxy;

use MyParcelCom\ApiSdk\MyParcelComApi;
use MyParcelCom\ApiSdk\MyParcelComApiInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\AddressInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\BrokerInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\FileInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\OrganizationInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\ApiSdk\Resources\Proxy\ManifestProxy;
use MyParcelCom\ApiSdk\Tests\Traits\MocksApiCommunication;
use PHPUnit\Framework\TestCase;

class ManifestProxyTest extends TestCase
{
    use MocksApiCommunication;

    private MyParcelComApiInterface $api;
    /** @var ManifestProxy */
    private $manifestProxy;

    protected function setUp(): void
    {
        parent::setUp();

        $client = $this->getClientMock();
        $authenticator = $this->getAuthenticatorMock();
        $this->api = (new MyParcelComApi('https://api', $client))
            ->setCache($this->getNullCache())
            ->authenticate($authenticator);

        $this->manifestProxy = (new ManifestProxy())
            ->setMyParcelComApi($this->api)
            ->setId('b41dff15-efcf-4901-bd9f-6ed2f9d8ecc8');
    }

    /** @test */
    public function testAccessors()
    {
        $this->assertEquals('Manifest Name', $this->manifestProxy->setName('Manifest Name')->getName());
        $this->assertEquals('an-id-for-a-manifest', $this->manifestProxy->setId('an-id-for-a-manifest')->getId());

        /** @var AddressInterface $senderAddress */
        $senderAddress = $this->getMockBuilder(AddressInterface::class)->getMock();
        $this->assertEquals($senderAddress, $this->manifestProxy->setAddress($senderAddress)->getAddress());

        /** @var ContractInterface $contract */
        $contract = $this->getMockBuilder(ContractInterface::class)->getMock();
        $this->assertEquals($contract, $this->manifestProxy->setContract($contract)->getContract());

        $fileBuilder = $this->getMockBuilder(FileInterface::class);
        /** @var FileInterface $fileA */
        $fileA = $fileBuilder->getMock();
        $this->assertEquals(
            [$fileA],
            $this->manifestProxy->setFiles([$fileA])->getFiles(),
        );
        /** @var FileInterface $fileB */
        $fileB = $fileBuilder->getMock();
        $this->assertEquals(
            [$fileA, $fileB],
            $this->manifestProxy->addFile($fileB)->getFiles(),
        );

        /** @var BrokerInterface $shop */
        $broker = $this->getMockBuilder(BrokerInterface::class)->getMock();
        $this->assertEquals($broker, $this->manifestProxy->setOwner($broker)->getOwner());
        /** @var OrganizationInterface $shop */
        $organization = $this->getMockBuilder(OrganizationInterface::class)->getMock();
        $this->assertEquals($organization, $this->manifestProxy->setOwner($organization)->getOwner());
        /** @var ShopInterface $shop */
        $shop = $this->getMockBuilder(ShopInterface::class)->getMock();
        $this->assertEquals($shop, $this->manifestProxy->setOwner($shop)->getOwner());

        $shipmentBuilder = $this->getMockBuilder(ShipmentInterface::class);
        /** @var ShipmentInterface $shipmentA */
        $shipmentA = $shipmentBuilder->getMock();
        $this->assertEquals(
            [$shipmentA],
            $this->manifestProxy->setShipments([$shipmentA])->getShipments(),
        );
        /** @var ShipmentInterface $shipmentB */
        $shipmentB = $shipmentBuilder->getMock();
        $this->assertEquals(
            [$shipmentA, $shipmentB],
            $this->manifestProxy->addShipment($shipmentB)->getShipments(),
        );
    }

    /** @test */
    public function testAttributes()
    {
        $this->assertEquals('b41dff15-efcf-4901-bd9f-6ed2f9d8ecc8', $this->manifestProxy->getId());
        $this->assertEquals(ResourceInterface::TYPE_MANIFEST, $this->manifestProxy->getType());
        $this->assertEquals('The manifest name', $this->manifestProxy->getName());
        $this->assertInstanceOf(AddressInterface::class, $this->manifestProxy->getAddress());
        $this->assertEquals('Baker Street', $this->manifestProxy->getAddress()->getStreet1());
        $this->assertEquals('London', $this->manifestProxy->getAddress()->getCity());
        $this->assertEquals('GB', $this->manifestProxy->getAddress()->getCountryCode());
    }

    public function testContractRelationship()
    {
        $this->assertInstanceOf(ContractInterface::class, $this->manifestProxy->getContract());
        $this->assertEquals(ResourceInterface::TYPE_CONTRACT, $this->manifestProxy->getContract()->getType());
        $this->assertEquals('35d525bf-a49f-40b4-8b6a-b343598fcb74', $this->manifestProxy->getContract()->getId());
    }

    public function testOwnerRelationship()
    {
        $this->assertInstanceOf(ShopInterface::class, $this->manifestProxy->getOwner());
        $this->assertEquals(ResourceInterface::TYPE_SHOP, $this->manifestProxy->getOwner()->getType());
        $this->assertEquals('0685de92-4f11-4dbd-bccc-84373ee731b2', $this->manifestProxy->getOwner()->getId());
    }

    public function testFilesRelationship()
    {
        $files = $this->manifestProxy->getFiles();
        foreach ($files as $file) {
            $this->assertInstanceOf(FileInterface::class, $file);
            $this->assertEquals(ResourceInterface::TYPE_FILE, $file->getType());
        }
        $fileIds = array_map(fn (FileInterface $file) => $file->getId(), $files);
        $this->assertContains('a1784758-270c-4e72-8207-de72352759c5', $fileIds);

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

        $files = $this->manifestProxy
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
        $files = $this->manifestProxy->addFile($file_C)->getFiles();

        $this->assertCount(3, $files);
    }

    public function testShipmentsRelationship()
    {
        $shipments = $this->manifestProxy->getShipments();
        foreach ($shipments as $shipment) {
            $this->assertInstanceOf(ShipmentInterface::class, $shipment);
            $this->assertEquals(ResourceInterface::TYPE_SHIPMENT, $shipment->getType());
        }
        $shipmentIds = array_map(fn (ShipmentInterface $shipment) => $shipment->getId(), $shipments);
        $this->assertContains('62b3d1e1-d854-4e00-9fb7-ddbf47dd9db2', $shipmentIds);

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

        $shipments = $this->manifestProxy
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
        $shipments = $this->manifestProxy->addShipment($shipment_C)->getShipments();

        $this->assertCount(3, $shipments);
    }

    public function testItSetsAndGetsMetaProperties()
    {
        $this->assertNull($this->manifestProxy->getUpdatesShipmentStatuses());
        $this->assertEquals(true, $this->manifestProxy->setUpdatesShipmentStatuses(true)->getUpdatesShipmentStatuses());
    }

    public function testJsonSerialize()
    {
        $manifestProxy = new ManifestProxy();
        $manifestProxy
            ->setMyParcelComApi($this->api)
            ->setResourceUri('https://api/manifests/b41dff15-efcf-4901-bd9f-6ed2f9d8ecc8')
            ->setId('b41dff15-efcf-4901-bd9f-6ed2f9d8ecc8')
            ->setUpdatesShipmentStatuses(true);

        $this->assertEquals([
            'id'   => 'b41dff15-efcf-4901-bd9f-6ed2f9d8ecc8',
            'type' => ResourceInterface::TYPE_MANIFEST,
            'meta' => [
                'update_shipment_statuses' => true,
            ],
        ], $manifestProxy->jsonSerialize());
    }
}
