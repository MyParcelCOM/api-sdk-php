<?php

declare(strict_types=1);

namespace Feature\MyParcelComApi;

use DateTime;
use MyParcelCom\ApiSdk\Collection\CollectionInterface as ResourceCollectionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\CollectionInterface;
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
}
