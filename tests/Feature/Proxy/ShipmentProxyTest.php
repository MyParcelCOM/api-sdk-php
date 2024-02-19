<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Feature\Proxy;

use MyParcelCom\ApiSdk\Authentication\AuthenticatorInterface;
use MyParcelCom\ApiSdk\Enums\TaxTypeEnum;
use MyParcelCom\ApiSdk\MyParcelComApi;
use MyParcelCom\ApiSdk\MyParcelComApiInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\AddressInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\CollectionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\CustomsInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\FileInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\PhysicalPropertiesInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceOptionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentItemInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentStatusInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\ApiSdk\Resources\Proxy\ShipmentProxy;
use MyParcelCom\ApiSdk\Resources\TaxIdentificationNumber;
use MyParcelCom\ApiSdk\Tests\Traits\MocksApiCommunication;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;

class ShipmentProxyTest extends TestCase
{
    use MocksApiCommunication;

    /** @var ClientInterface */
    private $client;
    /** @var AuthenticatorInterface */
    private $authenticator;
    /** @var MyParcelComApiInterface */
    private $api;
    /** @var ShipmentProxy */
    private $shipmentProxy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->getClientMock();
        $this->authenticator = $this->getAuthenticatorMock();
        $this->api = (new MyParcelComApi('https://api', $this->client))
            ->setCache($this->getNullCache())
            ->authenticate($this->authenticator);

        $this->shipmentProxy = (new ShipmentProxy())
            ->setMyParcelComApi($this->api)
            ->setId('shipment-id-1');
    }

    /** @test */
    public function testAccessors()
    {
        $this->assertEquals(8794, $this->shipmentProxy->setPrice(8794)->getPrice());
        $this->assertEquals('AED', $this->shipmentProxy->setCurrency('AED')->getCurrency());
        $this->assertEquals('Tracking-code_134', $this->shipmentProxy->setTrackingCode('Tracking-code_134')->getTrackingCode());
        $this->assertEquals('https://I.Track.U/134', $this->shipmentProxy->setTrackingUrl('https://I.Track.U/134')->getTrackingUrl());
        $this->assertEquals('https://track.localhost.private/MyParcel.com%20Shop//1433%20DC', $this->shipmentProxy->setTrackingPageUrl('https://track.localhost.private/MyParcel.com%20Shop//1433%20DC')->getTrackingPageUrl());
        $this->assertEquals('Cartoon Network', $this->shipmentProxy->setChannel('Cartoon Network')->getChannel());
        $this->assertEquals('Something living with hair', $this->shipmentProxy->setDescription('Something living with hair')->getDescription());
        $this->assertEquals('#1234', $this->shipmentProxy->setCustomerReference('#1234')->getCustomerReference());
        $this->assertEquals('|||||||||||||', $this->shipmentProxy->setBarcode('|||||||||||||')->getBarcode());
        $this->assertEquals(12, $this->shipmentProxy->setWeight(12)->getWeight());
        $this->assertEquals('80-90A', $this->shipmentProxy->setPickupLocationCode('80-90A')->getPickupLocationCode());
        $this->assertEquals('an-id-for-a-shipment', $this->shipmentProxy->setId('an-id-for-a-shipment')->getId());

        /** @var ServiceInterface $service */
        $service = $this->getMockBuilder(ServiceInterface::class)->getMock();
        $this->assertEquals($service, $this->shipmentProxy->setService($service)->getService());

        /** @var ContractInterface $contract */
        $contract = $this->getMockBuilder(ContractInterface::class)->getMock();
        $this->assertEquals($contract, $this->shipmentProxy->setContract($contract)->getContract());

        /** @var ShipmentStatusInterface $shipmentStatus */
        $shipmentStatus = $this->getMockBuilder(ShipmentStatusInterface::class)->getMock();
        $this->assertEquals($shipmentStatus, $this->shipmentProxy->setShipmentStatus($shipmentStatus)->getShipmentStatus());

        /** @var PhysicalPropertiesInterface $physicalProperties */
        $physicalProperties = $this->getMockBuilder(PhysicalPropertiesInterface::class)->getMock();
        $this->assertEquals($physicalProperties, $this->shipmentProxy->setPhysicalProperties($physicalProperties)->getPhysicalProperties());

        /** @var ShopInterface $shop */
        $shop = $this->getMockBuilder(ShopInterface::class)->getMock();
        $this->assertEquals($shop, $this->shipmentProxy->setShop($shop)->getShop());

        /** @var CustomsInterface $customs */
        $customs = $this->getMockBuilder(CustomsInterface::class)->getMock();
        $this->assertEquals($customs, $this->shipmentProxy->setCustoms($customs)->getCustoms());

        /** @var AddressInterface $senderAddress */
        $senderAddress = $this->getMockBuilder(AddressInterface::class)->getMock();
        $this->assertEquals($senderAddress, $this->shipmentProxy->setSenderAddress($senderAddress)->getSenderAddress());

        /** @var AddressInterface $pickupLocationAddress */
        $pickupLocationAddress = $this->getMockBuilder(AddressInterface::class)->getMock();
        $this->assertEquals($pickupLocationAddress, $this->shipmentProxy->setPickupLocationAddress($pickupLocationAddress)->getPickupLocationAddress());

        /** @var AddressInterface $recipientAddress */
        $recipientAddress = $this->getMockBuilder(AddressInterface::class)->getMock();
        $this->assertEquals($recipientAddress, $this->shipmentProxy->setRecipientAddress($recipientAddress)->getRecipientAddress());

        /** @var AddressInterface $returnAddress */
        $returnAddress = $this->getMockBuilder(AddressInterface::class)->getMock();
        $this->assertEquals($returnAddress, $this->shipmentProxy->setReturnAddress($returnAddress)->getReturnAddress());

        $shipmentStatusBuilder = $this->getMockBuilder(ShipmentStatusInterface::class);
        /** @var ShipmentStatusInterface $shipmentStatusA */
        $shipmentStatusA = $shipmentStatusBuilder->getMock();
        /** @var ShipmentStatusInterface $shipmentStatusB */
        $shipmentStatusB = $shipmentStatusBuilder->getMock();
        $this->assertEquals(
            [$shipmentStatusA, $shipmentStatusB],
            $this->shipmentProxy->setStatusHistory([$shipmentStatusA, $shipmentStatusB])->getStatusHistory()
        );

        $serviceOptionBuilder = $this->getMockBuilder(ServiceOptionInterface::class);
        /** @var ServiceOptionInterface $serviceOptionA */
        $serviceOptionA = $serviceOptionBuilder->getMock();
        $this->assertEquals(
            [$serviceOptionA],
            $this->shipmentProxy->setServiceOptions([$serviceOptionA])->getServiceOptions()
        );
        /** @var ServiceOptionInterface $serviceOptionB */
        $serviceOptionB = $serviceOptionBuilder->getMock();
        $this->assertEquals(
            [$serviceOptionA, $serviceOptionB],
            $this->shipmentProxy->addServiceOption($serviceOptionB)->getServiceOptions()
        );

        $fileBuilder = $this->getMockBuilder(fileInterface::class);
        /** @var fileInterface $fileA */
        $fileA = $fileBuilder->getMock();
        $this->assertEquals(
            [$fileA],
            $this->shipmentProxy->setFiles([$fileA])->getFiles()
        );
        /** @var fileInterface $fileB */
        $fileB = $fileBuilder->getMock();
        $this->assertEquals(
            [$fileA, $fileB],
            $this->shipmentProxy->addFile($fileB)->getFiles()
        );
    }

    /** @test */
    public function testAttributes()
    {
        $this->assertEquals('shipment-id-1', $this->shipmentProxy->getId());
        $this->assertEquals(ResourceInterface::TYPE_SHIPMENT, $this->shipmentProxy->getType());
        $this->assertInstanceOf(AddressInterface::class, $this->shipmentProxy->getRecipientAddress());
        $this->assertEquals('Some road', $this->shipmentProxy->getRecipientAddress()->getStreet1());
        $this->assertEquals('1GL HF1', $this->shipmentProxy->getRecipientAddress()->getPostalCode());

        $taxIdentificationNumbers = $this->shipmentProxy->getRecipientTaxIdentificationNumbers();
        $this->assertCount(1, $taxIdentificationNumbers);
        array_walk($taxIdentificationNumbers, function ($taxIdentificationNumber) {
            $this->assertInstanceOf(TaxIdentificationNumber::class, $taxIdentificationNumber);
            $this->assertEquals(TaxTypeEnum::IOSS, $taxIdentificationNumber->getType());
            $this->assertEquals('IM2760000742', $taxIdentificationNumber->getNumber());
        });

        $this->assertInstanceOf(AddressInterface::class, $this->shipmentProxy->getSenderAddress());
        $this->assertEquals(17, $this->shipmentProxy->getSenderAddress()->getStreetNumber());
        $this->assertEquals('Cardiff', $this->shipmentProxy->getSenderAddress()->getCity());

        $taxIdentificationNumbers = $this->shipmentProxy->getSenderTaxIdentificationNumbers();
        $this->assertCount(1, $taxIdentificationNumbers);
        array_walk($taxIdentificationNumbers, function ($taxIdentificationNumber) {
            $this->assertInstanceOf(TaxIdentificationNumber::class, $taxIdentificationNumber);
            $this->assertEquals(TaxTypeEnum::EORI, $taxIdentificationNumber->getType());
            $this->assertEquals('XI123456789', $taxIdentificationNumber->getNumber());
        });

        $this->assertEquals('123456', $this->shipmentProxy->getPickupLocationCode());
        $this->assertInstanceOf(AddressInterface::class, $this->shipmentProxy->getPickupLocationAddress());
        $this->assertEquals('GB', $this->shipmentProxy->getPickupLocationAddress()->getCountryCode());
        $this->assertEquals('Doe', $this->shipmentProxy->getPickupLocationAddress()->getLastName());

        $this->assertEquals('Playstation 4', $this->shipmentProxy->setDescription('Playstation 4')->getDescription());
        $this->assertEquals('#1234', $this->shipmentProxy->setCustomerReference('#1234')->getCustomerReference());
        $this->assertEquals(50, $this->shipmentProxy->setPrice(50)->getPrice());
        $this->assertEquals('EUR', $this->shipmentProxy->getCurrency());
        $this->assertEquals('3SABCD0123456789', $this->shipmentProxy->getBarcode());
        $this->assertEquals('TR4CK1NGC0D3', $this->shipmentProxy->getTrackingCode());
        $this->assertEquals('https://track.me/TR4CK1NGC0D3', $this->shipmentProxy->getTrackingUrl());
        $this->assertEquals('https://track.localhost.private/MyParcel.com%20Shop//1433%20DC', $this->shipmentProxy->getTrackingPageUrl());
        $this->assertEquals(24, $this->shipmentProxy->getWeight());
        $this->assertEquals(600, $this->shipmentProxy->getTotalValueAmount());
        $this->assertEquals('GBP', $this->shipmentProxy->getTotalValueCurrency());

        $this->assertInstanceOf(PhysicalPropertiesInterface::class, $this->shipmentProxy->getPhysicalProperties());
        $this->assertEquals(24, $this->shipmentProxy->getPhysicalProperties()->getHeight());
        $this->assertEquals(50, $this->shipmentProxy->getPhysicalProperties()->getWidth());
        $this->assertEquals(50, $this->shipmentProxy->getPhysicalProperties()->getLength());
        $this->assertEquals(0.06, $this->shipmentProxy->getPhysicalProperties()->getVolume());
        $this->assertEquals(24, $this->shipmentProxy->getPhysicalProperties()->getWeight());
        $this->assertEquals(12, $this->shipmentProxy->getPhysicalProperties()->getVolumetricWeight());

        $customs = $this->shipmentProxy->getCustoms();
        $this->assertInstanceOf(CustomsInterface::class, $customs);
        $this->assertEquals(CustomsInterface::CONTENT_TYPE_DOCUMENTS, $customs->getContentType());

        $items = $this->shipmentProxy->getItems();
        $this->assertIsArray($items);
        array_walk($items, function ($item) {
            $this->assertInstanceOf(ShipmentItemInterface::class, $item);
        });

        $this->assertEquals('123456789', $items[0]->getSku());
        $this->assertEquals('OnePlus X', $items[0]->getDescription());
        $this->assertEquals(100, $items[0]->getItemValue());
        $this->assertEquals(128, $items[0]->getItemWeight());
        $this->assertEquals('mg', $items[0]->getItemWeightUnit());
        $this->assertEquals(2, $items[0]->getQuantity());
        $this->assertEquals('8517.12.00', $items[0]->getHsCode());
        $this->assertEquals('GB', $items[0]->getOriginCountryCode());
        $this->assertEquals(false, $items[0]->getIsPreferentialOrigin());

        $this->assertEquals('213425', $items[1]->getSku());
        $this->assertEquals('OnePlus One', $items[1]->getDescription());
        $this->assertEquals(200, $items[1]->getItemValue());
        $this->assertEquals(256, $items[1]->getItemWeight());
        $this->assertEquals('g', $items[1]->getItemWeightUnit());
        $this->assertEquals(3, $items[1]->getQuantity());
        $this->assertEquals('8517.12.00', $items[1]->getHsCode());
        $this->assertEquals('GB', $items[1]->getOriginCountryCode());
        $this->assertEquals(true, $items[1]->getIsPreferentialOrigin());

        $this->assertEquals('6876', $items[2]->getSku());
        $this->assertEquals('OnePlus Two', $items[2]->getDescription());
        $this->assertEquals(300, $items[2]->getItemValue());
        $this->assertEquals(512, $items[2]->getItemWeight());
        $this->assertEquals('kg', $items[2]->getItemWeightUnit());
        $this->assertEquals(1, $items[2]->getQuantity());
        $this->assertEquals('8517.12.00', $items[2]->getHsCode());
        $this->assertEquals('GB', $items[2]->getOriginCountryCode());
        $this->assertEquals(false, $items[2]->getIsPreferentialOrigin());
    }

    /** @test */
    public function testShopRelationship()
    {
        $this->assertInstanceOf(ShopInterface::class, $this->shipmentProxy->getShop());
        $this->assertEquals(ResourceInterface::TYPE_SHOP, $this->shipmentProxy->getShop()->getType());
        $this->assertEquals('shop-id-1', $this->shipmentProxy->getShop()->getId());
    }

    /** @test */
    public function testServiceRelationship()
    {
        $this->assertInstanceOf(ServiceInterface::class, $this->shipmentProxy->getService());
        $this->assertEquals(ResourceInterface::TYPE_SERVICE, $this->shipmentProxy->getService()->getType());
        $this->assertEquals('service-id-1', $this->shipmentProxy->getService()->getId());
    }

    /** @test */
    public function testContractRelationship()
    {
        $this->assertInstanceOf(ContractInterface::class, $this->shipmentProxy->getContract());
        $this->assertEquals(ResourceInterface::TYPE_CONTRACT, $this->shipmentProxy->getContract()->getType());
        $this->assertEquals('contract-id-1', $this->shipmentProxy->getContract()->getId());
    }

    /** @test */
    public function testServiceOptionsRelationship()
    {
        // getServiceOptions() should retrieve the options from the stub.
        $serviceOptions = $this->shipmentProxy->getServiceOptions();
        array_walk($serviceOptions, function (ServiceOptionInterface $option) {
            $this->assertInstanceOf(ServiceOptionInterface::class, $option);
            $this->assertEquals(ResourceInterface::TYPE_SERVICE_OPTION, $option->getType());
        });
        $serviceOptionIds = array_map(function (ServiceOptionInterface $serviceOption) {
            return $serviceOption->getId();
        }, $serviceOptions);
        $this->assertContains('service-option-id-1', $serviceOptionIds);

        // First setting the service options should overwrite
        // potential old service options.
        $serviceOption_A = $this->createMock(ServiceOptionInterface::class);
        $serviceOption_A
            ->method('getId')
            ->willReturn('service-option-id-2');
        $serviceOption_B = $this->createMock(ServiceOptionInterface::class);
        $serviceOption_B
            ->method('getId')
            ->willReturn('service-option-id-3');

        $serviceOptions = $this->shipmentProxy
            ->setServiceOptions([$serviceOption_A, $serviceOption_B])
            ->getServiceOptions();

        array_walk($serviceOptions, function (ServiceOptionInterface $option) {
            $this->assertInstanceOf(ServiceOptionInterface::class, $option);
        });
        $serviceOptionIds = array_map(function (ServiceOptionInterface $serviceOption) {
            return $serviceOption->getId();
        }, $serviceOptions);
        $this->assertEqualsCanonicalizing(['service-option-id-2', 'service-option-id-3'], $serviceOptionIds);
        $this->assertCount(2, $serviceOptions);

        // Adding a service option should keep the old service options
        // and add a new one to the array.
        $serviceOption_C = $this->createMock(ServiceOptionInterface::class);
        $serviceOptions = $this->shipmentProxy->addServiceOption($serviceOption_C)->getServiceOptions();

        $this->assertCount(3, $serviceOptions);
    }

    /** @test */
    public function testFilesRelationship()
    {
        $files = $this->shipmentProxy->getFiles();
        array_walk($files, function (FileInterface $file) {
            $this->assertInstanceOf(FileInterface::class, $file);
            $this->assertEquals(ResourceInterface::TYPE_FILE, $file->getType());
        });
        $fileIds = array_map(function (FileInterface $file) {
            return $file->getId();
        }, $files);
        $this->assertContains('file-id-1', $fileIds);

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

        $files = $this->shipmentProxy
            ->setFiles([$file_A, $file_B])
            ->getFiles();

        array_walk($files, function (FileInterface $file) {
            $this->assertInstanceOf(FileInterface::class, $file);
        });
        $fileIds = array_map(function (FileInterface $file) {
            return $file->getId();
        }, $files);
        $this->assertEqualsCanonicalizing(['file-id-2', 'file-id-3'], $fileIds);
        $this->assertCount(2, $files);

        // Adding a file should keep the old files
        // and add a new one to the array.
        $file_C = $this->createMock(FileInterface::class);
        $files = $this->shipmentProxy->addFile($file_C)->getFiles();

        $this->assertCount(3, $files);
    }

    /** @test */
    public function testStatusRelationship()
    {
        $status = $this->shipmentProxy->getShipmentStatus();
        $this->assertInstanceOf(ShipmentStatusInterface::class, $status);
        $this->assertEquals(ResourceInterface::TYPE_SHIPMENT_STATUS, $status->getType());
        $this->assertEquals('shipment-status-id-1', $status->getId());
    }

    /** @test */
    public function testCollectionRelationship()
    {
        $collection = $this->shipmentProxy->getCollection();
        $this->assertInstanceOf(CollectionInterface::class, $collection);
        $this->assertEquals(ResourceInterface::TYPE_COLLECTION, $collection->getType());
        $this->assertEquals('collection-id-1', $collection->getId());
    }

    /** @test */
    public function testStatusHistory()
    {
        $statusHistory = $this->shipmentProxy->getStatusHistory();
        $this->assertIsArray($statusHistory);
        array_walk($statusHistory, function (ShipmentStatusInterface $shipmentStatus) {
            $this->assertInstanceOf(ShipmentStatusInterface::class, $shipmentStatus);
            $this->assertEquals(ResourceInterface::TYPE_SHIPMENT_STATUS, $shipmentStatus->getType());
        });

        $this->assertCount(5, $statusHistory);
        $shipmentStatusIds = array_map(function (ShipmentStatusInterface $shipmentStatus) {
            return $shipmentStatus->getId();
        }, $statusHistory);

        $this->assertEqualsCanonicalizing([
            'shipment-status-id-1',
            'shipment-status-id-2',
            'shipment-status-id-3',
            'shipment-status-id-4',
            'shipment-status-id-5',
        ], $shipmentStatusIds);
    }

    /** @test */
    public function testClientCalls()
    {
        // Check if the uri has been called only once
        // while requesting multiple attributes.
        $firstProxy = new ShipmentProxy();
        $firstProxy
            ->setMyParcelComApi($this->api)
            ->setId('shipment-id-1');
        $firstProxy->getDescription();
        $firstProxy->getCurrency();
        $firstProxy->getSenderAddress();

        $this->assertEquals(1, $this->clientCalls['https://api/shipments/shipment-id-1']);

        // Creating a new proxy for the same resource will
        // change the amount of client calls to 2.
        $secondProxy = new ShipmentProxy();
        $secondProxy
            ->setMyParcelComApi($this->api)
            ->setId('shipment-id-1');
        $secondProxy->getFiles();

        $this->assertEquals(2, $this->clientCalls['https://api/shipments/shipment-id-1']);
    }

    /** @test */
    public function testRegisterAt()
    {
        $this->assertEquals(
            1337,
            $this->shipmentProxy->setRegisterAt(1337)->getRegisterAt()->getTimestamp()
        );
        $this->assertEquals(
            (new \DateTime('2019-11-04'))->getTimestamp(),
            $this->shipmentProxy->setRegisterAt('2019-11-04')->getRegisterAt()->getTimestamp()
        );
        $now = time();
        $this->assertEquals(
            $now,
            $this->shipmentProxy->setRegisterAt((new \DateTime())->setTimestamp($now))->getRegisterAt()->getTimestamp()
        );
    }

    /** @test */
    public function testJsonSerialize()
    {
        $shipmentProxy = new ShipmentProxy();
        $shipmentProxy
            ->setMyParcelComApi($this->api)
            ->setResourceUri('https://api/shipments/shipment-id-1')
            ->setId('shipment-id-1');

        $this->assertEquals([
            'id'   => 'shipment-id-1',
            'type' => ResourceInterface::TYPE_SHIPMENT,
        ], $shipmentProxy->jsonSerialize());
    }
}
