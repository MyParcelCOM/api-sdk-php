<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Unit\Resources;

use MyParcelCom\ApiSdk\Resources\Interfaces\CarrierInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceRateInterface;
use MyParcelCom\ApiSdk\Resources\Service;
use MyParcelCom\ApiSdk\Resources\ServiceRate;
use PHPUnit\Framework\TestCase;

class ServiceTest extends TestCase
{
    /** @test */
    public function testId()
    {
        $service = new Service();
        $this->assertEquals('service-id', $service->setId('service-id')->getId());
    }

    /** @test */
    public function testType()
    {
        $service = new Service();
        $this->assertEquals('services', $service->getType());
    }

    /** @test */
    public function testName()
    {
        $service = new Service();
        $this->assertEquals('Easy Delivery Service', $service->setName('Easy Delivery Service')->getName());
    }

    /** @test */
    public function testPackageType()
    {
        $service = new Service();
        $this->assertEquals(
            Service::PACKAGE_TYPE_PARCEL,
            $service->setPackageType(Service::PACKAGE_TYPE_PARCEL)->getPackageType(),
        );
    }

    /** @test */
    public function testTransitTimeMin()
    {
        $service = new Service();
        $this->assertEquals(5, $service->setTransitTimeMin(5)->getTransitTimeMin());
    }

    /** @test */
    public function testTransitTimeMax()
    {
        $service = new Service();
        $this->assertEquals(576, $service->setTransitTimeMax(576)->getTransitTimeMax());
    }

    /** @test */
    public function testCarrier()
    {
        $service = new Service();
        $carrier = $this->getMockBuilder(CarrierInterface::class)->getMock();

        $this->assertEquals($carrier, $service->setCarrier($carrier)->getCarrier());
    }

    /** @test */
    public function testItSetsAddsAndGetsServiceRates()
    {
        $service = new Service();

        $mockBuilder = $this->getMockBuilder(ServiceRateInterface::class);
        $serviceRates = [$mockBuilder->getMock(), $mockBuilder->getMock(), $mockBuilder->getMock()];

        $this->assertEquals($serviceRates, $service->setServiceRates($serviceRates)->getServiceRates());

        $serviceRate = $mockBuilder->getMock();
        $serviceRates[] = $serviceRate;
        $this->assertEquals($serviceRates, $service->addServiceRate($serviceRate)->getServiceRates());
    }

    /** @test */
    public function testItCallsTheServiceRatesCallbackWhenServiceRatesIsEmpty()
    {
        $service = new Service();

        $this->assertEmpty($service->getServiceRates());

        $serviceRates = [new ServiceRate(), new ServiceRate()];
        $service->setServiceRatesCallback(function () use ($serviceRates) {
            return $serviceRates;
        });

        $this->assertEquals($serviceRates, $service->getServiceRates());
    }

    /** @test */
    public function testHandoverMethod()
    {
        $service = new Service();

        $this->assertEquals('drop-off', $service->setHandoverMethod('drop-off')->getHandoverMethod());
    }

    /** @test */
    public function testDeliveryDays()
    {
        $service = new Service();

        $this->assertEmpty($service->getDeliveryDays());

        $this->assertEquals(['Thursday'], $service->addDeliveryDay('Thursday')->getDeliveryDays());
        $this->assertEquals(['Tuesday', 'Friday'], $service->setDeliveryDays(['Tuesday', 'Friday'])->getDeliveryDays());
        $this->assertEqualsCanonicalizing(
            ['Monday', 'Tuesday', 'Friday'],
            $service->addDeliveryDay('Monday')->getDeliveryDays(),
            'Monday should have been added to already existing Tuesday and Friday',
        );
    }

    /** @test */
    public function testDeliveryMethod()
    {
        $service = new Service();

        $this->assertEquals('pick-up', $service->setDeliveryMethod('pick-up')->getDeliveryMethod());
    }

    /** @test */
    public function testItIndicatesWhetherAServiceUsesVolumetricWeight()
    {
        $service = new Service();

        $service->setUsesVolumetricWeight(true);
        $this->assertTrue($service->usesVolumetricWeight());
    }

    /** @test */
    public function testJsonSerialize()
    {
        $carrier = $this->getMockBuilder(CarrierInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $carrier->method('jsonSerialize')
            ->willReturn([
                'id'   => 'carrier-id-1',
                'type' => 'carriers',
            ]);

        $service = (new Service())
            ->setId('service-id')
            ->setName('Easy Delivery Service')
            ->setPackageType(Service::PACKAGE_TYPE_PARCEL)
            ->setTransitTimeMin(7)
            ->setTransitTimeMax(14)
            ->setHandoverMethod('drop-off')
            ->setDeliveryDays(['Monday'])
            ->setCarrier($carrier)
            ->setRegionsFrom([
                [
                    'country_code' => 'GB',
                ],
            ])
            ->setRegionsTo([
                [
                    'country_code' => 'GB',
                    'postal_code'  => '^((GY|JE).*|TR2[1-5]) ?[0-9]{1}[A-Z]{2}$',
                ],
            ])
            ->setUsesVolumetricWeight(true);

        $this->assertEquals([
            'id'            => 'service-id',
            'type'          => 'services',
            'attributes'    => [
                'name'                   => 'Easy Delivery Service',
                'package_type'           => Service::PACKAGE_TYPE_PARCEL,
                'transit_time'           => [
                    'min' => 7,
                    'max' => 14,
                ],
                'handover_method'        => 'drop-off',
                'delivery_days'          => [
                    'Monday',
                ],
                'regions_from'           => [
                    [
                        'country_code' => 'GB',
                    ],
                ],
                'regions_to'             => [
                    [
                        'country_code' => 'GB',
                        'postal_code'  => '^((GY|JE).*|TR2[1-5]) ?[0-9]{1}[A-Z]{2}$',
                    ],
                ],
                'uses_volumetric_weight' => true,
            ],
            'relationships' => [
                'carrier'         => [
                    'data' => [
                        'id'   => 'carrier-id-1',
                        'type' => 'carriers',
                    ],
                ],
                'service_options' => [
                    'data' => [],
                ],
            ],
        ], $service->jsonSerialize());
    }
}
