<?php

declare(strict_types=1);

namespace Feature\MyParcelComApi;

use MyParcelCom\ApiSdk\Collection\CollectionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\CollectionInterface as MpCollectionInterface;
use MyParcelCom\ApiSdk\Tests\TestCase;

/**
 * @group Collections
 */
class CollectionsTest extends TestCase
{
    public function testItRetrievesCollections(): void
    {
        $collections = $this->api->getCollections();

        $this->assertInstanceOf(CollectionInterface::class, $collections);
        foreach ($collections as $collection) {
            $this->assertInstanceOf(MpCollectionInterface::class, $collection);
        }
    }
}
