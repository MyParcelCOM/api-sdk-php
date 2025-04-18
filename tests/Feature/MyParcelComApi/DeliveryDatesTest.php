<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Feature\MyParcelComApi;

use DateTime;
use DateTimeImmutable;
use MyParcelCom\ApiSdk\Resources\Address;
use MyParcelCom\ApiSdk\Tests\TestCase;

/**
 * @group Shipments
 */
class DeliveryDatesTest extends TestCase
{
    public function testItRetrievesDeliveryDates(): void
    {
        $address = new Address();
        $address
            ->setCountryCode('DE')
            ->setPostalCode('12345')
            ->setStreetNumber(221);

        $deliveryDates = $this->api->getDeliveryDates(
            'service-code',
            'carrier-code',
            $address,
            new DateTime(),
            new DateTime(),
            [
                'service-option-code-1',
                'service-option-code-2',
            ],
        );

        $this->assertCount(3, $deliveryDates);

        foreach ($deliveryDates as $deliveryDate) {
            $this->assertInstanceOf(DateTimeImmutable::class, $deliveryDate['date_from']);
            $this->assertInstanceOf(DateTimeImmutable::class, $deliveryDate['date_to']);
        }
    }
}
