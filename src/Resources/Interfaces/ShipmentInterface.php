<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Interfaces;

use DateTime;
use MyParcelCom\ApiSdk\Enums\DimensionUnitEnum;
use MyParcelCom\ApiSdk\Resources\TaxIdentificationNumber;

interface ShipmentInterface extends ResourceInterface
{
    public function setRecipientAddress(AddressInterface $recipientAddress): self;

    public function getRecipientAddress(): ?AddressInterface;

    /**
     * @deprecated Use setRecipientTaxIdentificationNumbers() or addRecipientTaxIdentificationNumber() instead.
     */
    public function setRecipientTaxNumber(?string $recipientTaxNumber): self;

    /**
     * @deprecated Use getRecipientTaxIdentificationNumbers() instead.
     */
    public function getRecipientTaxNumber(): ?string;

    /**
     * @param TaxIdentificationNumber[] $taxIdentificationNumbers
     */
    public function setRecipientTaxIdentificationNumbers(array $taxIdentificationNumbers): self;

    public function addRecipientTaxIdentificationNumber(TaxIdentificationNumber $taxIdentificationNumber): self;

    /**
     * @return TaxIdentificationNumber[]
     */
    public function getRecipientTaxIdentificationNumbers(): array;

    public function setSenderAddress(AddressInterface $senderAddress): self;

    public function getSenderAddress(): ?AddressInterface;

    /**
     * @deprecated Use setSenderTaxIdentificationNumbers() or addSenderTaxIdentificationNumber() instead.
     */
    public function setSenderTaxNumber(?string $senderTaxNumber): self;

    /**
     * @deprecated Use getSenderTaxIdentificationNumbers() instead.
     */
    public function getSenderTaxNumber(): ?string;

    /**
     * @param TaxIdentificationNumber[] $taxIdentificationNumbers
     */
    public function setSenderTaxIdentificationNumbers(array $taxIdentificationNumbers): self;

    public function addSenderTaxIdentificationNumber(TaxIdentificationNumber $taxIdentificationNumber): self;

    /**
     * @return TaxIdentificationNumber[]
     */
    public function getSenderTaxIdentificationNumbers(): array;

    public function setReturnAddress(AddressInterface $returnAddress): self;

    public function getReturnAddress(): ?AddressInterface;

    public function setPickupLocationCode(?string $pickupLocationCode): self;

    public function getPickupLocationCode(): ?string;

    public function setPickupLocationAddress(?AddressInterface $pickupLocationAddress): self;

    public function getPickupLocationAddress(): ?AddressInterface;

    public function setChannel(?string $channel): self;

    public function getChannel(): ?string;

    public function setDescription(?string $description): self;

    public function getDescription(): ?string;

    public function setCustomerReference(?string $customerReference): self;

    public function getCustomerReference(): ?string;

    public function setPrice(?int $price): self;

    public function getPrice(): ?int;

    public function setCurrency(?string $currency): self;

    public function getCurrency(): ?string;

    public function setBarcode(?string $barcode): self;

    public function getBarcode(): ?string;

    public function setTrackingCode(?string $trackingCode): self;

    public function getTrackingCode(): ?string;

    public function setTrackingUrl(?string $trackingUrl): self;

    public function getTrackingUrl(): ?string;

    /**
     * @deprecated Use Shipment::getPhysicalProperties()->setWeight() instead.
     */
    public function setWeight(int $weight, string $unit = PhysicalPropertiesInterface::WEIGHT_GRAM): self;

    /**
     * @deprecated Use Shipment::getPhysicalProperties()->getWeight() instead.
     */
    public function getWeight(string $unit = PhysicalPropertiesInterface::WEIGHT_GRAM): ?int;

    public function setShop(ShopInterface $shop): self;

    public function getShop(): ?ShopInterface;

    /**
     * @param ServiceOptionInterface[] $options
     */
    public function setServiceOptions(array $options): self;

    public function addServiceOption(ServiceOptionInterface $option): self;

    /**
     * @return ServiceOptionInterface[]
     */
    public function getServiceOptions(): array;

    public function setPhysicalProperties(PhysicalPropertiesInterface $physicalProperties): self;

    public function getPhysicalProperties(): ?PhysicalPropertiesInterface;

    /**
     * @deprecated Use Shipment::getPhysicalProperties()->getVolumetricWeight() instead.
     */
    public function getVolumetricWeight(): ?int;

    /**
     * @deprecated Use Shipment::getPhysicalProperties()->setVolumetricWeight() instead.
     */
    public function setVolumetricWeight(?int $volumetricWeight): self;

    public function calculateVolume(string $unit = DimensionUnitEnum::MM3): ?float;

    /**
     * @param FileInterface[] $files
     */
    public function setFiles(array $files): self;

    public function addFile(FileInterface $file): self;

    /**
     * @return FileInterface[]
     */
    public function getFiles(?string $type = null): array;

    public function setShipmentStatus(ShipmentStatusInterface $status): self;

    public function getShipmentStatus(): ShipmentStatusInterface;

    public function setStatusHistory(array $statuses): self;

    /**
     * @return ShipmentStatusInterface[]
     */
    public function getStatusHistory(): array;

    public function setCustoms(?CustomsInterface $customs): self;

    public function getCustoms(): ?CustomsInterface;

    /**
     * @param ShipmentItemInterface[] $items
     */
    public function setItems(?array $items): self;

    public function addItem(ShipmentItemInterface $item): self;

    /**
     * @return ShipmentItemInterface[]|null
     */
    public function getItems(): ?array;

    /**
     * Set the date and time that this shipment should be registered at.
     * This can either be a datetime string as specified by PHP, a unix timestamp integer or a DateTime object.
     * @see http://php.net/manual/en/datetime.formats.php
     */
    public function setRegisterAt(DateTime|int|string|null $registerAt): self;

    public function getRegisterAt(): ?DateTime;

    public function setService(?ServiceInterface $service): self;

    public function getService(): ?ServiceInterface;

    public function setContract(?ContractInterface $contract): self;

    public function getContract(): ?ContractInterface;

    public function setTotalValueAmount(?int $totalValueAmount): self;

    public function getTotalValueAmount(): ?int;

    public function setTotalValueCurrency(?string $totalValueCurrency): self;

    public function getTotalValueCurrency(): ?string;

    public function setServiceCode(?string $serviceCode): self;

    public function getServiceCode(): ?string;

    public function setTags(?array $tags): self;

    public function addTag(mixed $tag): self;

    public function getTags(): ?array;

    public function clearTags(): self;

    public function setCollection(?CollectionInterface $collection): self;

    public function getCollection(): ?CollectionInterface;

    public function setShipmentSurcharges(array $surcharges): self;

    public function addShipmentSurcharge(ShipmentSurchargeInterface $surcharge): self;

    public function getShipmentSurcharges(): array;

    /**
     * @param ShipmentInterface[] $colli
     */
    public function setColli(array $colli): self;

    public function addCollo(ShipmentInterface $collo): self;

    /**
     * @return ShipmentInterface[]
     */
    public function getColli(): array;

    public function setColloNumber(?int $colloNumber): self;

    public function getColloNumber(): ?int;
}
