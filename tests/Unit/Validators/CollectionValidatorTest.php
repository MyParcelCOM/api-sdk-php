<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Unit\Validators;

use MyParcelCom\ApiSdk\Resources\Interfaces\AddressInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\CarrierInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\CollectionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\CollectionTimeInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\ApiSdk\Validators\CollectionValidator;
use PHPUnit\Framework\TestCase;

class CollectionValidatorTest extends TestCase
{
    private CollectionInterface $collection;
    private AddressInterface $address;
    private ShopInterface $shop;
    private CollectionTimeInterface $collectionTime;

    public function setUp(): void
    {
        parent::setUp();

        $this->address = $this->createMock(AddressInterface::class);
        $this->address->method('getStreet1')->willReturn('Street 1');
        $this->address->method('getCity')->willReturn('Amsterdam');
        $this->address->method('getCountryCode')->willReturn('NL');

        $this->shop = $this->createMock(ShopInterface::class);
        $this->shop->method('getId')->willReturn('shop-id');

        $this->collectionTime = $this->createMock(CollectionTimeInterface::class);
        $this->collectionTime->method('getFrom')->willReturn(1234567890);
        $this->collectionTime->method('getTo')->willReturn(1234567890);

        $this->collection = $this->createMock(CollectionInterface::class);
    }

    public function testMinimumCollectionIsValid(): void
    {
        $this->collection->method('getAddress')->willReturn($this->address);
        $this->collection->method('getShop')->willReturn($this->shop);
        $this->collection->method('getCollectionTime')->willReturn($this->collectionTime);

        $validator = new CollectionValidator($this->collection);

        $this->assertTrue($validator->isValid());
        $this->assertFalse($validator->hasErrors());
        $this->assertEmpty($validator->getErrors());
    }

    public function testCollectionWithoutAddressIsInvalid(): void
    {
        $this->collection->method('getAddress')->willReturn(null);
        $this->collection->method('getShop')->willReturn($this->shop);
        $this->collection->method('getCollectionTime')->willReturn($this->collectionTime);

        $validator = new CollectionValidator($this->collection);

        $this->assertFalse($validator->isValid());
        $this->assertNotEmpty($validator->getErrors());
        $this->assertEqualsCanonicalizing([
            'Attribute address.street_1 is required',
            'Attribute address.city is required',
            'Attribute address.country_code is required',
        ], $validator->getErrors());
    }

    public function testCollectionWithoutShopIsInvalid(): void
    {
        $this->collection->method('getAddress')->willReturn($this->address);
        $this->collection->method('getShop')->willReturn(null);
        $this->collection->method('getCollectionTime')->willReturn($this->collectionTime);

        $validator = new CollectionValidator($this->collection);

        $this->assertFalse($validator->isValid());
        $this->assertNotEmpty($validator->getErrors());
        $this->assertEquals('Attribute shop.id is required', $validator->getErrors()[0]);
    }

    public function testCollectionWithoutCollectionTimeIsInvalid(): void
    {
        $this->collection->method('getAddress')->willReturn($this->address);
        $this->collection->method('getShop')->willReturn($this->shop);
        $this->collection->method('getCollectionTime')->willReturn(null);

        $validator = new CollectionValidator($this->collection);

        $this->assertFalse($validator->isValid());
        $this->assertEqualsCanonicalizing([
            'Attribute collection_time.from is required',
            'Attribute collection_time.to is required',
        ], $validator->getErrors());
    }

    public function testContractCarrierShouldOfferCollections(): void
    {
        $contract = $this->createMock(ContractInterface::class);
        $carrier = $this->createMock(CarrierInterface::class);
        $carrier->method('getOffersCollections')->willReturn(false);
        $contract->method('getCarrier')->willReturn($carrier);

        $this->collection->method('getAddress')->willReturn($this->address);
        $this->collection->method('getShop')->willReturn($this->shop);
        $this->collection->method('getCollectionTime')->willReturn($this->collectionTime);
        $this->collection->method('getContract')->willReturn($contract);

        $validator = new CollectionValidator($this->collection);

        $this->assertFalse($validator->isValid());
        $this->assertEquals('The carrier of the contract does not offer collections', $validator->getErrors()[0]);
    }
}
