<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Unit\Resources;

use MyParcelCom\ApiSdk\Resources\Interfaces\StatusInterface;
use MyParcelCom\ApiSdk\Resources\Status;
use PHPUnit\Framework\TestCase;

class StatusTest extends TestCase
{
    /** @test */
    public function testId()
    {
        $status = new Status();
        $this->assertEquals('status-id', $status->setId('status-id')->getId());
    }

    /** @test */
    public function testType()
    {
        $status = new Status();
        $this->assertEquals('statuses', $status->getType());
    }

    /** @test */
    public function testCode()
    {
        $status = new Status();
        $this->assertEquals('shipment-concept', $status->setCode('shipment-concept')->getCode());
    }

    /** @test */
    public function testResourceType()
    {
        $status = new Status();
        $this->assertEquals('shipments', $status->setResourceType('shipments')->getResourceType());
    }

    /** @test */
    public function testDescription()
    {
        $status = new Status();
        $this->assertEquals('The shipment is created', $status->setDescription('The shipment is created')->getDescription());
    }

    /** @test */
    public function testLevel()
    {
        $status = new Status();
        $this->assertEquals(StatusInterface::LEVEL_FAILED, $status->setLevel(StatusInterface::LEVEL_FAILED)->getLevel());
    }

    /** @test */
    public function testName()
    {
        $status = new Status();
        $this->assertEquals('Delivered', $status->setName('Delivered')->getName());
    }

    /** @test */
    public function testJsonSerialize()
    {
        $status = (new Status())
            ->setDescription('The shipment is created')
            ->setCode('shipment-concept')
            ->setId('status-id')
            ->setName('Delivered')
            ->setLevel(StatusInterface::LEVEL_FAILED)
            ->setResourceType('shipments');

        $this->assertEquals([
            'id'         => 'status-id',
            'type'       => 'statuses',
            'attributes' => [
                'description'   => 'The shipment is created',
                'code'          => 'shipment-concept',
                'resource_type' => 'shipments',
                'level'         => 'failed',
                'name'          => 'Delivered',
            ],
        ], $status->jsonSerialize());
    }
}
