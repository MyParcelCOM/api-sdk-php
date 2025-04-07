<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Feature\MyParcelComApi;

use MyParcelCom\ApiSdk\Collection\CollectionInterface;
use MyParcelCom\ApiSdk\Http\Exceptions\RequestException;
use MyParcelCom\ApiSdk\Resources\Carrier;
use MyParcelCom\ApiSdk\Resources\Interfaces\CarrierInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\PickUpDropOffLocationInterface;
use MyParcelCom\ApiSdk\Tests\TestCase;

/**
 * @group PickUpDropOffLocations
 */
class PickUpDropOffLocationsTest extends TestCase
{
    public function testGetPickUpDropOffLocationsForCarrier()
    {
        $carrier = $this->createMock(CarrierInterface::class);
        $carrier
            ->method('getId')
            ->willReturn('eef00b32-177e-43d3-9b26-715365e4ce46');

        $normalCarrierPudoLocations = $this->api->getPickUpDropOffLocations(
            'GB',
            'B48 7QN',
            null,
            null,
            $carrier,
        );

        $this->assertInstanceOf(CollectionInterface::class, $normalCarrierPudoLocations);
        foreach ($normalCarrierPudoLocations as $pudoLocation) {
            $this->assertInstanceOf(PickUpDropOffLocationInterface::class, $pudoLocation);
        }
    }

    public function testGetPickUpDropOffLocationsForFailingCarrier()
    {
        // This carrier does not have pickup points and thus returns an error.
        // An exception should be thrown.
        $failingCarrier = $this->createMock(CarrierInterface::class);
        $failingCarrier
            ->method('getId')
            ->willReturn('4a78637a-5d81-4e71-9b18-c338968f72fa');

        $this->expectException(RequestException::class);
        $this->api->getPickUpDropOffLocations(
            'GB',
            'B48 7QN',
            null,
            null,
            $failingCarrier,
            false,
        );
    }

    public function testGetPickUpDropOffLocations()
    {
        $carriers = $this->api->getCarriers()->get();

        array_walk($carriers, function (CarrierInterface $carrier) use (&$failingCarrierId, &$normalCarrierId) {
            if ($carrier->getId() === '4a78637a-5d81-4e71-9b18-c338968f72fa') {
                $failingCarrierId = $carrier->getId();
            } elseif ($carrier->getId() === 'eef00b32-177e-43d3-9b26-715365e4ce46') {
                $normalCarrierId = $carrier->getId();
            }
        });

        $allPudoLocations = $this->api->getPickUpDropOffLocations(
            'GB',
            'B48 7QN',
            null,
            null,
            null,
            false,
        );

        $this->assertIsArray($allPudoLocations);
        $this->assertNull($allPudoLocations[$failingCarrierId]);
        $this->assertInstanceOf(CollectionInterface::class, $allPudoLocations[$normalCarrierId]);

        foreach ($allPudoLocations[$normalCarrierId] as $pudoLocation) {
            $this->assertInstanceOf(PickUpDropOffLocationInterface::class, $pudoLocation);
        }

        $this->assertCount(count($carriers), $allPudoLocations);
        $this->assertEqualsCanonicalizing(
            array_map(function (CarrierInterface $carrier) {
                return $carrier->getId();
            }, $carriers),
            array_keys($allPudoLocations),
        );
    }

    public function testItRetrievesPickupLocationsForCarriersWithActiveContract()
    {
        $pudoServices = $this->api->getServices(null, [
            'has_active_contract' => 'true',
            'delivery_method'     => 'pick-up',
        ])->get();
        $this->assertCount(1, $pudoServices);
        $pudoCarrierId = reset($pudoServices)->getCarrier()->getId();

        $allCarriers = $this->api->getCarriers()->get();
        $this->assertCount(2, $allCarriers);

        // Requesting pudo locations without the onlyActiveContracts filter gives pudo locations for both carriers.
        $allPudoLocations = $this->api->getPickUpDropOffLocations(
            'GB',
            'B48 7QN',
            null,
            null,
            null,
            false,
        );

        $this->assertTrue(array_key_exists($pudoCarrierId, $allPudoLocations));
        $this->assertCount(2, $allPudoLocations);

        // When requesting pudo locations for active contracts,
        // it only returns the one that has active contract for pudo.
        $filteredPudoLocations = $this->api->getPickUpDropOffLocations('GB', 'B48 7QN');

        $this->assertTrue(array_key_exists($pudoCarrierId, $filteredPudoLocations));
        $this->assertCount(1, $filteredPudoLocations);
    }

    public function testGetPudoLocationsForSpecificCarrierWhichDoesntHaveActiveContract()
    {
        $pudoServices = $this->api->getServices(null, [
            'has_active_contract' => 'true',
            'delivery_method'     => 'pick-up',
        ])->get();
        $this->assertCount(1, $pudoServices);

        /** @var Carrier $pudoCarrier */
        $pudoCarrier = reset($pudoServices)->getCarrier();

        $pudoLocations = $this->api->getPickUpDropOffLocations(
            'GB',
            'B48 7QN',
            null,
            null,
            $pudoCarrier,
        );

        // The carrier with pudo locations should return a set of pudo locations.
        $this->assertNotEmpty($pudoLocations);

        $allCarriers = $this->api->getCarriers()->get();
        $this->assertCount(2, $allCarriers);

        $nonPudoCarriers = array_filter($allCarriers, function (CarrierInterface $carrier) use ($pudoCarrier) {
            return $carrier->getId() !== $pudoCarrier->getId();
        });

        $pudoLocations = $this->api->getPickUpDropOffLocations(
            'GB',
            'B48 7QN',
            null,
            null,
            reset($nonPudoCarriers),
        );

        // The other carrier does not have any pudo services and should thus not return pudo locations.
        $this->assertEmpty($pudoLocations);
    }

    public function testGetPudoLocationsWithLocationTypesFilter()
    {
        $carrier = $this->createMock(CarrierInterface::class);
        $carrier
            ->method('getId')
            ->willReturn('eef00b32-177e-43d3-9b26-715365e4ce46');

        $pudoLocations = $this->api->getPickUpDropOffLocations(
            'GB',
            'B48 7QN',
            null,
            null,
            $carrier,
            false,
            filters: ['location_type' => ['office']],
        );

        // The carrier with pudo locations should return a set of pudo locations.
        $this->assertCount(10, $pudoLocations);

        /** @var \MyParcelCom\ApiSdk\Resources\PickUpDropOffLocation $pudoLocation */
        foreach ($pudoLocations as $pudoLocation) {
            $this->assertEquals('office', $pudoLocation->getLocationType());
        }
    }
}
