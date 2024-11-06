<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Unit\Shipments;

use MyParcelCom\ApiSdk\Exceptions\CalculationException;
use MyParcelCom\ApiSdk\Exceptions\InvalidResourceException;
use MyParcelCom\ApiSdk\Resources\Interfaces\PhysicalPropertiesInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\ApiSdk\Shipments\PriceCalculator;
use MyParcelCom\ApiSdk\Tests\Traits\MocksContract;
use PHPUnit\Framework\TestCase;

class PriceCalculatorTest extends TestCase
{
    use MocksContract;

    /** @test */
    public function testItCalculatesTheTotalPriceOfAShipment()
    {
        $serviceOptionMocks = [
            $this->getMockedServiceOption('service-option-id-uno', 250),
            $this->getMockedServiceOption('service-option-id-dos', 850),
        ];
        $serviceRateMock = $this->getMockedServiceRate($serviceOptionMocks, 5000, 0, 5000);
        $serviceMock = $this->getMockedService([$serviceRateMock]);
        $shipment = $this->getMockedShipment(1337, $serviceMock, $serviceOptionMocks);

        $this->assertEquals(6100, (new PriceCalculator())->calculate($shipment));
    }

    /** @test */
    public function testItCalculatesTheTotalPriceOfAShipmentWithVolumetricWeight()
    {
        $serviceRateMock = $this->getMockedServiceRate([], 333, 2001, 3000);
        $serviceMock = $this->getMockedService([$serviceRateMock], 'delivery', true);
        $shipment = $this->getMockedShipment(1500, $serviceMock, [], 8004000.0); // 8004000 / 4000 divisor = 2001 gram

        $this->assertEquals(333, (new PriceCalculator())->calculate($shipment));
    }

    /** @test */
    public function testItCalculatesTheTotalPriceOfAShipmentWithDynamicServiceRate()
    {
        $serviceRateMock = $this->getMockedServiceRate([], null, 0, 5000, null, true);
        $serviceMock = $this->getMockedService([$serviceRateMock]);
        $shipment = $this->getMockedShipment(1337, $serviceMock);

        $this->assertEquals(321, (new PriceCalculator())->calculate($shipment));
    }

    /** @test */
    public function testItCalculatesTheTotalPriceIncludingFuelSurcharge()
    {
        $serviceOptionMocks = [
            $this->getMockedServiceOption('service-option-id-uno', 250),
            $this->getMockedServiceOption('service-option-id-dos', 850),
        ];
        $serviceRateMock = $this->getMockedServiceRate($serviceOptionMocks, 5000, 0, 5000, 19);
        $serviceMock = $this->getMockedService([$serviceRateMock]);
        $shipment = $this->getMockedShipment(1337, $serviceMock, $serviceOptionMocks);

        $this->assertEquals(6119, (new PriceCalculator())->calculate($shipment));
    }

    /** @test */
    public function testItCalculatesTheOptionsPriceForAShipment()
    {
        $serviceOptionMocks = [
            $this->getMockedServiceOption('service-option-id-uno', 250),
            $this->getMockedServiceOption('service-option-id-dos', 850),
        ];
        $serviceRateMock = $this->getMockedServiceRate($serviceOptionMocks, 5000, 0, 5000);
        $serviceMock = $this->getMockedService([$serviceRateMock]);
        $shipment = $this->getMockedShipment(1337, $serviceMock, $serviceOptionMocks);

        $this->assertEquals(1100, (new PriceCalculator())->calculateOptionsPrice($shipment));
    }

    /** @test */
    public function testTheTotalPriceIsNullWhenOptionPriceIsNull()
    {
        $serviceOptionMocks = [
            $this->getMockedServiceOption('service-option-id-uno', 250),
            $this->getMockedServiceOption('service-option-id-dos', 400),
            $this->getMockedServiceOption('service-option-id-tres', null),
        ];
        $serviceRateMock = $this->getMockedServiceRate($serviceOptionMocks, 5000, 0, 5000);
        $serviceMock = $this->getMockedService([$serviceRateMock]);
        $shipment = $this->getMockedShipment(1337, $serviceMock, $serviceOptionMocks);

        $this->assertNull((new PriceCalculator())->calculate($shipment));
    }

    /** @test */
    public function testItReturnsNullForNotPricedServices()
    {
        $serviceRateMock = $this->getMockedServiceRate([], null, 0, 5000);
        $serviceMock = $this->getMockedService([$serviceRateMock]);
        $shipment = $this->getMockedShipment(1337, $serviceMock);

        $this->assertNull((new PriceCalculator())->calculate($shipment));
    }

    /** @test */
    public function testItCalculatesThePriceOfShipmentsWithoutServiceOptions()
    {
        $serviceRateMock = $this->getMockedServiceRate([], 3914, 0, 5000);
        $serviceMock = $this->getMockedService([$serviceRateMock]);
        $shipment = $this->getMockedShipment(1337, $serviceMock);

        $this->assertEquals(3914, (new PriceCalculator())->calculate($shipment));
    }

    /** @test */
    public function testItThrowsAnExceptionWhenNoServiceRateCanBeMatchedForShipment()
    {
        $serviceMock = $this->getMockedService();
        $shipment = $this->getMockedShipment(1233, $serviceMock);

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage('Cannot find a matching service rate for given shipment');
        (new PriceCalculator())->calculate($shipment);
    }

    /** @test */
    public function testItThrowsAnExceptionWhenNoServiceRateCanBeMatchedForShipmentWithOptions()
    {
        $serviceRateMock = $this->getMockedServiceRate([], 5000, 0, 5000);
        $serviceMock = $this->getMockedService([$serviceRateMock]);
        $shipment = $this->getMockedShipment(1337, $serviceMock, [
            $this->getMockedServiceOption('service-option-id'),
        ]);

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage('Cannot find a matching service rate for given shipment');
        (new PriceCalculator())->calculate($shipment);
    }

    /** @test */
    public function testItThrowsAnExceptionWhenPassedServiceRateHasInvalidOptions()
    {
        $serviceOptionMocks = [
            $this->getMockedServiceOption('service-option-id-uno', 250),
            $this->getMockedServiceOption('service-option-id-dos', 850),
        ];
        $serviceRateMock = $this->getMockedServiceRate([], 5000, 0, 5000);
        $serviceMock = $this->getMockedService([$serviceRateMock]);
        $shipment = $this->getMockedShipment(1337, $serviceMock, $serviceOptionMocks);

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage('Cannot calculate a price for given shipment; invalid option: ');
        (new PriceCalculator())->calculate($shipment, $serviceRateMock);
    }

    /** @test */
    public function testItThrowsAnExceptionIfShipmentDoesNotHaveWeightSet()
    {
        $serviceMock = $this->getMockedService();
        $shipment = $this->getMockedShipment(null, $serviceMock);

        $this->expectException(InvalidResourceException::class);
        $this->expectExceptionMessage('Cannot calculate shipment price without a valid shipment weight.');
        (new PriceCalculator())->calculate($shipment);
    }

    /** @test */
    public function testItThrowsAnExceptionIfShipmentWeightIsNotInRangeOfServiceRateWeightLimits()
    {
        $serviceRateMock = $this->getMockedServiceRate([], 3914, 0, 5000);
        $serviceMock = $this->getMockedService([$serviceRateMock]);
        $shipment = $this->getMockedShipment(999999, $serviceMock);

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage(
            'Could not calculate price for the given service rate since it does not support the shipment weight.'
        );
        (new PriceCalculator())->calculate($shipment);
    }

    /** @test */
    public function testItThrowsAnExceptionIfShipmentWeightIsNegative()
    {
        $serviceRateMock = $this->getMockedServiceRate();
        $serviceMock = $this->getMockedService([$serviceRateMock]);
        $shipment = $this->getMockedShipment(-545, $serviceMock);

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage(
            'Could not calculate price for the given service rate since it does not support the shipment weight.'
        );
        (new PriceCalculator())->calculate($shipment);
    }

    /** @test */
    public function testItThrowsAnExceptionWhenCalculatingShipmentPriceButNoServiceIsSet()
    {
        $shipment = $this->getMockedShipment();

        $this->expectException(InvalidResourceException::class);
        $this->expectExceptionMessage('Cannot calculate shipment price without a set service.');
        (new PriceCalculator())->calculate($shipment);
    }

    /** @test */
    public function testItThrowsAnExceptionWhenCalculatingShipmentPriceButNoContractIsSet()
    {
        /** @var ShipmentInterface $shipment */
        $shipment = $this->createMock(ShipmentInterface::class);
        $physicalProperties = $this->createMock(PhysicalPropertiesInterface::class);
        $physicalProperties->method('getWeight')->willReturn(1234);

        $shipment->method('getPhysicalProperties')->willReturn($physicalProperties);
        $shipment->method('getService')->willReturn($this->createMock(ServiceInterface::class));
        $shipment->method('getContract')->willReturn(null);

        $this->expectException(InvalidResourceException::class);
        $this->expectExceptionMessage('Cannot calculate shipment price without a set contract.');
        (new PriceCalculator())->calculate($shipment);
    }
}
