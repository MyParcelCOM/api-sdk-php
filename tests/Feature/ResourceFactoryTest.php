<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Feature;

use MyParcelCom\ApiSdk\MyParcelComApi;
use MyParcelCom\ApiSdk\MyParcelComApiInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\CarrierInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\FileInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\PickUpDropOffLocationInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\RegionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceOptionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceRateInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentStatusInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\ApiSdk\Resources\ResourceFactory;
use MyParcelCom\ApiSdk\Tests\Traits\MocksApiCommunication;
use PHPUnit\Framework\TestCase;

class ResourceFactoryTest extends TestCase
{
    use MocksApiCommunication;

    /** @test */
    public function testCreateEmptyCarrier()
    {
        $resourceFactory = new ResourceFactory();
        $carrier = $resourceFactory->create('carriers');

        $this->assertInstanceOf(CarrierInterface::class, $carrier);
        $this->assertEquals([
            'type' => 'carriers',
        ], $carrier->jsonSerialize());
    }

    /** @test */
    public function testCreateCarrier()
    {
        $carrierProperties = [
            'type'       => 'carriers',
            'attributes' => [
                'name'               => 'MyParcel.com Carrier',
                'code'               => 'mp-carrier',
                'credentials_format' => [
                    '$schema'              => 'http://json-schema.org/draft-07/schema#',
                    'type'                 => 'object',
                    'additionalProperties' => false,
                    'required'             => [
                        'user',
                        'pw',
                    ],
                    'properties'           => [
                        'user' => [
                            'type' => 'string',
                        ],
                        'pw'   => [
                            'type' => 'string',
                        ],
                    ],
                ],
            ],
        ];

        $resourceFactory = new ResourceFactory();
        $carrier = $resourceFactory->create('carriers', $carrierProperties);

        $this->assertInstanceOf(CarrierInterface::class, $carrier);
        $this->assertEquals($carrierProperties, $carrier->jsonSerialize());
    }

    /** @test */
    public function testCreateEmptyContract()
    {
        $resourceFactory = new ResourceFactory();
        $contract = $resourceFactory->create('contracts');

        $this->assertInstanceOf(ContractInterface::class, $contract);
        $this->assertEquals([
            'type'       => 'contracts',
            'attributes' => [
                'volumetric_weight_divisor_factor' => 1.0,
            ],
        ], $contract->jsonSerialize());
    }

    public function testCreateContract()
    {
        $contractProperties = [
            'id'            => 'carrier-id',
            'type'          => 'contracts',
            'attributes'    => [
                'currency'                         => 'JPY',
                'status'                           => 'active',
                'volumetric_weight_divisor_factor' => 1.0,
            ],
            'relationships' => [
                'carrier' => [
                    'data' => [
                        'type' => 'carriers',
                        'id'   => 'carrier-id',
                    ],
                ],
            ],
        ];

        $resourceFactory = new ResourceFactory();
        $resourceFactory->setMyParcelComApi($this->getMockBuilder(MyParcelComApiInterface::class)->getMock());
        $contract = $resourceFactory->create('contracts', $contractProperties);

        $this->assertInstanceOf(ContractInterface::class, $contract);
        $this->assertEquals($contractProperties, $contract->jsonSerialize());
    }

    /** @test */
    public function testCreateEmptyPickUpDropOffLocation()
    {
        $resourceFactory = new ResourceFactory();
        $pudoLocation = $resourceFactory->create('pickup-dropoff-locations');

        $this->assertInstanceOf(PickUpDropOffLocationInterface::class, $pudoLocation);
        $this->assertEquals([
            'type' => 'pickup-dropoff-locations',
        ], $pudoLocation->jsonSerialize());
    }

    /** @test */
    public function testCreatePickUpDropOffLocation()
    {
        $pudoProperties = [
            'type'       => 'pickup-dropoff-locations',
            'attributes' => [
                'address'       => [
                    'street_1'             => 'Diagonally',
                    'street_2'             => 'Apartment A',
                    'street_number'        => 40,
                    'street_number_suffix' => 'A',
                    'postal_code'          => '1AR BR2',
                    'city'                 => 'London',
                    'region_code'          => 'NH',
                    'country_code'         => 'AF',
                    'first_name'           => 'Robert',
                    'last_name'            => 'Drop Tables',
                    'company'              => 'ACME co.',
                    'email'                => 'rob@tables.com',
                    'phone_number'         => '+31 (0)234 567 890',
                ],
                'opening_hours' => [
                    [
                        'day'    => 'Monday',
                        'open'   => '12:00',
                        'closed' => '15:00',
                    ],
                    [
                        'day'    => 'Tuesday',
                        'open'   => '09:00',
                        'closed' => '19:00',
                    ],
                    [
                        'day'    => 'Wednesday',
                        'open'   => '09:00',
                        'closed' => '19:00',
                    ],
                    [
                        'day'    => 'Thursday',
                        'open'   => '09:00',
                        'closed' => '19:00',
                    ],
                    [
                        'day'    => 'Friday',
                        'open'   => '09:00',
                        'closed' => '19:00',
                    ],
                    [
                        'day'    => 'Saturday',
                        'open'   => '10:00',
                        'closed' => '17:00',
                    ],
                    [
                        'day'    => 'Sunday',
                        'open'   => '00:00',
                        'closed' => '00:00',
                    ],
                ],
                'position'      => [
                    'latitude'  => 1.2345,
                    'longitude' => 2.34567,
                ],
                'categories'    => [
                    'pick-up',
                ],
            ],
            'meta'       => [
                'distance' => 5000,
            ],
        ];

        $resourceFactory = new ResourceFactory();
        $pudoLocation = $resourceFactory->create('pickup-dropoff-locations', $pudoProperties);

        $this->assertInstanceOf(PickUpDropOffLocationInterface::class, $pudoLocation);
        $this->assertEquals(
            $pudoProperties,
            $pudoLocation->jsonSerialize(),
        );
    }

    /** @test */
    public function testCreateEmptyRegion()
    {
        $resourceFactory = new ResourceFactory();
        $region = $resourceFactory->create('regions');

        $this->assertInstanceOf(RegionInterface::class, $region);
        $this->assertEquals([
            'type' => 'regions',
        ], $region->jsonSerialize());
    }

    /** @test */
    public function testCreateRegion()
    {
        $regionProperties = [
            'type'          => 'regions',
            'attributes'    => [
                'country_code' => 'NL',
                'region_code'  => 'ZH',
                'currency'     => 'EUR',
                'name'         => 'Rotterdam',
                'category'     => 'city',
            ],
            'relationships' => [
                'parent' => [
                    'data' => [
                        'id'   => 'region-id',
                        'type' => 'regions',
                    ],
                ],
            ],
        ];

        $resourceFactory = new ResourceFactory();
        $resourceFactory->setMyParcelComApi($this->getMockBuilder(MyParcelComApiInterface::class)->getMock());
        $region = $resourceFactory->create('regions', $regionProperties);

        $this->assertInstanceOf(RegionInterface::class, $region);
        $this->assertEquals($regionProperties, $region->jsonSerialize());
    }

    /** @test */
    public function testCreateEmptyService()
    {
        $resourceFactory = new ResourceFactory();
        $service = $resourceFactory->create('services');

        $this->assertInstanceOf(ServiceInterface::class, $service);
        $this->assertEquals([
            'type'       => 'services',
            'attributes' => [
                'transit_time'           => [],
                'delivery_days'          => [],
                'regions_from'           => [],
                'regions_to'             => [],
                'uses_volumetric_weight' => false,
            ],
        ], $service->jsonSerialize());
    }

    /** @test */
    public function testCreateService()
    {
        $serviceProperties = [
            'id'            => 'service-id-9001',
            'type'          => 'services',
            'attributes'    => [
                'name'                   => 'Easy Delivery Service',
                'code'                   => 'easy-service',
                'package_type'           => ServiceInterface::PACKAGE_TYPE_PARCEL,
                'transit_time'           => [
                    'min' => 2,
                    'max' => 5,
                ],
                'handover_method'        => 'drop-off',
                'delivery_days'          => [
                    'Monday',
                    'Wednesday',
                    'Friday',
                ],
                'delivery_method'        => 'delivery',
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
        ];

        $resourceFactory = new ResourceFactory();
        $resourceFactory->setMyParcelComApi($this->getMockBuilder(MyParcelComApiInterface::class)->getMock());
        $service = $resourceFactory->create('services', $serviceProperties);

        $this->assertInstanceOf(ServiceInterface::class, $service);
        $this->assertEquals($serviceProperties, $service->jsonSerialize());
    }

    /** @test */
    public function testItCreatesAnEmptyServiceRate()
    {
        $resourceFactory = new ResourceFactory();
        $serviceRate = $resourceFactory->create('service-rates');

        $this->assertInstanceOf(ServiceRateInterface::class, $serviceRate);
        $this->assertEquals([
            'type' => 'service-rates',
        ], $serviceRate->jsonSerialize());
    }

    /** @test */
    public function testItCreatesAServiceRate()
    {
        $serviceRateProperties = [
            'type'          => 'service-rates',
            'id'            => 'service-rate-id',
            'attributes'    => [
                'weight_min'     => 2000,
                'weight_max'     => 5000,
                'width_max'      => 100,
                'height_max'     => 200,
                'volume_max'     => 6,
                'length_max'     => 300,
                'price'          => [
                    'amount'   => 800,
                    'currency' => 'GBP',
                ],
                'fuel_surcharge' => [
                    'amount'   => 19,
                    'currency' => 'GBP',
                ],
            ],
            'relationships' => [
                'service'         => [
                    'data' => [
                        'id'   => 'service-id',
                        'type' => 'services',
                    ],
                ],
                'contract'        => [
                    'data' => [
                        'id'   => 'contract-id',
                        'type' => 'contracts',
                    ],
                ],
                'service_options' => [
                    'data' => [
                        [
                            'id'   => 'service-option-id-1',
                            'type' => 'service-options',
                            'meta' => [
                                'included' => true,
                                'price'    => [
                                    'amount'   => 500,
                                    'currency' => 'GBP',
                                ],
                            ],
                        ],
                        [
                            'id'   => 'service-option-id-2',
                            'type' => 'service-options',
                            'meta' => [
                                'included' => false,
                                'price'    => [
                                    'amount'   => 200,
                                    'currency' => 'GBP',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $resourceFactory = new ResourceFactory();
        $resourceFactory->setMyParcelComApi($this->getMockBuilder(MyParcelComApiInterface::class)->getMock());
        $serviceRate = $resourceFactory->create('service-rates', $serviceRateProperties);

        $this->assertInstanceOf(ServiceRateInterface::class, $serviceRate);
        $this->assertEquals($serviceRateProperties, $serviceRate->jsonSerialize());
    }

    /** @test */
    public function testCreateEmptyServiceOption()
    {
        $resourceFactory = new ResourceFactory();
        $serviceOption = $resourceFactory->create('service-options');

        $this->assertInstanceOf(ServiceOptionInterface::class, $serviceOption);
        $this->assertEquals([
            'type' => 'service-options',
        ], $serviceOption->jsonSerialize());
    }

    /** @test */
    public function testCreateServiceOption()
    {
        $serviceOptionProperties = [
            'type'       => 'service-options',
            'attributes' => [
                'name'     => 'Sign on delivery',
                'code'     => 'some-code',
                'category' => 'some-category',
            ],
        ];

        $resourceFactory = new ResourceFactory();
        $serviceOption = $resourceFactory->create('service-options', $serviceOptionProperties);

        $this->assertInstanceOf(ServiceOptionInterface::class, $serviceOption);
        $this->assertEquals($serviceOptionProperties, $serviceOption->jsonSerialize());
    }

    /** @test */
    public function testCreateEmptyShipment()
    {
        $resourceFactory = new ResourceFactory();
        $shipment = $resourceFactory->create('shipments');

        $this->assertInstanceOf(ShipmentInterface::class, $shipment);
        $this->assertEquals([
            'type' => 'shipments',
            'meta' => [
                'label_mime_type' => 'application/pdf',
            ],
        ], $shipment->jsonSerialize());
    }

    /** @test */
    public function testCreateShipment()
    {
        $shipmentProperties = [
            'type'          => 'shipments',
            'attributes'    => [
                'barcode'              => 'S3BARCODE',
                'description'          => 'Fidget spinners',
                'customer_reference'   => '#012ASD',
                'price'                => [
                    'amount'   => 99,
                    'currency' => 'USD',
                ],
                'total_value'          => [
                    'amount'   => 1337,
                    'currency' => 'EUR',
                ],
                'physical_properties'  => [
                    'weight' => 1000,
                    'length' => 1100,
                    'height' => 1300,
                    'width'  => 1400,
                    'volume' => 2002,
                ],
                'recipient_address'    => [
                    'street_1'             => 'Diagonally',
                    'street_2'             => 'Apartment 4',
                    'street_number'        => 1,
                    'street_number_suffix' => 'A',
                    'postal_code'          => '1AR BR2',
                    'city'                 => 'London',
                    'region_code'          => 'NH',
                    'country_code'         => 'AF',
                    'first_name'           => 'Robert',
                    'last_name'            => 'Drop Tables',
                    'company'              => 'ACME co.',
                    'email'                => 'rob@tables.com',
                    'phone_number'         => '+31 (0)234 567 890',
                ],
                'recipient_tax_number' => 'H111111-11',
                'sender_address'       => [
                    'street_1'             => 'Diagonally',
                    'street_2'             => 'Apartment 9',
                    'street_number'        => 4,
                    'street_number_suffix' => 'A',
                    'postal_code'          => '1AR BR2',
                    'city'                 => 'London',
                    'region_code'          => 'NH',
                    'country_code'         => 'AF',
                    'first_name'           => 'Robert',
                    'last_name'            => 'Drop Tables',
                    'company'              => 'ACME co.',
                    'email'                => 'rob@tables.com',
                    'phone_number'         => '+31 (0)234 567 890',
                ],
                'sender_tax_number'    => 'G666666-66',
                'pickup_location'      => [
                    'code'    => 'CODE123',
                    'address' => [
                        'street_1'             => 'Diagonally',
                        'street_2'             => 'Apartment 41',
                        'street_number'        => 2,
                        'street_number_suffix' => 'A',
                        'postal_code'          => '1AR BR2',
                        'city'                 => 'London',
                        'region_code'          => 'NH',
                        'country_code'         => 'AF',
                        'first_name'           => 'Robert',
                        'last_name'            => 'Drop Tables',
                        'company'              => 'ACME co.',
                        'email'                => 'rob@tables.com',
                        'phone_number'         => '+31 (0)234 567 890',
                    ],
                ],
                'register_at'          => 1526913941,
            ],
            'relationships' => [
                'shop'            => ['data' => ['id' => 'shop-id-1', 'type' => 'shops']],
                'service'         => ['data' => ['id' => 'service-id-1', 'type' => 'services']],
                'contract'        => ['data' => ['id' => 'contract-id-1', 'type' => 'contracts']],
                'shipment_status' => ['data' => ['id' => 'shipment-status-id-1', 'type' => 'shipment-statuses']],
                'service_options' => ['data' => [['id' => 'option-id-1', 'type' => 'service-options']]],
                'files'           => ['data' => [['id' => 'file-id-1', 'type' => 'files']]],
            ],
            'meta'          => [
                'label_mime_type' => 'application/pdf',
            ],
        ];

        $resourceFactory = new ResourceFactory();
        $resourceFactory->setMyParcelComApi($this->getMockBuilder(MyParcelComApiInterface::class)->getMock());
        $shipment = $resourceFactory->create('shipments', $shipmentProperties);

        $this->assertInstanceOf(ShipmentInterface::class, $shipment);
        $this->assertEquals($shipmentProperties, $shipment->jsonSerialize());
    }

    /** @test */
    public function testCreateShipmentWithCustoms()
    {
        $shipmentProperties = [
            'type'          => 'shipments',
            'attributes'    => [
                'barcode'              => 'S3BARCODE',
                'description'          => 'Fidget spinners',
                'customer_reference'   => '#012ASD',
                'price'                => [
                    'amount'   => 99,
                    'currency' => 'USD',
                ],
                'total_value'          => [
                    'amount'   => 1337,
                    'currency' => 'EUR',
                ],
                'physical_properties'  => [
                    'weight' => 1000,
                    'length' => 1100,
                    'height' => 1300,
                    'width'  => 1400,
                    'volume' => 2002,
                ],
                'recipient_address'    => [
                    'street_1'             => 'Diagonally',
                    'street_2'             => 'Apartment 4',
                    'street_number'        => 1,
                    'street_number_suffix' => 'A',
                    'postal_code'          => '1AR BR2',
                    'city'                 => 'London',
                    'country_code'         => 'GB',
                    'first_name'           => 'Robert',
                    'last_name'            => 'Drop Tables',
                    'company'              => 'ACME co.',
                    'email'                => 'rob@tables.com',
                    'phone_number'         => '+31 (0)234 567 890',
                ],
                'recipient_tax_number' => 'H111111-11',
                'sender_address'       => [
                    'street_1'             => 'Diagonally',
                    'street_2'             => 'Apartment 9',
                    'street_number'        => 4,
                    'street_number_suffix' => 'A',
                    'postal_code'          => '1AR BR2',
                    'city'                 => 'London',
                    'country_code'         => 'NL',
                    'first_name'           => 'Robert',
                    'last_name'            => 'Drop Tables',
                    'company'              => 'ACME co.',
                    'email'                => 'rob@tables.com',
                    'phone_number'         => '+31 (0)234 567 890',
                ],
                'sender_tax_number'    => 'G666666-66',
                'pickup_location'      => [
                    'code'    => 'CODE123',
                    'address' => [
                        'street_1'             => 'Diagonally',
                        'street_2'             => 'Apartment 41',
                        'street_number'        => 2,
                        'street_number_suffix' => 'A',
                        'postal_code'          => '1AR BR2',
                        'city'                 => 'London',
                        'country_code'         => 'GB',
                        'first_name'           => 'Robert',
                        'last_name'            => 'Drop Tables',
                        'company'              => 'ACME co.',
                        'email'                => 'rob@tables.com',
                        'phone_number'         => '+31 (0)234 567 890',
                    ],
                ],
                'items'                => [
                    [
                        'sku'                    => '123456789',
                        'description'            => 'OnePlus X',
                        'item_value'             => [
                            'amount'   => 100,
                            'currency' => 'GBP',
                        ],
                        'item_weight'            => 128,
                        'item_weight_unit'       => 'mg',
                        'quantity'               => 2,
                        'hs_code'                => '8517.12.00',
                        'origin_country_code'    => 'GB',
                        'is_preferential_origin' => false,
                    ],
                    [
                        'sku'                    => '213425',
                        'description'            => 'OnePlus One',
                        'item_value'             => [
                            'amount'   => 200,
                            'currency' => 'GBP',
                        ],
                        'item_weight'            => 256,
                        'item_weight_unit'       => 'g',
                        'quantity'               => 3,
                        'hs_code'                => '8517.12.00',
                        'origin_country_code'    => 'GB',
                        'is_preferential_origin' => true,
                    ],
                    [
                        'sku'                    => '6876',
                        'description'            => 'OnePlus Two',
                        'item_value'             => [
                            'amount'   => 300,
                            'currency' => 'GBP',
                        ],
                        'item_weight'            => 512,
                        'item_weight_unit'       => 'kg',
                        'quantity'               => 1,
                        'hs_code'                => '8517.12.00',
                        'origin_country_code'    => 'GB',
                        'is_preferential_origin' => false,
                    ],
                ],
                'customs'              => [
                    'content_type'       => 'documents',
                    'invoice_number'     => 'NO.5',
                    'non_delivery'       => 'return',
                    'incoterm'           => 'DAP',
                    'shipping_value'     => [
                        'amount'   => 995,
                        'currency' => 'EUR',
                    ],
                    'license_number'     => '512842382',
                    'certificate_number' => '2112211',
                ],
                'register_at'          => 1526913941,
            ],
            'relationships' => [
                'shop'            => ['data' => ['id' => 'shop-id-1', 'type' => 'shops']],
                'service'         => ['data' => ['id' => 'service-id-1', 'type' => 'services']],
                'contract'        => ['data' => ['id' => 'contract-id-1', 'type' => 'contracts']],
                'shipment_status' => ['data' => ['id' => 'shipment-status-id-1', 'type' => 'shipment-statuses']],
                'service_options' => ['data' => [['id' => 'option-id-1', 'type' => 'service-options']]],
                'files'           => ['data' => [['id' => 'file-id-1', 'type' => 'files']]],
            ],
            'meta'          => [
                'label_mime_type' => 'application/pdf',
            ],
        ];

        $resourceFactory = new ResourceFactory();
        $resourceFactory->setMyParcelComApi($this->getMockBuilder(MyParcelComApiInterface::class)->getMock());
        $shipment = $resourceFactory->create('shipments', $shipmentProperties);

        $this->assertInstanceOf(ShipmentInterface::class, $shipment);
        $this->assertEquals($shipmentProperties, $shipment->jsonSerialize());
    }

    /** @test */
    public function testCreateEmptyShop()
    {
        $resourceFactory = new ResourceFactory();
        $shop = $resourceFactory->create('shops');

        $this->assertInstanceOf(ShopInterface::class, $shop);
        $this->assertEquals([
            'type' => 'shops',
        ], $shop->jsonSerialize());
    }

    /** @test */
    public function testCreateShop()
    {
        $shopProperties = [
            'type'       => 'shops',
            'attributes' => [
                'name'           => 'MyParcel.com Test Shop',
                'sender_address' => [
                    'street_1'             => 'Diagonally',
                    'street_2'             => 'Apartment 3',
                    'street_number'        => 4,
                    'street_number_suffix' => 'A',
                    'postal_code'          => '1AR BR2',
                    'city'                 => 'London',
                    'region_code'          => 'NH',
                    'country_code'         => 'AF',
                    'first_name'           => 'Robert',
                    'last_name'            => 'Drop Tables',
                    'company'              => 'ACME co.',
                    'email'                => 'rob@tables.com',
                    'phone_number'         => '+31 (0)234 567 890',
                ],
                'return_address' => [
                    'street_1'             => 'Diagonally',
                    'street_2'             => 'Apartment 1',
                    'street_number'        => 4,
                    'street_number_suffix' => 'A',
                    'postal_code'          => '1AR BR2',
                    'city'                 => 'London',
                    'region_code'          => 'NH',
                    'country_code'         => 'AF',
                    'first_name'           => 'Robert',
                    'last_name'            => 'Drop Tables',
                    'company'              => 'ACME co.',
                    'email'                => 'rob@tables.com',
                    'phone_number'         => '+31 (0)234 567 890',
                ],
            ],
        ];

        $resourceFactory = new ResourceFactory();
        $shop = $resourceFactory->create('shops', $shopProperties);

        $this->assertInstanceOf(ShopInterface::class, $shop);
        $this->assertEquals($shopProperties, $shop->jsonSerialize());
    }

    /** @test */
    public function testCreateEmptyFile()
    {
        $resourceFactory = new ResourceFactory();
        $file = $resourceFactory->create('files');

        $this->assertInstanceOf(FileInterface::class, $file);
        $this->assertEquals([
            'type' => 'files',
        ], $file->jsonSerialize());
    }

    /** @test */
    public function testCreateFile()
    {
        $fileProperties = [
            'id'         => 'file-id',
            'type'       => 'files',
            'attributes' => [
                'document_type' => 'label',
                'formats'       => [
                    [
                        'extension' => 'pdf',
                        'mime_type' => 'application/pdf',
                    ],
                    [
                        'extension' => 'png',
                        'mime_type' => 'image/png',
                    ],
                ],
            ],
        ];

        // Mock a response from the http client.
        $api = $this->getMockBuilder(MyParcelComApiInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $resourceFactory = (new ResourceFactory())
            ->setMyParcelComApi($api);

        $file = $resourceFactory->create('files', $fileProperties);

        $this->assertInstanceOf(FileInterface::class, $file);
        $this->assertEquals($fileProperties, $file->jsonSerialize());
    }

    /** @test */
    public function testCreateShipmentStatus()
    {
        $shipmentStatusProperties = [
            'type'          => 'shipment-statuses',
            'id'            => 'shipment-status-id-1',
            'attributes'    => [
                'carrier_statuses' => [
                    [
                        'code'        => '9001',
                        'description' => 'Confirmed at destination',
                        'assigned_at' => 1504801719,
                    ],
                ],
                'errors'           => [
                    [
                        'status' => '422',
                        'code'   => '12345',
                        'title'  => 'Value is too long',
                        'detail' => 'The description field exceeds the limit of 25 characters.',
                        'source' => [
                            'pointer'   => '/data/attributes/description',
                            'parameter' => 'include',
                        ],
                        'meta'   => [
                            'carrier_response' => 'ParcelDescription1 exceeds character limit.',
                            'carrier_status'   => '400',
                            'carrier_rules'    => [
                                [
                                    'type'  => 'max-length',
                                    'value' => '35',
                                ],
                            ],
                        ],
                    ],
                ],
                'created_at'       => 1504801719,
            ],
            'relationships' => [
                'status'   => [
                    'data' => [
                        'type' => 'statuses',
                        'id'   => 'status-id-1',
                    ],
                ],
                'shipment' => [
                    'data' => [
                        'type' => 'shipments',
                        'id'   => 'shipment-id-1',
                    ],
                ],
            ],
        ];

        /** @var MyParcelComApi $api */
        $api = $this->getMockBuilder(MyParcelComApiInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $resourceFactory = (new ResourceFactory())
            ->setMyParcelComApi($api);

        $shipmentStatus = $resourceFactory->create('shipment-statuses', $shipmentStatusProperties);

        $this->assertInstanceOf(ShipmentStatusInterface::class, $shipmentStatus);
        $this->assertEquals($shipmentStatusProperties, $shipmentStatus->jsonSerialize());
    }
}
