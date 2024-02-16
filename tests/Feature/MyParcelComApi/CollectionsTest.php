<?php

declare(strict_types=1);

namespace Feature\MyParcelComApi;

use DateTime;
use MyParcelCom\ApiSdk\Collection\CollectionInterface as ResourceCollectionInterface;
use MyParcelCom\ApiSdk\Exceptions\InvalidResourceException;
use MyParcelCom\ApiSdk\Resources\Address;
use MyParcelCom\ApiSdk\Resources\Collection;
use MyParcelCom\ApiSdk\Resources\CollectionTime;
use MyParcelCom\ApiSdk\Resources\Interfaces\CollectionInterface;
use MyParcelCom\ApiSdk\Resources\Shop;
use MyParcelCom\ApiSdk\Tests\TestCase;

/**
 * @group Collections
 */
class CollectionsTest extends TestCase
{
    public function testItRetrievesCollections(): void
    {
        $collections = $this->api->getCollections();

        $this->assertInstanceOf(ResourceCollectionInterface::class, $collections);
        $this->assertCount(3, $collections);
        foreach ($collections as $collection) {
            $this->assertInstanceOf(CollectionInterface::class, $collection);
        }
    }

    public function testItFiltersCollections(): void
    {
        $collections = $this->api->getCollections([
            'shop'            => '1ebabb0e-9036-4259-b58e-2b42742bb86a',
            'collection_date' => '2024-02-17',
        ]);

        $this->assertInstanceOf(ResourceCollectionInterface::class, $collections);
        $this->assertCount(2, $collections);

        foreach ($collections as $collection) {
            $this->assertInstanceOf(CollectionInterface::class, $collection);
        }

        $collectionDescriptions = array_map(
            fn (CollectionInterface $collection) => $collection->getDescription(),
            $collections->get()
        );

        $this->assertEqualsCanonicalizing([
            'Test collection 1',
            'Test collection 2',
        ], $collectionDescriptions);
    }

    public function testItRetrievesASingleCollection(): void
    {
        $collection = $this->api->getCollection('8d8d63aa-032b-4674-990b-706551a2bf23');

        $this->assertInstanceOf(CollectionInterface::class, $collection);
        $this->assertEquals('8d8d63aa-032b-4674-990b-706551a2bf23', $collection->getId());
        $this->assertEquals('Test', $collection->getDescription());
    }

    public function testItCreatesAMinimalCollection(): void
    {
        $newCollection = new Collection();
        $newCollection->setAddress(
            (new Address())
                ->setStreet1('Test street 1')
                ->setCity('Test city')
                ->setCountryCode('NL')
        );
        $newCollection->setCollectionTime(
            (new CollectionTime())->setFrom(123456789)->setTo(123456800)
        );
        $newCollection->setShop(
            (new Shop())->setId('1ebabb0e-9036-4259-b58e-2b42742bb86a')
        );

        $postedCollection = $this->api->createCollection($newCollection);

        $this->assertInstanceOf(CollectionInterface::class, $postedCollection);
        $this->assertEquals('77eda208-d081-49bf-9f74-cb9a98fc71f3', $postedCollection->getId());
    }

    public function testItFailsWhenNoCollectionTimeIsSet(): void
    {
        $newCollection = new Collection();
        $newCollection->setAddress(
            (new Address())
                ->setStreet1('Test street 1')
                ->setCity('Test city')
                ->setCountryCode('NL')
        );

        $newCollection->setShop(
            (new Shop())->setId('1ebabb0e-9036-4259-b58e-2b42742bb86a')
        );

        $this->expectException(InvalidResourceException::class);
        $this->expectExceptionMessage('This collection contains invalid data. Attribute collection_time.from is required. Attribute collection_time.to is required.');
        $this->api->createCollection($newCollection);
    }

    public function testItFailsWhenNoShopIsSet(): void
    {
        $newCollection = new Collection();
        $newCollection->setAddress(
            (new Address())
                ->setStreet1('Test street 1')
                ->setCity('Test city')
                ->setCountryCode('NL')
        );

        $newCollection->setCollectionTime(
            (new CollectionTime())->setFrom(123456789)->setTo(123456800)
        );

        $this->expectException(InvalidResourceException::class);
        $this->expectExceptionMessage('This collection contains invalid data. Attribute shop.id is required');
        $this->api->createCollection($newCollection);
    }

    public function testItDefaultsToShopAddressWhenNoAddressIsSet(): void
    {
        $shop = (new Shop())
            ->setId('1ebabb0e-9036-4259-b58e-2b42742bb86a')
            ->setSenderAddress(
                (new Address())
                    ->setStreet1('Test street 1')
                    ->setCity('Test city')
                    ->setCountryCode('NL')
            );

        $newCollection = new Collection();
        $newCollection->setShop($shop);
        $newCollection->setCollectionTime(
            (new CollectionTime())->setFrom(123456789)->setTo(123456800)
        );

        $createdCollection = $this->api->createCollection($newCollection);
        $this->assertInstanceOf(CollectionInterface::class, $createdCollection);
        $this->assertEquals('Test street 1', $createdCollection->getAddress()->getStreet1());
        $this->assertEquals('Test city', $createdCollection->getAddress()->getCity());
        $this->assertEquals('NL', $createdCollection->getAddress()->getCountryCode());
    }
}
