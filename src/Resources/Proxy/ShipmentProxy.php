<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Proxy;

use DateTime;
use MyParcelCom\ApiSdk\Resources\Interfaces\AddressInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\CollectionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\CustomsInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\FileInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\PhysicalPropertiesInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceProxyInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceOptionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentItemInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentStatusInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\ApiSdk\Resources\Shipment;
use MyParcelCom\ApiSdk\Resources\TaxIdentificationNumber;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\ProxiesResource;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;

/**
 * @method Shipment getResource()
 */
class ShipmentProxy implements ShipmentInterface, ResourceProxyInterface
{
    use JsonSerializable;
    use ProxiesResource;
    use Resource;

    private ?string $id = null;

    private string $type = ResourceInterface::TYPE_SHIPMENT;

    public function getMeta(): array
    {
        return $this->getResource()->getMeta();
    }

    public function setRecipientAddress(AddressInterface $recipientAddress): self
    {
        $this->getResource()->setRecipientAddress($recipientAddress);

        return $this;
    }

    public function getRecipientAddress(): ?AddressInterface
    {
        return $this->getResource()->getRecipientAddress();
    }

    /**
     * @deprecated Use setRecipientTaxIdentificationNumbers() or addRecipientTaxIdentificationNumber() instead.
     */
    public function setRecipientTaxNumber(?string $recipientTaxNumber): self
    {
        $this->getResource()->setRecipientTaxNumber($recipientTaxNumber);

        return $this;
    }

    /**
     * @deprecated Use getRecipientTaxIdentificationNumbers() instead.
     */
    public function getRecipientTaxNumber(): ?string
    {
        return $this->getResource()->getRecipientTaxNumber();
    }

    public function setRecipientTaxIdentificationNumbers(array $taxIdentificationNumbers): self
    {
        $this->getResource()->setRecipientTaxIdentificationNumbers($taxIdentificationNumbers);

        return $this;
    }

    public function addRecipientTaxIdentificationNumber(TaxIdentificationNumber $taxIdentificationNumber): self
    {
        $this->getResource()->addRecipientTaxIdentificationNumber($taxIdentificationNumber);

        return $this;
    }

    public function getRecipientTaxIdentificationNumbers(): array
    {
        return $this->getResource()->getRecipientTaxIdentificationNumbers();
    }

    public function setSenderAddress(AddressInterface $senderAddress): self
    {
        $this->getResource()->setSenderAddress($senderAddress);

        return $this;
    }

    public function getSenderAddress(): ?AddressInterface
    {
        return $this->getResource()->getSenderAddress();
    }

    /**
     * @deprecated Use setSenderTaxIdentificationNumbers() or addSenderTaxIdentificationNumber() instead.
     */
    public function setSenderTaxNumber(?string $senderTaxNumber): self
    {
        $this->getResource()->setSenderTaxNumber($senderTaxNumber);

        return $this;
    }

    /**
     * @deprecated Use getSenderTaxIdentificationNumbers() instead.
     */
    public function getSenderTaxNumber(): ?string
    {
        return $this->getResource()->getSenderTaxNumber();
    }

    public function setSenderTaxIdentificationNumbers(array $taxIdentificationNumbers): self
    {
        $this->getResource()->setSenderTaxIdentificationNumbers($taxIdentificationNumbers);

        return $this;
    }

    public function addSenderTaxIdentificationNumber(TaxIdentificationNumber $taxIdentificationNumber): self
    {
        $this->getResource()->addSenderTaxIdentificationNumber($taxIdentificationNumber);

        return $this;
    }

    public function getSenderTaxIdentificationNumbers(): array
    {
        return $this->getResource()->getSenderTaxIdentificationNumbers();
    }

    public function setReturnAddress(AddressInterface $returnAddress): self
    {
        $this->getResource()->setReturnAddress($returnAddress);

        return $this;
    }

    public function getReturnAddress(): ?AddressInterface
    {
        return $this->getResource()->getReturnAddress();
    }

    public function setPickupLocationCode(?string $pickupLocationCode): self
    {
        $this->getResource()->setPickupLocationCode($pickupLocationCode);

        return $this;
    }

    public function getPickupLocationCode(): ?string
    {
        return $this->getResource()->getPickupLocationCode();
    }

    public function setPickupLocationAddress(?AddressInterface $pickupLocationAddress): self
    {
        $this->getResource()->setPickupLocationAddress($pickupLocationAddress);

        return $this;
    }

    public function getPickupLocationAddress(): ?AddressInterface
    {
        return $this->getResource()->getPickupLocationAddress();
    }

    public function setChannel(?string $channel): self
    {
        $this->getResource()->setChannel($channel);

        return $this;
    }

    public function getChannel(): ?string
    {
        return $this->getResource()->getChannel();
    }

    public function setDescription(?string $description): self
    {
        $this->getResource()->setDescription($description);

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->getResource()->getDescription();
    }

    public function setCustomerReference(?string $customerReference): self
    {
        $this->getResource()->setCustomerReference($customerReference);

        return $this;
    }

    public function getCustomerReference(): ?string
    {
        return $this->getResource()->getCustomerReference();
    }

    public function setPrice(?int $price): self
    {
        $this->getResource()->setPrice($price);

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->getResource()->getPrice();
    }

    public function setCurrency(?string $currency): self
    {
        $this->getResource()->setCurrency($currency);

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->getResource()->getCurrency();
    }

    public function setBarcode(?string $barcode): self
    {
        $this->getResource()->setBarcode($barcode);

        return $this;
    }

    public function getBarcode(): ?string
    {
        return $this->getResource()->getBarcode();
    }

    public function setTrackingCode(?string $trackingCode): self
    {
        $this->getResource()->setTrackingCode($trackingCode);

        return $this;
    }

    public function getTrackingCode(): ?string
    {
        return $this->getResource()->getTrackingCode();
    }

    public function setTrackingUrl(?string $trackingUrl): self
    {
        $this->getResource()->setTrackingUrl($trackingUrl);

        return $this;
    }

    public function getTrackingUrl(): ?string
    {
        return $this->getResource()->getTrackingUrl();
    }

    public function setTrackingPageUrl(?string $trackingUrl): self
    {
        $this->getResource()->setTrackingPageUrl($trackingUrl);

        return $this;
    }

    public function getTrackingPageUrl(): ?string
    {
        return $this->getResource()->getTrackingPageUrl();
    }

    /**
     * @deprecated Use Shipment::getPhysicalProperties()->setWeight() instead.
     */
    public function setWeight(int $weight, string $unit = PhysicalPropertiesInterface::WEIGHT_GRAM): self
    {
        $this->getResource()->setWeight($weight, $unit);

        return $this;
    }

    /**
     * @deprecated Use Shipment::getPhysicalProperties()->getWeight() instead.
     */
    public function getWeight(string $unit = PhysicalPropertiesInterface::WEIGHT_GRAM): ?int
    {
        return $this->getResource()->getWeight($unit);
    }

    public function setShop(ShopInterface $shop): self
    {
        $this->getResource()->setShop($shop);

        return $this;
    }

    public function getShop(): ?ShopInterface
    {
        return $this->getResource()->getShop();
    }

    public function setServiceOptions(array $options): self
    {
        $this->getResource()->setServiceOptions($options);

        return $this;
    }

    public function addServiceOption(ServiceOptionInterface $option): self
    {
        $this->getResource()->addServiceOption($option);

        return $this;
    }

    public function getServiceOptions(): array
    {
        return $this->getResource()->getServiceOptions();
    }

    public function setPhysicalProperties(PhysicalPropertiesInterface $physicalProperties): self
    {
        $this->getResource()->setPhysicalProperties($physicalProperties);

        return $this;
    }

    public function getPhysicalProperties(): ?PhysicalPropertiesInterface
    {
        return $this->getResource()->getPhysicalProperties();
    }

    /**
     * @deprecated Use Shipment::getPhysicalProperties()->setVolumetricWeight() instead.
     */
    public function setVolumetricWeight(?int $volumetricWeight): self
    {
        $this->getResource()->setVolumetricWeight($volumetricWeight);

        return $this;
    }

    /**
     * @deprecated Use Shipment::getPhysicalProperties()->getVolumetricWeight() instead.
     */
    public function getVolumetricWeight(): ?int
    {
        return $this->getResource()->getVolumetricWeight();
    }

    public function setFiles(array $files): self
    {
        $this->getResource()->setFiles($files);

        return $this;
    }

    public function addFile(FileInterface $file): self
    {
        $this->getResource()->addFile($file);

        return $this;
    }

    public function getFiles(string $type = null): array
    {
        return $this->getResource()->getFiles($type);
    }

    public function setShipmentStatus(ShipmentStatusInterface $status): self
    {
        $this->getResource()->setShipmentStatus($status);

        return $this;
    }

    public function getShipmentStatus(): ShipmentStatusInterface
    {
        return $this->getResource()->getShipmentStatus();
    }

    public function setCustoms(?CustomsInterface $customs): self
    {
        $this->getResource()->setCustoms($customs);

        return $this;
    }

    public function getCustoms(): ?CustomsInterface
    {
        return $this->getResource()->getCustoms();
    }

    public function setStatusHistory(array $statuses): self
    {
        $this->getResource()->setStatusHistory($statuses);

        return $this;
    }

    public function getStatusHistory(): array
    {
        return $this->getResource()->getStatusHistory();
    }

    public function setItems(?array $items): self
    {
        $this->getResource()->setItems($items);

        return $this;
    }

    public function addItem(ShipmentItemInterface $item): self
    {
        $this->getResource()->addItem($item);

        return $this;
    }

    public function getItems(): ?array
    {
        return $this->getResource()->getItems();
    }

    public function setRegisterAt(DateTime|int|string|null $registerAt): self
    {
        $this->getResource()->setRegisterAt($registerAt);

        return $this;
    }

    public function getRegisterAt(): ?DateTime
    {
        return $this->getResource()->getRegisterAt();
    }

    public function setService(?ServiceInterface $service): self
    {
        $this->getResource()->setService($service);

        return $this;
    }

    public function getService(): ?ServiceInterface
    {
        return $this->getResource()->getService();
    }

    public function setContract(?ContractInterface $contract): self
    {
        $this->getResource()->setContract($contract);

        return $this;
    }

    public function getContract(): ?ContractInterface
    {
        return $this->getResource()->getContract();
    }

    public function setTotalValueAmount(?int $totalValueAmount): self
    {
        $this->getResource()->setTotalValueAmount($totalValueAmount);

        return $this;
    }

    public function getTotalValueAmount(): ?int
    {
        return $this->getResource()->getTotalValueAmount();
    }

    public function setTotalValueCurrency(?string $totalValueCurrency): self
    {
        $this->getResource()->setTotalValueCurrency($totalValueCurrency);

        return $this;
    }

    public function getTotalValueCurrency(): ?string
    {
        return $this->getResource()->getTotalValueCurrency();
    }

    public function setServiceCode(?string $serviceCode): self
    {
        $this->getResource()->setServiceCode($serviceCode);

        return $this;
    }

    public function getServiceCode(): ?string
    {
        return $this->getResource()->getServiceCode();
    }

    public function setTags(?array $tags): self
    {
        return $this->getResource()->setTags($tags);
    }

    public function addTag($tag): self
    {
        return $this->getResource()->addTag($tag);
    }

    public function getTags(): ?array
    {
        return $this->getResource()->getTags();
    }

    public function clearTags(): self
    {
        return $this->getResource()->clearTags();
    }

    public function setLabelMimeType(string $labelMimeType)
    {
        return $this->getResource()->setLabelMimeType($labelMimeType);
    }

    public function setCollection(?CollectionInterface $collection): self
    {
        return $this->getResource()->setCollection($collection);
    }

    public function getCollection(): ?CollectionInterface
    {
        return $this->getResource()->getCollection();
    }

    /**
     * This function puts all object properties in an array and returns it.
     */
    public function jsonSerialize(): array
    {
        $values = get_object_vars($this);
        unset($values['resource']);
        unset($values['api']);
        unset($values['uri']);

        return $this->arrayValuesToArray($values);
    }
}
