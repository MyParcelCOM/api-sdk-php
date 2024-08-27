<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Proxy;

use MyParcelCom\ApiSdk\Resources\Contract;
use MyParcelCom\ApiSdk\Resources\Interfaces\CarrierInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceProxyInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\ProxiesResource;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;

/**
 * @method Contract getResource()
 */
class ContractProxy implements ContractInterface, ResourceProxyInterface
{
    use JsonSerializable;
    use ProxiesResource;
    use Resource;

    private ?string $id = null;

    private string $type = ResourceInterface::TYPE_CONTRACT;

    public function setName(string $name): self
    {
        $this->getResource()->setName($name);

        return $this;
    }

    public function getName(): string
    {
        return $this->getResource()->getName();
    }

    public function setCurrency(string $currency): self
    {
        $this->getResource()->setCurrency($currency);

        return $this;
    }

    public function getCurrency(): string
    {
        return $this->getResource()->getCurrency();
    }

    public function setCarrier(CarrierInterface $carrier): self
    {
        $this->getResource()->setCarrier($carrier);

        return $this;
    }

    public function getCarrier(): CarrierInterface
    {
        return $this->getResource()->getCarrier();
    }

    public function setStatus(string $status): self
    {
        $this->getResource()->setStatus($status);

        return $this;
    }

    public function getStatus(): string
    {
        return $this->getResource()->getStatus();
    }

    public function setVolumetricWeightDivisorFactor(float $factor): self
    {
        $this->getResource()->setVolumetricWeightDivisorFactor($factor);

        return $this;
    }

    public function getVolumetricWeightDivisorFactor(): float
    {
        return $this->getResource()->getVolumetricWeightDivisorFactor();
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
