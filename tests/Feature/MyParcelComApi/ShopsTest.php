<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Feature\MyParcelComApi;

use MyParcelCom\ApiSdk\Collection\CollectionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\ApiSdk\Tests\TestCase;

/**
 * @group Shops
 */
class ShopsTest extends TestCase
{
    public function testGetShops()
    {
        $shops = $this->api->getShops();

        $this->assertInstanceOf(CollectionInterface::class, $shops);
        foreach ($shops as $shop) {
            $this->assertInstanceOf(ShopInterface::class, $shop);
        }
    }

    public function testGetDefaultShop()
    {
        $shop = $this->api->getDefaultShop();
        $this->assertInstanceOf(ShopInterface::class, $shop);
    }
}
