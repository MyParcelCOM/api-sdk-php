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
        $this->getResource()->setName($name);

        return $this;
    }

    public function getName(): ?string
    {
        return $this->getResource()->getName();
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

    public function setFeeAmount(int $amount): self
    {
        $this->getResource()->setFeeAmount($amount);

        return $this;
    }

    public function getFeeAmount(): ?int
    {
        return $this->getResource()->getFeeAmount();
    }

    public function setFeeCurrency(string $currency): self
    {
        $this->getResource()->setFeeCurrency($currency);

        return $this;
    }

    public function getFeeCurrency(): ?string
    {
        return $this->getResource()->getFeeCurrency();
    }

    public function getShipment(): ?ShipmentInterface
    {
        return $this->getResource()->getShipment();
    }

    public function setShipment(?ShipmentInterface $shipment): self
    {
        $this->getResource()->setShipment($shipment);

        return $this;
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
