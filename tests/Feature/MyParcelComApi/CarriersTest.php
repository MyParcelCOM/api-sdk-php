<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Feature\MyParcelComApi;

use MyParcelCom\ApiSdk\Collection\CollectionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\CarrierInterface;
use MyParcelCom\ApiSdk\Tests\TestCase;

/**
 * @group Carriers
 */
class CarriersTest extends TestCase
{
    public function testGetCarriers()
    {
        $carriers = $this->api->getCarriers();

        $this->assertInstanceOf(CollectionInterface::class, $carriers);
        $this->assertCount(2, $carriers);
        foreach ($carriers as $carrier) {
            $this->assertInstanceOf(CarrierInterface::class, $carrier);
            $this->assertNotEmpty($carrier->getName());
            $this->assertNotEmpty($carrier->getCode());
            $this->assertNotEmpty($carrier->getCredentialsFormat());
            $this->assertNotEmpty($carrier->getLabelMimeTypes());
        }
    }
}
