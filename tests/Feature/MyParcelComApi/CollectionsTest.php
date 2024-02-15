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
        $filters = [
            'shop'            => '1ebabb0e-9036-4259-b58e-2b42742bb86a',
            'collection_date' => '2024-02-17',
        ];

        $collections = $this->api->getCollections($filters);

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
}
