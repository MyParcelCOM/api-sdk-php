<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources;

use DateTime;
use MyParcelCom\ApiSdk\Enums\DimensionUnitEnum;
use MyParcelCom\ApiSdk\Resources\Interfaces\AddressInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\CollectionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\CustomsInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\FileInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ManifestInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\PhysicalPropertiesInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceOptionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentItemInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentStatusInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentSurchargeInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\ProcessIncludes;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;
use MyParcelCom\ApiSdk\Utils\DateUtils;

class Shipment implements ShipmentInterface
{
    use JsonSerializable;
    use ProcessIncludes;
    use Resource;

    const ATTRIBUTE_BARCODE = 'barcode';
    const ATTRIBUTE_TRACKING_CODE = 'tracking_code';
    const ATTRIBUTE_TRACKING_URL = 'tracking_url';
    const ATTRIBUTE_TRACKING_PAGE_URL = 'tracking_page_url';
    const ATTRIBUTE_CHANNEL = 'channel';
    const ATTRIBUTE_DESCRIPTION = 'description';
    const ATTRIBUTE_CUSTOMER_REFERENCE = 'customer_reference';
    const ATTRIBUTE_AMOUNT = 'amount';
    const ATTRIBUTE_PRICE = 'price';
    const ATTRIBUTE_CURRENCY = 'currency';
    const ATTRIBUTE_PHYSICAL_PROPERTIES = 'physical_properties';
    const ATTRIBUTE_RECIPIENT_ADDRESS = 'recipient_address';
    const ATTRIBUTE_RECIPIENT_TAX_NUMBER = 'recipient_tax_number';
    const ATTRIBUTE_RECIPIENT_TAX_IDENTIFICATION_NUMBERS = 'recipient_tax_identification_numbers';
    const ATTRIBUTE_SENDER_ADDRESS = 'sender_address';
    const ATTRIBUTE_SENDER_TAX_NUMBER = 'sender_tax_number';
    const ATTRIBUTE_SENDER_TAX_IDENTIFICATION_NUMBERS = 'sender_tax_identification_numbers';
    const ATTRIBUTE_RETURN_ADDRESS = 'return_address';
    const ATTRIBUTE_PICKUP = 'pickup_location';
    const ATTRIBUTE_PICKUP_CODE = 'code';
    const ATTRIBUTE_PICKUP_ADDRESS = 'address';
    const ATTRIBUTE_CUSTOMS = 'customs';
    const ATTRIBUTE_ITEMS = 'items';
    const ATTRIBUTE_REGISTER_AT = 'register_at';
    const ATTRIBUTE_TOTAL_VALUE = 'total_value';
    const ATTRIBUTE_TAGS = 'tags';
    const ATTRIBUTE_COLLO_NUMBER = 'collo_number';

    const RELATIONSHIP_COLLI = 'colli';
    const RELATIONSHIP_CONTRACT = 'contract';
    const RELATIONSHIP_FILES = 'files';
    const RELATIONSHIP_MANIFEST = 'manifest';
    const RELATIONSHIP_SERVICE = 'service';
    const RELATIONSHIP_SERVICE_OPTIONS = 'service_options';
    const RELATIONSHIP_STATUS = 'shipment_status';
    const RELATIONSHIP_SHOP = 'shop';
    const RELATIONSHIP_COLLECTION = 'collection';
    const RELATIONSHIP_SHIPMENT_SURCHARGES = 'shipment_surcharges';

    const META_LABEL_MIME_TYPE = 'label_mime_type';
    const META_SERVICE_CODE = 'service_code';

    const INCLUDES = [
        ResourceInterface::TYPE_CONTRACT           => self::RELATIONSHIP_CONTRACT,
        ResourceInterface::TYPE_FILE               => self::RELATIONSHIP_FILES,
        ResourceInterface::TYPE_SERVICE            => self::RELATIONSHIP_SERVICE,
        ResourceInterface::TYPE_SERVICE_OPTION     => self::RELATIONSHIP_SERVICE_OPTIONS,
        ResourceInterface::TYPE_SHIPMENT           => self::RELATIONSHIP_COLLI,
        ResourceInterface::TYPE_SHIPMENT_STATUS    => self::RELATIONSHIP_STATUS,
        ResourceInterface::TYPE_SHOP               => self::RELATIONSHIP_SHOP,
        ResourceInterface::TYPE_COLLECTION         => self::RELATIONSHIP_COLLECTION,
        ResourceInterface::TYPE_SHIPMENT_SURCHARGE => self::RELATIONSHIP_SHIPMENT_SURCHARGES,
    ];

    private ?string $id = null;

    private string $type = ResourceInterface::TYPE_SHIPMENT;

    private array $attributes = [
        self::ATTRIBUTE_BARCODE                              => null,
        self::ATTRIBUTE_TRACKING_CODE                        => null,
        self::ATTRIBUTE_TRACKING_URL                         => null,
        self::ATTRIBUTE_TRACKING_PAGE_URL                    => null,
        self::ATTRIBUTE_CHANNEL                              => null,
        self::ATTRIBUTE_DESCRIPTION                          => null,
        self::ATTRIBUTE_CUSTOMER_REFERENCE                   => null,
        self::ATTRIBUTE_PRICE                                => null,
        self::ATTRIBUTE_PHYSICAL_PROPERTIES                  => null,
        self::ATTRIBUTE_RECIPIENT_ADDRESS                    => null,
        self::ATTRIBUTE_RECIPIENT_TAX_NUMBER                 => null,
        self::ATTRIBUTE_RECIPIENT_TAX_IDENTIFICATION_NUMBERS => null,
        self::ATTRIBUTE_SENDER_ADDRESS                       => null,
        self::ATTRIBUTE_SENDER_TAX_NUMBER                    => null,
        self::ATTRIBUTE_SENDER_TAX_IDENTIFICATION_NUMBERS    => null,
        self::ATTRIBUTE_RETURN_ADDRESS                       => null,
        self::ATTRIBUTE_PICKUP                               => null,
        self::ATTRIBUTE_CUSTOMS                              => null,
        self::ATTRIBUTE_ITEMS                                => null,
        self::ATTRIBUTE_REGISTER_AT                          => null,
        self::ATTRIBUTE_TOTAL_VALUE                          => [
            'amount'   => null,
            'currency' => null,
        ],
        self::ATTRIBUTE_TAGS                                 => null,
        self::ATTRIBUTE_COLLO_NUMBER                         => null,
    ];

    private array $relationships = [
        self::RELATIONSHIP_SHOP                => [
            'data' => null,
        ],
        self::RELATIONSHIP_STATUS              => [
            'data' => null,
        ],
        self::RELATIONSHIP_SERVICE_OPTIONS     => [
            'data' => [],
        ],
        self::RELATIONSHIP_FILES               => [
            'data' => [],
        ],
        self::RELATIONSHIP_SERVICE             => [
            'data' => null,
        ],
        self::RELATIONSHIP_CONTRACT            => [
            'data' => null,
        ],
        self::RELATIONSHIP_MANIFEST            => [
            'data' => null,
        ],
        self::RELATIONSHIP_COLLECTION          => [
            'data' => null,
        ],
        self::RELATIONSHIP_SHIPMENT_SURCHARGES => [
            'data' => [],
        ],
        self::RELATIONSHIP_COLLI               => [
            'data' => [],
        ],
    ];

    private array $meta = [
        self::META_LABEL_MIME_TYPE => FileInterface::MIME_TYPE_PDF,
        self::META_SERVICE_CODE    => null,
    ];

    /** @var ShipmentStatusInterface[] */
    private array $statusHistory = [];

    /** @var callable */
    private $statusHistoryCallback = null;

    /**
     * Prepare the data for a request to our API. This filters the read-only relationships to avoid validation errors.
     */
    public function getData(): array
    {
        $data = $this->jsonSerialize();

        // Remove read-only relationships.
        unset($data['relationships'][self::RELATIONSHIP_COLLI]);
        unset($data['relationships'][self::RELATIONSHIP_FILES]);
        unset($data['relationships'][self::RELATIONSHIP_STATUS]);
        unset($data['relationships'][self::RELATIONSHIP_SHIPMENT_SURCHARGES]);

        return $data;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    public function setRecipientAddress(AddressInterface $recipientAddress): self
    {
        $this->attributes[self::ATTRIBUTE_RECIPIENT_ADDRESS] = $recipientAddress;

        return $this;
    }

    public function getRecipientAddress(): ?AddressInterface
    {
        return $this->attributes[self::ATTRIBUTE_RECIPIENT_ADDRESS];
    }

    /**
     * @deprecated Use setRecipientTaxIdentificationNumbers() or addRecipientTaxIdentificationNumber() instead.
     */
    public function setRecipientTaxNumber(?string $recipientTaxNumber): self
    {
        $this->attributes[self::ATTRIBUTE_RECIPIENT_TAX_NUMBER] = $recipientTaxNumber;

        return $this;
    }

    /**
     * @deprecated Use getRecipientTaxIdentificationNumbers() instead.
     */
    public function getRecipientTaxNumber(): ?string
    {
        return $this->attributes[self::ATTRIBUTE_RECIPIENT_TAX_NUMBER];
    }

    public function setRecipientTaxIdentificationNumbers(array $taxIdentificationNumbers): self
    {
        $this->attributes[self::ATTRIBUTE_RECIPIENT_TAX_IDENTIFICATION_NUMBERS] = [];

        array_walk($taxIdentificationNumbers, function (TaxIdentificationNumber $taxIdentificationNumber) {
            $this->addRecipientTaxIdentificationNumber($taxIdentificationNumber);
        });

        return $this;
    }

    public function addRecipientTaxIdentificationNumber(TaxIdentificationNumber $taxIdentificationNumber): self
    {
        $this->attributes[self::ATTRIBUTE_RECIPIENT_TAX_IDENTIFICATION_NUMBERS][] = $taxIdentificationNumber;

        return $this;
    }

    public function getRecipientTaxIdentificationNumbers(): array
    {
        return $this->attributes[self::ATTRIBUTE_RECIPIENT_TAX_IDENTIFICATION_NUMBERS];
    }

    public function setSenderAddress(AddressInterface $senderAddress): self
    {
        $this->attributes[self::ATTRIBUTE_SENDER_ADDRESS] = $senderAddress;

        return $this;
    }

    public function getSenderAddress(): ?AddressInterface
    {
        return $this->attributes[self::ATTRIBUTE_SENDER_ADDRESS];
    }

    /**
     * @deprecated Use setSenderTaxIdentificationNumbers() or addSenderTaxIdentificationNumber() instead.
     */
    public function setSenderTaxNumber(?string $senderTaxNumber): self
    {
        $this->attributes[self::ATTRIBUTE_SENDER_TAX_NUMBER] = $senderTaxNumber;

        return $this;
    }

    /**
     * @deprecated Use getSenderTaxIdentificationNumbers() instead.
     */
    public function getSenderTaxNumber(): ?string
    {
        return $this->attributes[self::ATTRIBUTE_SENDER_TAX_NUMBER];
    }

    public function setSenderTaxIdentificationNumbers(array $taxIdentificationNumbers): self
    {
        $this->attributes[self::ATTRIBUTE_SENDER_TAX_IDENTIFICATION_NUMBERS] = [];

        array_walk($taxIdentificationNumbers, function (TaxIdentificationNumber $taxIdentificationNumber) {
            $this->addSenderTaxIdentificationNumber($taxIdentificationNumber);
        });

        return $this;
    }

    public function addSenderTaxIdentificationNumber(TaxIdentificationNumber $taxIdentificationNumber): self
    {
        $this->attributes[self::ATTRIBUTE_SENDER_TAX_IDENTIFICATION_NUMBERS][] = $taxIdentificationNumber;

        return $this;
    }

    public function getSenderTaxIdentificationNumbers(): array
    {
        return $this->attributes[self::ATTRIBUTE_SENDER_TAX_IDENTIFICATION_NUMBERS];
    }

    public function setReturnAddress(AddressInterface $returnAddress): self
    {
        $this->attributes[self::ATTRIBUTE_RETURN_ADDRESS] = $returnAddress;

        return $this;
    }

    public function getReturnAddress(): ?AddressInterface
    {
        return $this->attributes[self::ATTRIBUTE_RETURN_ADDRESS];
    }

    public function setPickupLocationCode(?string $pickupLocationCode): self
    {
        $this->attributes[self::ATTRIBUTE_PICKUP][self::ATTRIBUTE_PICKUP_CODE] = $pickupLocationCode;

        return $this;
    }

    public function getPickupLocationCode(): ?string
    {
        return $this->attributes[self::ATTRIBUTE_PICKUP][self::ATTRIBUTE_PICKUP_CODE] ?? null;
    }

    public function setPickupLocationAddress(?AddressInterface $pickupLocationAddress): self
    {
        $this->attributes[self::ATTRIBUTE_PICKUP][self::ATTRIBUTE_PICKUP_ADDRESS] = $pickupLocationAddress;

        return $this;
    }

    public function getPickupLocationAddress(): ?AddressInterface
    {
        return $this->attributes[self::ATTRIBUTE_PICKUP][self::ATTRIBUTE_PICKUP_ADDRESS] ?? null;
    }

    public function setChannel(?string $channel): self
    {
        $this->attributes[self::ATTRIBUTE_CHANNEL] = $channel;

        return $this;
    }

    public function getChannel(): ?string
    {
        return $this->attributes[self::ATTRIBUTE_CHANNEL];
    }

    public function setDescription(?string $description): self
    {
        $this->attributes[self::ATTRIBUTE_DESCRIPTION] = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->attributes[self::ATTRIBUTE_DESCRIPTION];
    }

    public function setCustomerReference(?string $customerReference): self
    {
        $this->attributes[self::ATTRIBUTE_CUSTOMER_REFERENCE] = $customerReference;

        return $this;
    }

    public function getCustomerReference(): ?string
    {
        return $this->attributes[self::ATTRIBUTE_CUSTOMER_REFERENCE];
    }

    /**
     * @internal Method to process our API response. You should not set your own price on a shipment.
     */
    public function setPrice(?int $price): self
    {
        $this->attributes[self::ATTRIBUTE_PRICE][self::ATTRIBUTE_AMOUNT] = $price;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->attributes[self::ATTRIBUTE_PRICE][self::ATTRIBUTE_AMOUNT] ?? null;
    }

    /**
     * @internal Method to process our API response. You should not set your own currency on a shipment.
     */
    public function setCurrency(?string $currency): self
    {
        $this->attributes[self::ATTRIBUTE_PRICE][self::ATTRIBUTE_CURRENCY] = $currency;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->attributes[self::ATTRIBUTE_PRICE][self::ATTRIBUTE_CURRENCY] ?? null;
    }

    /**
     * @internal Method to process our API response. You should not set your own barcode on a shipment.
     */
    public function setBarcode(?string $barcode): self
    {
        $this->attributes[self::ATTRIBUTE_BARCODE] = $barcode;

        return $this;
    }

    public function getBarcode(): ?string
    {
        return $this->attributes[self::ATTRIBUTE_BARCODE];
    }

    /**
     * @internal Method to process our API response. You should not set your own tracking code on a shipment.
     */
    public function setTrackingCode(?string $trackingCode): self
    {
        $this->attributes[self::ATTRIBUTE_TRACKING_CODE] = $trackingCode;

        return $this;
    }

    public function getTrackingCode(): ?string
    {
        return $this->attributes[self::ATTRIBUTE_TRACKING_CODE];
    }

    /**
     * @internal Method to process our API response. You should not set your own tracking URL on a shipment.
     */
    public function setTrackingUrl(?string $trackingUrl): self
    {
        $this->attributes[self::ATTRIBUTE_TRACKING_URL] = $trackingUrl;

        return $this;
    }

    public function getTrackingUrl(): ?string
    {
        return $this->attributes[self::ATTRIBUTE_TRACKING_URL];
    }

    /**
     * @internal Method to process our API response. You should not set your own tracking URL on a shipment.
     */
    public function setTrackingPageUrl(?string $trackingPageUrl): self
    {
        $this->attributes[self::ATTRIBUTE_TRACKING_PAGE_URL] = $trackingPageUrl;

        return $this;
    }

    public function getTrackingPageUrl(): ?string
    {
        return $this->attributes[self::ATTRIBUTE_TRACKING_PAGE_URL];
    }

    /**
     * @deprecated Use Shipment::getPhysicalProperties()->setWeight() instead.
     */
    public function setWeight(int $weight, string $unit = PhysicalPropertiesInterface::WEIGHT_GRAM): self
    {
        if ($this->getPhysicalProperties() === null) {
            $this->setPhysicalProperties(new PhysicalProperties());
        }
        $this->getPhysicalProperties()->setWeight($weight, $unit);

        return $this;
    }

    /**
     * @deprecated Use Shipment::getPhysicalProperties()->getWeight() instead.
     */
    public function getWeight(string $unit = PhysicalPropertiesInterface::WEIGHT_GRAM): ?int
    {
        if ($this->getPhysicalProperties() === null) {
            $this->setPhysicalProperties(new PhysicalProperties());
        }

        return $this->getPhysicalProperties()->getWeight($unit);
    }

    public function setPhysicalProperties(PhysicalPropertiesInterface $physicalProperties): self
    {
        $this->attributes[self::ATTRIBUTE_PHYSICAL_PROPERTIES] = $physicalProperties;

        return $this;
    }

    public function getPhysicalProperties(): ?PhysicalPropertiesInterface
    {
        return $this->attributes[self::ATTRIBUTE_PHYSICAL_PROPERTIES];
    }

    /**
     * @deprecated Your code should not rely on this function. Do not calculate your own volumetric weight.
     */
    public function setVolumetricWeight(?int $volumetricWeight): self
    {
        if ($this->getPhysicalProperties() === null) {
            $this->setPhysicalProperties(new PhysicalProperties());
        }
        $this->getPhysicalProperties()->setVolumetricWeight($volumetricWeight);

        return $this;
    }

    /**
     * @deprecated Use Shipment::getPhysicalProperties()->getVolumetricWeight() instead.
     */
    public function getVolumetricWeight(): ?int
    {
        if ($this->getPhysicalProperties() === null) {
            $this->setPhysicalProperties(new PhysicalProperties());
        }

        return $this->getPhysicalProperties()->getVolumetricWeight();
    }

    public function calculateVolume(string $unit = DimensionUnitEnum::MM3): ?float
    {
        $properties = $this->getPhysicalProperties() ?? new PhysicalProperties();

        if (!$properties->getLength() || !$properties->getWidth() || !$properties->getHeight()) {
            return null;
        }

        $volumeFloatInMm3 = $properties->getLength() * $properties->getWidth() * $properties->getHeight() * 1.0;

        return match ($unit) {
            DimensionUnitEnum::DM3 => $volumeFloatInMm3 / 1000000,
            DimensionUnitEnum::CM3 => $volumeFloatInMm3 / 1000,
            DimensionUnitEnum::MM3 => $volumeFloatInMm3,
            default                => $volumeFloatInMm3,
        };
    }

    public function setShop(?ShopInterface $shop): self
    {
        $this->relationships[self::RELATIONSHIP_SHOP]['data'] = $shop;

        return $this;
    }

    public function getShop(): ?ShopInterface
    {
        return $this->relationships[self::RELATIONSHIP_SHOP]['data'];
    }

    public function setServiceOptions(array $options): self
    {
        $this->relationships[self::RELATIONSHIP_SERVICE_OPTIONS]['data'] = [];

        array_walk($options, function ($option) {
            $this->addServiceOption($option);
        });

        return $this;
    }

    public function addServiceOption(ServiceOptionInterface $option): self
    {
        $this->relationships[self::RELATIONSHIP_SERVICE_OPTIONS]['data'][] = $option;

        return $this;
    }

    public function getServiceOptions(): array
    {
        return $this->relationships[self::RELATIONSHIP_SERVICE_OPTIONS]['data'];
    }

    /**
     * @internal Method to process our API response.
     */
    public function setFiles(array $files): self
    {
        $this->relationships[self::RELATIONSHIP_FILES]['data'] = [];

        array_walk($files, function ($file) {
            $this->addFile($file);
        });

        return $this;
    }

    /**
     * @internal Method to process our API response.
     */
    public function addFile(FileInterface $file): self
    {
        $this->relationships[self::RELATIONSHIP_FILES]['data'][] = $file;

        return $this;
    }

    public function getFiles(?string $type = null): array
    {
        // For multi-colli `master` shipments we make this function return all files from the related `colli` shipments.
        if (!empty($this->getColli())) {
            $colliFiles = array_map(
                fn (ShipmentInterface $collo) => $collo->getFiles($type),
                $this->getColli(),
            );

            return array_merge(...$colliFiles);
        }

        if ($type === null) {
            return $this->relationships[self::RELATIONSHIP_FILES]['data'];
        }

        return array_filter(
            $this->relationships[self::RELATIONSHIP_FILES]['data'],
            fn (FileInterface $file) => $file->getDocumentType() === $type,
        );
    }

    /**
     * @internal Method to process our API response. You should not set your own status on a shipment.
     */
    public function setShipmentStatus(ShipmentStatusInterface $status): self
    {
        $this->relationships[self::RELATIONSHIP_STATUS]['data'] = $status;

        return $this;
    }

    public function getShipmentStatus(): ShipmentStatusInterface
    {
        return $this->relationships[self::RELATIONSHIP_STATUS]['data'];
    }

    /**
     * @internal Method to process our API response. You should not set your own statuses on a shipment.
     */
    public function setStatusHistory(array $statuses): self
    {
        $this->statusHistory = $statuses;

        return $this;
    }

    public function getStatusHistory(): array
    {
        if (empty($this->statusHistory) && isset($this->statusHistoryCallback)) {
            $this->setStatusHistory(call_user_func($this->statusHistoryCallback));
        }

        return $this->statusHistory;
    }

    /**
     * @internal Set the callback to use when retrieving the status history.
     */
    public function setStatusHistoryCallback(callable $callback): self
    {
        $this->statusHistoryCallback = $callback;

        return $this;
    }

    public function setCustoms(?CustomsInterface $customs): self
    {
        $this->attributes[self::ATTRIBUTE_CUSTOMS] = $customs;

        return $this;
    }

    public function getCustoms(): ?CustomsInterface
    {
        return $this->attributes[self::ATTRIBUTE_CUSTOMS];
    }

    public function getItems(): ?array
    {
        return $this->attributes[self::ATTRIBUTE_ITEMS];
    }

    public function addItem(ShipmentItemInterface $item): self
    {
        $this->attributes[self::ATTRIBUTE_ITEMS][] = $item;

        return $this;
    }

    public function setItems(?array $items): self
    {
        $this->attributes[self::ATTRIBUTE_ITEMS] = [];

        array_walk($items, function (ShipmentItemInterface $item) {
            $this->addItem($item);
        });

        return $this;
    }

    public function setRegisterAt(DateTime|int|string|null $registerAt): self
    {
        $this->attributes[self::ATTRIBUTE_REGISTER_AT] = DateUtils::toTimestamp($registerAt);

        return $this;
    }

    public function getRegisterAt(): ?DateTime
    {
        return isset($this->attributes[self::ATTRIBUTE_REGISTER_AT])
            ? (new DateTime())->setTimestamp($this->attributes[self::ATTRIBUTE_REGISTER_AT])
            : null;
    }

    public function setService(?ServiceInterface $service): self
    {
        $this->relationships[self::RELATIONSHIP_SERVICE]['data'] = $service;

        return $this;
    }

    public function getService(): ?ServiceInterface
    {
        return $this->relationships[self::RELATIONSHIP_SERVICE]['data'];
    }

    public function setContract(?ContractInterface $contract): self
    {
        $this->relationships[self::RELATIONSHIP_CONTRACT]['data'] = $contract;

        return $this;
    }

    public function getContract(): ?ContractInterface
    {
        return $this->relationships[self::RELATIONSHIP_CONTRACT]['data'];
    }

    public function setTotalValueAmount(?int $totalValueAmount): self
    {
        $this->attributes[self::ATTRIBUTE_TOTAL_VALUE]['amount'] = $totalValueAmount;

        return $this;
    }

    public function getTotalValueAmount(): ?int
    {
        return $this->attributes[self::ATTRIBUTE_TOTAL_VALUE]['amount'];
    }

    public function setTotalValueCurrency(?string $totalValueCurrency): self
    {
        $this->attributes[self::ATTRIBUTE_TOTAL_VALUE]['currency'] = $totalValueCurrency;

        return $this;
    }

    public function getTotalValueCurrency(): ?string
    {
        return $this->attributes[self::ATTRIBUTE_TOTAL_VALUE]['currency'];
    }

    public function setServiceCode(?string $serviceCode): self
    {
        $this->meta[self::META_SERVICE_CODE] = $serviceCode;

        return $this;
    }

    public function getServiceCode(): ?string
    {
        return $this->meta[self::META_SERVICE_CODE];
    }

    public function setTags(?array $tags): self
    {
        $this->attributes[self::ATTRIBUTE_TAGS] = $tags;

        return $this;
    }

    public function addTag(mixed $tag): self
    {
        $this->attributes[self::ATTRIBUTE_TAGS][] = $tag;

        return $this;
    }

    public function getTags(): ?array
    {
        return $this->attributes[self::ATTRIBUTE_TAGS];
    }

    public function clearTags(): self
    {
        $this->attributes[self::ATTRIBUTE_TAGS] = null;

        return $this;
    }

    /**
     * Supported values are FileInterface::MIME_TYPE_PDF or FileInterface::MIME_TYPE_ZPL
     */
    public function setLabelMimeType(string $labelMimeType): self
    {
        $this->meta[self::META_LABEL_MIME_TYPE] = $labelMimeType;

        return $this;
    }

    public function setManifest(?ManifestInterface $manifest): self
    {
        $this->relationships[self::RELATIONSHIP_MANIFEST]['data'] = $manifest;

        return $this;
    }

    public function getManifest(): ?ManifestInterface
    {
        return $this->relationships[self::RELATIONSHIP_MANIFEST]['data'];
    }

    public function setCollection(?CollectionInterface $collection): ShipmentInterface
    {
        $this->relationships[self::RELATIONSHIP_COLLECTION]['data'] = $collection;

        return $this;
    }

    public function getCollection(): ?CollectionInterface
    {
        return $this->relationships[self::RELATIONSHIP_COLLECTION]['data'];
    }

    /**
     * @internal Method to process our API response. To add surcharges, use $api->createShipmentSurcharge($shipment)
     */
    public function setShipmentSurcharges(array $surcharges): self
    {
        $this->relationships[self::RELATIONSHIP_SHIPMENT_SURCHARGES]['data'] = [];

        array_walk($surcharges, function ($surcharge) {
            $this->addShipmentSurcharge($surcharge);
        });

        return $this;
    }

    /**
     * @internal Method to process our API response. To add a surcharge, use $api->createShipmentSurcharge($shipment)
     */
    public function addShipmentSurcharge(ShipmentSurchargeInterface $surcharge): self
    {
        $this->relationships[self::RELATIONSHIP_SHIPMENT_SURCHARGES]['data'][] = $surcharge;

        return $this;
    }

    public function getShipmentSurcharges(): array
    {
        return $this->relationships[self::RELATIONSHIP_SHIPMENT_SURCHARGES]['data'];
    }

    public function setColli(array $colli): self
    {
        $this->relationships[self::RELATIONSHIP_COLLI]['data'] = [];

        array_walk($colli, function ($collo) {
            $this->addCollo($collo);
        });

        return $this;
    }

    public function addCollo(ShipmentInterface $collo): self
    {
        $this->relationships[self::RELATIONSHIP_COLLI]['data'][] = $collo;

        return $this;
    }

    public function getColli(): array
    {
        return $this->relationships[self::RELATIONSHIP_COLLI]['data'];
    }

    /**
     * @internal Method to process our API response. You should not set your own collo number on a shipment.
     */
    public function setColloNumber(?int $colloNumber): self
    {
        $this->attributes[self::ATTRIBUTE_COLLO_NUMBER] = $colloNumber;

        return $this;
    }

    public function getColloNumber(): ?int
    {
        return $this->attributes[self::ATTRIBUTE_COLLO_NUMBER];
    }
}
