<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Feature\MyParcelComApi;

use MyParcelCom\ApiSdk\Collection\CollectionInterface;
use MyParcelCom\ApiSdk\Exceptions\InvalidResourceException;
use MyParcelCom\ApiSdk\MyParcelComApiInterface;
use MyParcelCom\ApiSdk\Resources\Address;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\ApiSdk\Resources\PhysicalProperties;
use MyParcelCom\ApiSdk\Resources\Shipment;
use MyParcelCom\ApiSdk\Resources\Shop;
use Psr\Http\Message\RequestInterface;
use MyParcelCom\ApiSdk\Tests\TestCase;

/**
 * @group Shipments
 */
class ShipmentsTest extends TestCase
{
    public function testCreateMinimumViableShipment(): void
    {
        $recipient = (new Address())
            ->setFirstName('Bobby')
            ->setLastName('Tables')
            ->setCity('Birmingham')
            ->setStreet1('Newbourne Hill')
            ->setStreetNumber(12)
            ->setPostalCode('B48 7QN')
            ->setCountryCode('GB');

        // Minimum required data should be recipient address and weight. All other data should be filled with defaults.
        $shipment = (new Shipment())
            ->setWeight(500)
            ->setRecipientAddress($recipient);

        $shipment = $this->api->createShipment($shipment);

        $this->assertNull($shipment->getService());
        $this->assertNull($shipment->getContract());
        $this->assertNotNull(
            $shipment->getId(),
            'Once the shipment has been created, it should have an id'
        );
        $this->assertNotNull(
            $shipment->getPrice(),
            'Successfully created shipments should have a price'
        );
        $this->assertEquals(
            $this->api->getDefaultShop()->getSenderAddress(),
            $shipment->getSenderAddress(),
            'The shipment\'s sender address should default to the default shop\'s sender address'
        );
        $this->assertEquals(
            $recipient,
            $shipment->getRecipientAddress(),
            'The shipment\'s recipient address should not have changed'
        );
    }

    public function testCreateIdempotentShipment(): void
    {
        $idempotencyKey = 'a-shipment-identifier';

        $this->client
            ->method('sendRequest')
            ->willReturnCallback(function (RequestInterface $request) use ($idempotencyKey) {
                $requestIdempotencyKey = $request->getHeader(MyParcelComApiInterface::HEADER_IDEMPOTENCY_KEY)[0];
                $this->assertEquals($idempotencyKey, $requestIdempotencyKey);
            });

        $recipient = (new Address())
            ->setFirstName('Bobby')
            ->setLastName('Tables')
            ->setCity('Birmingham')
            ->setStreet1('Newbourne Hill')
            ->setStreetNumber(12)
            ->setPostalCode('B48 7QN')
            ->setCountryCode('GB');

        $shopMock = $this
            ->getMockBuilder(Shop::class)
            ->getMock();

        $senderAddress = (new Address())
            ->setFirstName('Bobby')
            ->setLastName('Tables')
            ->setCity('Birmingham')
            ->setStreet1('Newbourne Hill')
            ->setStreetNumber(12)
            ->setPostalCode('B48 7QN')
            ->setCountryCode('GB');

        $shopMock
            ->method('getSenderAddress')
            ->willReturn($senderAddress);

        // Minimum required data should be recipient address and weight. All other data should be filled with defaults.
        $shipment = (new Shipment())
            ->setPhysicalProperties((new PhysicalProperties())->setWeight(500))
            ->setShop($shopMock)
            ->setRecipientAddress($recipient);

        $this->api->createShipment($shipment, $idempotencyKey);
    }

    public function testCreateRegisteredShipment(): void
    {
        $recipient = (new Address())
            ->setFirstName('Sherlock')
            ->setLastName('Holmes')
            ->setCity('London')
            ->setStreet1('Baker Street')
            ->setStreetNumber(221)
            ->setPostalCode('NW1 6XE')
            ->setCountryCode('GB');

        $shipment = (new Shipment())
            ->setPhysicalProperties((new PhysicalProperties())->setWeight(500))
            ->setRecipientAddress($recipient)
            ->setServiceCode('myparcelcom-unstamped');

        $shipment = $this->api->createAndRegisterShipment($shipment);

        $this->assertNotNull($shipment->getService());
        $this->assertNotNull($shipment->getContract());
        $this->assertCount(1, $shipment->getFiles());
        $this->assertNotNull($shipment->getFiles()[0]->getBase64Data());
    }

    public function testSaveShipment(): void
    {
        $initialAddress = (new Address())
            ->setFirstName('Bobby')
            ->setLastName('Tables')
            ->setCity('Birmingham')
            ->setStreet1('Newbourne Hill')
            ->setStreetNumber(12)
            ->setPostalCode('B48 7QN')
            ->setCountryCode('GB');

        // Minimum required data should be recipient address and weight. All other data should be filled with defaults.
        $shipment = (new Shipment())
            ->setWeight(500)
            ->setRecipientAddress($initialAddress)
            ->setReturnAddress($initialAddress);

        $shipment = $this->api->saveShipment($shipment);

        $this->assertNull($shipment->getService());
        $this->assertNull($shipment->getContract());
        $this->assertNotNull(
            $shipment->getId(),
            'Once the shipment has been created, it should have an id'
        );
        $this->assertNotNull(
            $shipment->getPrice(),
            'Successfully created shipments should have a price'
        );
        $this->assertEquals(
            $this->api->getDefaultShop()->getSenderAddress(),
            $shipment->getSenderAddress(),
            'The shipment\'s sender address should default to the default shop\'s sender address'
        );
        $this->assertEquals(
            $initialAddress,
            $shipment->getRecipientAddress(),
            'The shipment\'s recipient address should not have changed'
        );

        $patchRecipient = (new Address())
            ->setFirstName('Schmidt')
            ->setLastName('Jenko')
            ->setCity('Funkytown')
            ->setStreet1('Jump street')
            ->setStreetNumber(21)
            ->setPostalCode('A48 7QN')
            ->setCountryCode('GB');

        $shipment->setRecipientAddress($patchRecipient);
        $shipment->setDescription('new patched description');

        // Save an existing shipment should patch it
        $shipment = $this->api->saveShipment($shipment);

        $this->assertEquals(
            $patchRecipient,
            $shipment->getRecipientAddress(),
            'patch replaced the recipient address'
        );
        $this->assertEquals(
            $initialAddress,
            $shipment->getReturnAddress(),
            'patch should not have replaced the return address'
        );
        $this->assertEquals('new patched description', $shipment->getDescription());
    }

    public function testCreateInvalidShipmentMissingRecipient(): void
    {
        $shipment = new Shipment();

        // Shipments with no recipient and weight, should cause the api to throw an exception.
        $this->expectException(InvalidResourceException::class);
        $this->api->createShipment($shipment);
    }

    public function testCreateInvalidShipmentMissingWeight(): void
    {
        $recipient = (new Address())
            ->setFirstName('Bobby')
            ->setLastName('Tables')
            ->setCity('Birmingham')
            ->setStreet1('Newbourne Hill')
            ->setStreetNumber(12)
            ->setPostalCode('B48 7QN')
            ->setCountryCode('GB');
        $shipment = (new Shipment())
            ->setRecipientAddress($recipient);

        // add recipient to test the exception in createShipment
        $this->expectException(InvalidResourceException::class);
        $this->api->createShipment($shipment);
    }

    public function testUpdateInvalidShipmentMissingId(): void
    {
        $shipment = new Shipment();

        $this->expectException(InvalidResourceException::class);
        $this->api->updateShipment($shipment);
    }

    public function testGetShipments()
    {
        $shipments = $this->api->getShipments();

        $this->assertInstanceOf(CollectionInterface::class, $shipments);
        foreach ($shipments as $shipment) {
            $this->assertInstanceOf(ShipmentInterface::class, $shipment);
        }
    }

    public function testGetShipmentsForShop()
    {
        $shops = $this->api->getShops();

        foreach ($shops as $shop) {
            $shipments = $this->api->getShipments($shop);

            $this->assertInstanceOf(CollectionInterface::class, $shipments);
            foreach ($shipments as $shipment) {
                $this->assertInstanceOf(ShipmentInterface::class, $shipment);
                $this->assertEquals($shop->getId(), $shipment->getShop()->getId());
                $this->assertEquals($shop->getType(), $shipment->getShop()->getType());
                $this->assertEquals($shop->getCreatedAt(), $shipment->getShop()->getCreatedAt());
                $this->assertEquals($shop->getSenderAddress(), $shipment->getShop()->getSenderAddress());
                $this->assertEquals($shop->getReturnAddress(), $shipment->getShop()->getReturnAddress());
                $this->assertEquals($shop->getName(), $shipment->getShop()->getName());
            }
        }
    }

    public function testGetShipment()
    {
        $shipments = $this->api->getShipments();

        foreach ($shipments as $shipment) {
            $this->assertEquals($shipment, $this->api->getShipment($shipment->getId()));
        }
    }
}
