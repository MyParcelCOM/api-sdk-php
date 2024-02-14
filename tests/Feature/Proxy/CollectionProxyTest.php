<?php

declare(strict_types=1);

namespace Feature\Proxy;

use MyParcelCom\ApiSdk\MyParcelComApi;
use MyParcelCom\ApiSdk\MyParcelComApiInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\AddressInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\CollectionTimeInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\FileInterface;
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

        $this->assertFalse($this->collectionProxy->setRegistered(false)->getRegistered());

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
        $this->assertEquals('modi amet ut', $this->collectionProxy->getDescription());

        $this->assertInstanceOf(AddressInterface::class, $this->collectionProxy->getAddress());
        $this->assertEquals('Baker Street', $this->collectionProxy->getAddress()->getStreet1());
        $this->assertEquals('London', $this->collectionProxy->getAddress()->getCity());
        $this->assertEquals('GB', $this->collectionProxy->getAddress()->getCountryCode());

        $this->assertFalse($this->collectionProxy->getRegister());

        $this->assertInstanceOf(CollectionTimeInterface::class, $this->collectionProxy->getCollectionTime());
        $this->assertEquals(1708153200, $this->collectionProxy->getCollectionTime()->getFrom());
        $this->assertEquals(1708171200, $this->collectionProxy->getCollectionTime()->getTo());

        $this->assertEquals('TRK-1234567890', $this->collectionProxy->getTrackingCode());
    }

    public function testContractRelationship(): void
    {
        // TODO: Actually use a response stub with all relationships.
        $this->assertInstanceOf(ContractInterface::class, $this->collectionProxy->getContract());
        $this->assertEquals(ResourceInterface::TYPE_CONTRACT, $this->collectionProxy->getContract()->getType());
        $this->assertEquals('35d525bf-a49f-40b4-8b6a-b343598fcb74', $this->collectionProxy->getContract()->getId());
    }
}
