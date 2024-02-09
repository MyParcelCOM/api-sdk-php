<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Feature\MyParcelComApi;

use MyParcelCom\ApiSdk\Collection\CollectionInterface;
use MyParcelCom\ApiSdk\Exceptions\InvalidResourceException;
use MyParcelCom\ApiSdk\Resources\Address;
use MyParcelCom\ApiSdk\Resources\Interfaces\ManifestInterface;
use MyParcelCom\ApiSdk\Resources\Manifest;
use MyParcelCom\ApiSdk\Resources\Organization;
use MyParcelCom\ApiSdk\Resources\Shipment;
use MyParcelCom\ApiSdk\Resources\Shop;
use MyParcelCom\ApiSdk\Tests\TestCase;

/**
 * @group Manifests
 */
class ManifestsTest extends TestCase
{
    public function testCreateMinimumViableManifest(): void
    {
        $address = (new Address())
            ->setCity('London')
            ->setStreet1('Baker Street')
            ->setCountryCode('GB');

        $owner = (new Shop())
            ->setId('0685de92-4f11-4dbd-bccc-84373ee731b2');

        $shipment = (new Shipment())
            ->setId('62b3d1e1-d854-4e00-9fb7-ddbf47dd9db2');

        // Minimum required attributes should be address and name
        $manifest = (new Manifest())
            ->setName('Manifest Name')
            ->setAddress($address)
            ->setOwner($owner)
            ->addShipment($shipment);

        $manifest = $this->api->createManifest($manifest);

        $this->assertNull($manifest->getContract());
        $this->assertNotNull($manifest->getId());
        $this->assertEquals('Manifest Name', $manifest->getName());
        $this->assertEquals($address, $manifest->getAddress());
    }

    public function testCreateManifestWithShopOwnerAddress()
    {
        $address = (new Address())
            ->setCity('London')
            ->setStreet1('Baker Street')
            ->setCountryCode('GB');
        $owner = (new Shop())
            ->setId('0685de92-4f11-4dbd-bccc-84373ee731b2')
            ->setSenderAddress($address);

        $shipment = (new Shipment())
            ->setId('62b3d1e1-d854-4e00-9fb7-ddbf47dd9db2');

        $manifest = (new Manifest())
            ->setName('Manifest Name')
            ->setOwner($owner)
            ->addShipment($shipment);

        $manifest = $this->api->createManifest($manifest);

        $this->assertNotNull($manifest->getId());
        $this->assertEquals('Manifest Name', $manifest->getName());
        $this->assertEquals($address, $manifest->getAddress());
    }

    public function testCreateInvalidManifestMissingAddress(): void
    {
        $this->expectException(InvalidResourceException::class);

        $owner = (new Organization())
            ->setId('0685de92-4f11-4dbd-bccc-84373ee731b2');

        $shipment = (new Shipment())
            ->setId('62b3d1e1-d854-4e00-9fb7-ddbf47dd9db2');

        $manifest = (new Manifest())
            ->setName('Manifest Name')
            ->setOwner($owner)
            ->addShipment($shipment);

        $this->api->createManifest($manifest);
    }

    public function testCreateInvalidManifestMissingName(): void
    {
        $this->expectException(InvalidResourceException::class);

        $address = (new Address())
            ->setCity('Birmingham')
            ->setStreet1('Newbourne Hill')
            ->setCountryCode('GB');

        $owner = (new Organization())
            ->setId('0685de92-4f11-4dbd-bccc-84373ee731b2');

        $shipment = (new Shipment())
            ->setId('62b3d1e1-d854-4e00-9fb7-ddbf47dd9db2');

        $manifest = (new Manifest())
            ->setAddress($address)
            ->setOwner($owner)
            ->addShipment($shipment);

        $this->api->createManifest($manifest);
    }

    public function testCreateManifestMissingOwner()
    {
        $this->expectException(InvalidResourceException::class);

        $address = (new Address())
            ->setCity('London')
            ->setStreet1('Baker Street')
            ->setCountryCode('GB');

        $shipment = (new Shipment())
            ->setId('62b3d1e1-d854-4e00-9fb7-ddbf47dd9db2');

        // Minimum required attributes should be address and name
        $manifest = (new Manifest())
            ->setName('Manifest Name')
            ->setAddress($address)
            ->addShipment($shipment);

        $this->api->createManifest($manifest);
    }

    public function testCreateManifestMissingShipments()
    {
        $this->expectException(InvalidResourceException::class);

        $address = (new Address())
            ->setCity('London')
            ->setStreet1('Baker Street')
            ->setCountryCode('GB');

        $owner = (new Shop())
            ->setId('0685de92-4f11-4dbd-bccc-84373ee731b2');

        // Minimum required attributes should be address and name
        $manifest = (new Manifest())
            ->setName('Manifest Name')
            ->setAddress($address)
            ->setOwner($owner);

        $this->api->createManifest($manifest);
    }

    public function testGetManifests(): void
    {
        $manifests = $this->api->getManifests();

        $this->assertInstanceOf(CollectionInterface::class, $manifests);
        foreach ($manifests as $manifest) {
            $this->assertInstanceOf(ManifestInterface::class, $manifest);
        }
    }

    public function testGetManifest(): void
    {
        $manifests = $this->api->getManifests();

        foreach ($manifests as $manifest) {
            $this->assertEquals($manifest, $this->api->getManifest($manifest->getId()));
        }
    }
}
