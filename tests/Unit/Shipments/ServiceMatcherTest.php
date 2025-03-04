<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Unit\Shipments;

use MyParcelCom\ApiSdk\Exceptions\InvalidResourceException;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceOptionInterface;
use MyParcelCom\ApiSdk\Shipments\ServiceMatcher;
use MyParcelCom\ApiSdk\Tests\Traits\MocksContract;
use PHPUnit\Framework\TestCase;

class ServiceMatcherTest extends TestCase
{
    use MocksContract;

    /** @var ServiceOptionInterface */
    private $proofOfDeliveryOption;
    /** @var ServiceOptionInterface */
    private $collectionOption;
    /** @var ServiceOptionInterface */
    private $weekendDeliveryOption;
    /** @var ServiceMatcher */
    private $matcher;

    protected function setUp(): void
    {
        $this->matcher = new ServiceMatcher();

        $this->proofOfDeliveryOption = $this->getMockedServiceOption('proof-of-delivery-option');
        $this->collectionOption = $this->getMockedServiceOption('collection-option');
        $this->weekendDeliveryOption = $this->getMockedServiceOption('weekend-delivery-option');

        parent::setUp();
    }

    /** @test */
    public function testItMatchesDeliveryMethodsOnAServiceAndShipment()
    {
        $shipment = $this->getMockedShipment();
        $shipment->method('getPickupLocationCode')->willReturn(null);

        $matcher = new ServiceMatcher();

        $pickupService = $this->getMockedService([], 'pick-up');
        $this->assertFalse($matcher->matchesDeliveryMethod($shipment, $pickupService));

        $deliveryService = $this->getMockedService([], 'delivery');
        $this->assertTrue($matcher->matchesDeliveryMethod($shipment, $deliveryService));
    }

    /** @test */
    public function testItMatchesServiceOptionsOnShipmentAndService()
    {
        // Shipment only has collectionOption.
        // Only serviceRates with collectionOption should be returned.
        $shipment = $this->getMockedShipment(5000, $this->getMockedService(), [$this->collectionOption]);

        $serviceRates = [
            // Contains collectionOption and should thus be returned.
            $serviceRateMockA = $this->getMockedServiceRate([
                $this->proofOfDeliveryOption,
                $this->collectionOption,
            ]),

            // Contains collectionOption and should thus be returned.
            $serviceRateMockB = $this->getMockedServiceRate([
                $this->collectionOption,
            ]),

            // Does NOT contain collectionOption and should thus NOT be returned.
            $serviceRateMockC = $this->getMockedServiceRate([
                $this->proofOfDeliveryOption,
            ]),

            // Contains collectionOption and should thus be returned.
            $serviceRateMockD = $this->getMockedServiceRate([
                $this->proofOfDeliveryOption,
                $this->collectionOption,
                $this->weekendDeliveryOption,
            ]),

            // Does NOT contain collectionOption and should thus NOT be returned.
            $serviceRateMockE = $this->getMockedServiceRate([
                $this->proofOfDeliveryOption,
                $this->weekendDeliveryOption,
            ]),
        ];

        $matcher = new ServiceMatcher();

        $this->assertEquals([
            $serviceRateMockA,
            $serviceRateMockB,
            $serviceRateMockD,
        ], $matcher->getMatchedOptions($shipment, $serviceRates));
    }

    /** @test */
    public function testItThrowsAnExceptionIfShipmentWeightIsNegative()
    {
        $serviceMock = $this->getMockedService();
        $shipment = $this->getMockedShipment(-2345);

        $matcher = new ServiceMatcher();

        $this->expectException(InvalidResourceException::class);
        $this->expectExceptionMessage('Cannot match shipment and service without a valid shipment weight.');
        $matcher->matches($shipment, $serviceMock);
    }

    /** @test */
    public function testItThrowsAnExceptionIfShipmentDoesNotHaveWeightSet()
    {
        $serviceMock = $this->getMockedService();
        $shipment = $this->getMockedShipment(null, $serviceMock);

        $matcher = new ServiceMatcher();

        $this->expectException(InvalidResourceException::class);
        $this->expectExceptionMessage('Cannot match shipment and service without a valid shipment weight.');
        $matcher->matches($shipment, $serviceMock);
    }

    /** @test */
    public function testItMatchesAServiceAndShipment()
    {
        // A matching service should have the POD and collection options as well as have delivery method 'pick-up'.
        $shipment = $this->getMockedShipment(2500, null, [$this->proofOfDeliveryOption, $this->collectionOption]);
        $shipment->method('getPickupLocationCode')->willReturn('pickup-code');

        $matchingServiceRates = [
            $this->getMockedServiceRate([
                $this->proofOfDeliveryOption,
                $this->collectionOption,
                $this->weekendDeliveryOption,
            ]),
            $this->getMockedServiceRate([
                $this->proofOfDeliveryOption,
                $this->collectionOption,
            ]),
        ];

        $nonMatchingServiceRates = [
            $this->getMockedServiceRate([
                $this->weekendDeliveryOption,
            ]),
            $this->getMockedServiceRate([
                $this->weekendDeliveryOption,
                $this->proofOfDeliveryOption,
            ]),
        ];

        // Note that in the service matcher,
        // service rates are retrieved from the API using the shipment's weight as filter.
        // An empty array here represents a mocked empty API response for the shipment's weight.
        $nonMatchingServiceA = $this->getMockedService([], 'delivery');
        $this->assertFalse($this->matcher->matches($shipment, $nonMatchingServiceA));

        // This service does have matching service rates, but the delivery method is wrong.
        $nonMatchingServiceB = $this->getMockedService($matchingServiceRates, 'delivery');
        $this->assertFalse($this->matcher->matches($shipment, $nonMatchingServiceB));

        // This service does match the shipment's delivery method, but the service rates don't match the
        // shipment weight.
        $nonMatchingServiceC = $this->getMockedService([], 'pick-up');
        $this->assertFalse($this->matcher->matches($shipment, $nonMatchingServiceC));

        // This service does match the shipment's delivery method and the service rates match the
        // shipment weight, but it doesn't have the right options.
        $nonMatchingServiceC = $this->getMockedService($nonMatchingServiceRates, 'pick-up');
        $this->assertFalse($this->matcher->matches($shipment, $nonMatchingServiceC));

        // This service matches the shipment's delivery method and all service rates match.
        $matchingServiceA = $this->getMockedService($matchingServiceRates, 'pick-up');
        $this->assertTrue($this->matcher->matches($shipment, $matchingServiceA));

        // This service matches the shipment's delivery method and has matching service rates.
        // Note that even though it also has non-matching service rates, the service itself should still match.
        $matchingServiceB = $this->getMockedService(
            array_merge($matchingServiceRates, $nonMatchingServiceRates),
            'pick-up'
        );
        $this->assertTrue($this->matcher->matches($shipment, $matchingServiceB));
    }
}
