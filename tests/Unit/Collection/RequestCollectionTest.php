<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Unit\Collection;

use MyParcelCom\ApiSdk\Collection\CollectionInterface;
use MyParcelCom\ApiSdk\Collection\RequestCollection;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class RequestCollectionTest extends TestCase
{
    /** @var CollectionInterface */
    private $collection;

    /** @var int */
    private $pageNumber;

    /** @var int */
    private $pageSize;

    protected function setUp(): void
    {
        parent::setUp();

        $promiseCreator = function ($pageNumber, $pageSize) {
            $this->pageNumber = $pageNumber;
            $this->pageSize = $pageSize;

            $response = $this->createMock(ResponseInterface::class);
            $response->method('getBody')->willReturn('{"data": "something something", "meta": {"total_records": 123}}');

            return $response;
        };

        $resourceCreator = function ($data) {
            $this->assertEquals('something something', $data);

            $start = ($this->pageNumber - 1) * $this->pageSize;
            $resources = [];
            for ($n = $start; $n < 123 && $n < $start + $this->pageSize; $n++) {
                $resources[] = $this->createConfiguredMock(ResourceInterface::class, [
                    'getId' => (string) $n,
                ]);
            }

            return $resources;
        };

        $this->collection = new RequestCollection($promiseCreator, $resourceCreator);
    }

    /** @test */
    public function testGet()
    {
        $resources = $this->collection->offset(83)->limit(9)->get();
        $this->assertCount(9, $resources);
        $this->assertEquals(85, $resources[85]->getId());
    }

    /** @test */
    public function testForeach()
    {
        foreach ($this->collection as $resource) {
            $this->assertGreaterThanOrEqual(0, $resource->getId());
            $this->assertLessThanOrEqual(99, $resource->getId());
        }
    }

    /** @test */
    public function testOffsetAndLimit()
    {
        $resources = $this->collection->offset(73)->limit(6)->get();
        $this->assertCount(6, $resources);

        array_walk($resources, function ($resource) {
            $this->assertGreaterThanOrEqual(73, $resource->getId());
            $this->assertLessThanOrEqual(78, $resource->getId());
        });
    }

    /** @test */
    public function testMaxLimit()
    {
        $resources = $this->collection->offset(3)->limit(101)->get();
        $this->assertCount(100, $resources);

        array_walk($resources, function ($resource) {
            $this->assertGreaterThanOrEqual(3, $resource->getId());
            $this->assertLessThanOrEqual(103, $resource->getId());
        });
    }

    /** @test */
    public function testCount()
    {
        $this->assertEquals(123, $this->collection->count());
    }

    /** @test */
    public function testValid()
    {
        $this->assertTrue($this->collection->offset(65)->valid());
        $this->assertFalse($this->collection->offset(1512312)->valid());
    }

    /** @test */
    public function testKeyCurrentAndNext()
    {
        $this->assertEquals(99, $this->collection->offset(99)->key());

        $this->collection->next();
        $this->assertEquals(100, $this->collection->current()->getId());

        $this->assertNull($this->collection->offset(123541243)->current());
    }
}
