<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Unit;

use MyParcelCom\ApiSdk\Resources\Contract;
use MyParcelCom\ApiSdk\Resources\Interfaces\CarrierInterface;
use PHPUnit\Framework\TestCase;

class ContractTest extends TestCase
{
    /** @test */
    public function testId()
    {
        $contract = new Contract();
        $this->assertEquals('contract-id', $contract->setId('contract-id')->getId());
    }

    /** @test */
    public function testType()
    {
        $contract = new Contract();
        $this->assertEquals('contracts', $contract->getType());
    }

    /** @test */
    public function testName()
    {
        $service = new Contract();
        $this->assertEquals('Test Contract', $service->setName('Test Contract')->getName());
    }

    /** @test */
    public function testCurrency()
    {
        $contract = new Contract();
        $this->assertEquals('ABC', $contract->setCurrency('ABC')->getCurrency());
    }

    /** @test */
    public function testCarrier()
    {
        $contract = new Contract();
        $carrier = $this->getMockBuilder(CarrierInterface::class)->getMock();

        $this->assertEquals($carrier, $contract->setCarrier($carrier)->getCarrier());
    }

    /** @test */
    public function testItSetsAndGetsStatus()
    {
        $contract = new Contract();
        $this->assertEquals('inactive', $contract->setStatus('inactive')->getStatus());
    }

    /** @test */
    public function testVolumetricWeightDivisorFactor()
    {
        $contract = new Contract();
        $this->assertEquals(1.0, $contract->getVolumetricWeightDivisorFactor());

        $contract->setVolumetricWeightDivisorFactor(1.1);
        $this->assertEquals(1.1, $contract->getVolumetricWeightDivisorFactor());
    }

    /** @test */
    public function testJsonSerialize()
    {
        $carrierMock = $this->getMockBuilder(CarrierInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $carrierMock->method('jsonSerialize')
            ->willReturn([
                'type' => 'carriers',
                'id'   => 'carrier-id',
            ]);

        $contract = (new Contract())
            ->setId('contract-id')
            ->setCurrency('IOU')
            ->setCarrier($carrierMock)
            ->setStatus('invalid')
            ->setVolumetricWeightDivisorFactor(1.1);

        $this->assertEquals([
            'id'            => 'contract-id',
            'type'          => 'contracts',
            'attributes'    => [
                'currency'                         => 'IOU',
                'status'                           => 'invalid',
                'volumetric_weight_divisor_factor' => 1.1,
            ],
            'relationships' => [
                'carrier' => [
                    'data' => [
                        'id'   => 'carrier-id',
                        'type' => 'carriers',
                    ],
                ],
            ],
        ], $contract->jsonSerialize());
    }
}
