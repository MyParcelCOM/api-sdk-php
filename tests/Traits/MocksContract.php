<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Traits;

use MyParcelCom\ApiSdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\PhysicalPropertiesInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceOptionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceRateInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;

trait MocksContract
{
    protected function getMockedServiceRate(
        array $serviceOptions = [],
        ?int $price = null,
        int $weightMin = 0,
        int $weightMax = 5000,
        ?int $fuelSurcharge = null,
        bool $isDynamic = false
    ): ServiceRateInterface {
        $dynamicServiceRateMock = $this->getMockBuilder(ServiceRateInterface::class)->getMock();
        $dynamicServiceRateMock->method('getPrice')->willReturn(intval($price) + 321);
        $dynamicServiceRateMock->method('getServiceOptions')->willReturn($serviceOptions);
        $dynamicServiceRateMock->method('getWeightMin')->willReturn($weightMin);
        $dynamicServiceRateMock->method('getWeightMax')->willReturn($weightMax);

        $mock = $this->getMockBuilder(ServiceRateInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $mock
            ->method('getPrice')
            ->willReturn($price);
        $mock
            ->method('getFuelSurchargeAmount')
            ->willReturn($fuelSurcharge);
        $mock
            ->method('getServiceOptions')
            ->willReturn($serviceOptions);
        $mock
            ->method('getWeightMin')
            ->willReturn($weightMin);
        $mock
            ->method('getWeightMax')
            ->willReturn($weightMax);
        $mock
            ->method('getWeightBracket')
            ->willReturn([]);
        $mock
            ->method('isDynamic')
            ->willReturn($isDynamic);
        $mock
            ->method('resolveDynamicRateForShipment')
            ->willReturn($dynamicServiceRateMock);

        return $mock;
    }

    protected function getMockedShipment(
        ?int $weight = 5000,
        ?ServiceInterface $service = null,
        array $serviceOptions = [],
        ?float $volumeInMm3 = null,
    ): ShipmentInterface {
        $physicalPropertiesMock = $this->getMockBuilder(PhysicalPropertiesInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $physicalPropertiesMock->method('getWeight')->willReturn($weight);

        $contractMock = $this->getMockBuilder(ContractInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $contractMock->method('getVolumetricWeightDivisorFactor')->willReturn(1.0);

        $shipment = $this->getMockBuilder(ShipmentInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $shipment->method('getPhysicalProperties')->willReturn($physicalPropertiesMock);
        $shipment->method('getContract')->willReturn($contractMock);
        $shipment->method('getServiceOptions')->willReturn($serviceOptions);
        if ($service) {
            $shipment->method('getService')->willReturn($service);
        }
        $shipment->method('calculateVolume')->willReturn($volumeInMm3);

        return $shipment;
    }

    protected function getMockedServiceOption(string $id, ?int $price = 0): ServiceOptionInterface
    {
        $mock = $this->getMockBuilder(ServiceOptionInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $mock
            ->method('getId')
            ->willReturn($id);
        $mock
            ->method('getPrice')
            ->willReturn($price);

        return $mock;
    }

    protected function getMockedService(
        array $serviceRates = [],
        string $deliveryMethod = 'delivery',
        bool $usesVolumetricWeight = false,
    ): ServiceInterface {
        $mock = $this->getMockBuilder(ServiceInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $mock
            ->method('getServiceRates')
            ->willReturn($serviceRates);
        $mock
            ->method('getDeliveryMethod')
            ->willReturn($deliveryMethod);
        $mock->method('usesVolumetricWeight')
            ->willReturn($usesVolumetricWeight);
        $mock->method('getVolumetricWeightDivisor')
            ->willReturn($usesVolumetricWeight ? 4000 : null);

        return $mock;
    }
}
