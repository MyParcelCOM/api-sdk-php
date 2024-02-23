<?php

declare(strict_types=1);

namespace Feature\MyParcelComApi;

use MyParcelCom\ApiSdk\Collection\CollectionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\RegionInterface;
use MyParcelCom\ApiSdk\Tests\TestCase;

/**
 * @group Regions
 */
class RegionsTest extends TestCase
{
    public function testGetRegions()
    {
        $shipments = $this->api->getRegions();

        $this->assertInstanceOf(CollectionInterface::class, $shipments);
        foreach ($shipments as $shipment) {
            $this->assertInstanceOf(RegionInterface::class, $shipment);
        }
    }

    public function testGetGbRegions()
    {
        $regions = $this->api->getRegions([
            'country_code' => 'GB',
        ]);

        $this->assertInstanceOf(CollectionInterface::class, $regions);
        $this->assertEquals(9, $regions->count());
        foreach ($regions as $region) {
            $this->assertInstanceOf(RegionInterface::class, $region);
            $this->assertEquals('GB', $region->getCountryCode());
        }

        $ireland = $this->api->getRegions([
            'country_code' => 'GB',
            'region_code'  => 'NIR',
        ]);

        $this->assertInstanceOf(CollectionInterface::class, $ireland);
        $this->assertEquals(1, $ireland->count());
        foreach ($ireland as $region) {
            $this->assertInstanceOf(RegionInterface::class, $region);
            $this->assertEquals('GB', $region->getCountryCode());
            $this->assertEquals('NIR', $region->getRegionCode());
        }
    }

    public function testGetRegionsWithNonExistingRegionCode()
    {
        $regions = $this->api->getRegions([
            'country_code' => 'NL',
            'region_code'  => 'NH',
        ]);

        $this->assertInstanceOf(CollectionInterface::class, $regions);
        $this->assertEquals(1, $regions->count());
        foreach ($regions as $region) {
            $this->assertInstanceOf(RegionInterface::class, $region);
            $this->assertEquals('NL', $region->getCountryCode());
        }
    }

    public function testItFiltersRegionsByPostalCode()
    {
        $regions = $this->api->getRegions([
            'country_code' => 'GB',
            'postal_code'  => 'NW1 6XE',
        ]);

        $this->assertInstanceOf(CollectionInterface::class, $regions);
        $this->assertEquals(1, $regions->count());
        foreach ($regions as $region) {
            $this->assertInstanceOf(RegionInterface::class, $region);
            $this->assertEquals('GB', $region->getCountryCode());
        }
    }}
