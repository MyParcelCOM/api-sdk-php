<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Feature\MyParcelComApi;

use DateTime;
use MyParcelCom\ApiSdk\Resources\Address;
use MyParcelCom\ApiSdk\Resources\Interfaces\FileInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\ApiSdk\Tests\TestCase;

/**
 * @group Carriers
 */
class ResourcesTest extends TestCase
{
    public function testGetResourceById()
    {
        /** @var FileInterface $file */
        $file = $this->api->getResourceById(ResourceInterface::TYPE_FILE, 'file-id-1');
        $this->assertInstanceOf(FileInterface::class, $file);
        $this->assertEquals('files', $file->getType());
        $this->assertEquals('file-id-1', $file->getId());
        $this->assertEquals('label', $file->getDocumentType());
        $this->assertEquals([['extension' => 'pdf', 'mime_type' => 'application/pdf']], $file->getFormats());

        /** @var ShopInterface $shop */
        $shop = $this->api->getResourceById(ResourceInterface::TYPE_SHOP, 'shop-id-1');
        $this->assertInstanceOf(ShopInterface::class, $shop);
        $this->assertEquals('shops', $shop->getType());
        $this->assertEquals('shop-id-1', $shop->getId());
        $this->assertEquals('Testshop', $shop->getName());
        $this->assertEquals((new DateTime())->setTimestamp(1509378904), $shop->getCreatedAt());
        $this->assertEquals(
            (new Address())
                ->setStreet1('Hoofdweg')
                ->setStreetNumber(679)
                ->setPostalCode('1AA BB2')
                ->setCity('London')
                ->setCountryCode('GB')
                ->setFirstName('Mister')
                ->setLastName('Sender')
                ->setCompany('MyParcel.com')
                ->setEmail('info@myparcel.com')
                ->setPhoneNumber('+31 85 208 5997'),
            $shop->getSenderAddress()
        );
        $this->assertEquals(
            (new Address())
                ->setStreet1('Hoofdweg')
                ->setStreetNumber(679)
                ->setPostalCode('1AA BB2')
                ->setCity('London')
                ->setCountryCode('GB')
                ->setFirstName('Mister')
                ->setLastName('Return')
                ->setCompany('MyParcel.com')
                ->setEmail('info@myparcel.com')
                ->setPhoneNumber('+31 85 208 5997'),
            $shop->getReturnAddress()
        );
    }
}
