<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Interfaces;

use JsonSerializable;

/**
 * Interface that should be implemented by each class that represents a resource af the API.
 */
interface ResourceInterface extends JsonSerializable
{
    const TYPE_BROKER = 'brokers';
    const TYPE_CARRIER = 'carriers';
    const TYPE_CONTRACT = 'contracts';
    const TYPE_FILE = 'files';
    const TYPE_MANIFEST = 'manifests';
    const TYPE_ORGANIZATION = 'organizations';
    const TYPE_PUDO_LOCATION = 'pickup-dropoff-locations';
    const TYPE_REGION = 'regions';
    const TYPE_SERVICE = 'services';
    const TYPE_SERVICE_OPTION = 'service-options';
    const TYPE_SERVICE_RATE = 'service-rates';
    const TYPE_SHIPMENT = 'shipments';
    const TYPE_SHIPMENT_STATUS = 'shipment-statuses';
    const TYPE_SHOP = 'shops';
    const TYPE_STATUS = 'statuses';

    public function getId(): ?string;

    public function setId(string $id): self;

    public function getType(): string;
}
