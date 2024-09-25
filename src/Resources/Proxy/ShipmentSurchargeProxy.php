<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Proxy;

use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceProxyInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentSurchargeInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\ProxiesResource;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;

/**
 * @method ShipmentSurchargeInterface getResource()
 */
class ShipmentSurchargeProxy implements ShipmentSurchargeInterface, ResourceProxyInterface
{
    use JsonSerializable;
    use ProxiesResource;
    use Resource;

    private ?string $id = null;

    private string $type = ResourceInterface::TYPE_SHIPMENT_SURCHARGE;

    public function setName(string $name): self
    {
        return $this->getResource()->setName($name);
    }

    public function getName(): ?string
    {
        return $this->getResource()->getName();
    }

    public function setDescription(?string $description): self
    {
        return $this->getResource()->setDescription($description);
    }

    public function getDescription(): ?string
    {
        return $this->getResource()->getDescription();
    }

    public function setFeeAmount(int $amount): self
    {
        return $this->getResource()->setFeeAmount($amount);
    }

    public function getFeeAmount(): ?int
    {
        return $this->getResource()->getFeeAmount();
    }

    public function setFeeCurrency(string $currency): self
    {
        return $this->getResource()->setFeeCurrency($currency);
    }

    public function getFeeCurrency(): ?string
    {
        return $this->getResource()->getFeeCurrency();
    }

    public function getShipment(): ?ShipmentInterface
    {
        return $this->getResource()->getShipment();
    }

    public function setShipment(ShipmentInterface $shipment): self
    {
        return $this->getResource()->setShipment($shipment);
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
