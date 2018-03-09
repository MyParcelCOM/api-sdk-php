<?php

namespace MyParcelCom\ApiSdk\Resources\Proxy;

use MyParcelCom\ApiSdk\Resources\Interfaces\CarrierInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\RegionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceProxyInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\ProxiesResource;

class ServiceProxy implements ServiceInterface, ResourceProxyInterface
{
    use JsonSerializable;
    use ProxiesResource;

    /** @var string */
    private $id;
    /** @var string */
    private $type = ResourceInterface::TYPE_SERVICE;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->getResource()->setName($name);

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getResource()->getName();
    }

    /**
     * @param string $packageType
     * @return $this
     */
    public function setPackageType($packageType)
    {
        $this->getResource()->setPackageType($packageType);

        return $this;
    }

    /**
     * @return string
     */
    public function getPackageType()
    {
        return $this->getResource()->getPackageType();
    }

    /**
     * @return int|null
     */
    public function getTransitTimeMin()
    {
        return $this->getResource()->getTransitTimeMin();
    }

    /**
     * @param int|null $transitTimeMin
     * @return $this
     */
    public function setTransitTimeMin($transitTimeMin)
    {
        $this->getResource()->setTransitTimeMin($transitTimeMin);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTransitTimeMax()
    {
        return $this->getResource()->getTransitTimeMax();
    }

    /**
     * @param int|null $transitTimeMax
     * @return $this
     */
    public function setTransitTimeMax($transitTimeMax)
    {
        $this->getResource()->setTransitTimeMax($transitTimeMax);

        return $this;
    }

    /**
     * @param CarrierInterface $carrier
     * @return $this
     */
    public function setCarrier(CarrierInterface $carrier)
    {
        $this->getResource()->setCarrier($carrier);

        return $this;
    }

    /**
     * @return CarrierInterface
     */
    public function getCarrier()
    {
        return $this->getResource()->getCarrier();
    }

    /**
     * @param RegionInterface $region
     * @return $this
     */
    public function setRegionFrom(RegionInterface $region)
    {
        $this->getResource()->setRegionFrom($region);

        return $this;
    }

    /**
     * @return RegionInterface
     */
    public function getRegionFrom()
    {
        return $this->getResource()->getRegionFrom();
    }

    /**
     * @param RegionInterface $region
     * @return $this
     */
    public function setRegionTo(RegionInterface $region)
    {
        $this->getResource()->setRegionTo($region);

        return $this;
    }

    /**
     * @return RegionInterface
     */
    public function getRegionTo()
    {
        return $this->getResource()->getRegionTo();
    }

    /**
     * @param ServiceContractInterface[] $serviceContracts
     * @return $this
     */
    public function setServiceContracts(array $serviceContracts)
    {
        $this->getResource()->setServiceContracts($serviceContracts);

        return $this;
    }

    /**
     * @param ServiceContractInterface $serviceContract
     * @return $this
     */
    public function addServiceContract(ServiceContractInterface $serviceContract)
    {
        $this->getResource()->addServiceContract($serviceContract);

        return $this;
    }

    /**
     * @return ServiceContractInterface[]
     */
    public function getServiceContracts()
    {
        return $this->getResource()->getServiceContracts();
    }

    /**
     * @param string $handoverMethod
     * @return $this
     */
    public function setHandoverMethod($handoverMethod)
    {
        $this->getResource()->setHandoverMethod($handoverMethod);

        return $this;
    }

    /**
     * @return string
     */
    public function getHandoverMethod()
    {
        return $this->getResource()->getHandoverMethod();
    }

    /**
     * @param string[] $deliveryDays
     * @return $this
     */
    public function setDeliveryDays(array $deliveryDays)
    {
        $this->getResource()->setDeliveryDays($deliveryDays);

        return $this;
    }

    /**
     * @param string $deliveryDay
     * @return $this
     */
    public function addDeliveryDay($deliveryDay)
    {
        $this->getResource()->addDeliveryDay($deliveryDay);

        return $this;
    }

    /**
     * @return string[]
     */
    public function getDeliveryDays()
    {
        return $this->getResource()->getDeliveryDays();
    }

    /**
     * This function puts all object properties in an array and returns it.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $values = get_object_vars($this);
        unset($values['resource']);
        unset($values['api']);
        unset($values['uri']);

        return $this->arrayValuesToArray($values);
    }
}
