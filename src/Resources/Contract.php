<?php

namespace MyParcelCom\ApiSdk\Resources;

use MyParcelCom\ApiSdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceGroupInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceInsuranceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceOptionInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;

class Contract implements ContractInterface
{
    use JsonSerializable;

    const ATTRIBUTE_GROUPS = 'groups';
    const ATTRIBUTE_SERVICE_OPTIONS = 'service_options';
    const ATTRIBUTE_INSURANCES = 'insurances';

    /** @var string */
    private $id;
    /** @var string */
    private $type = ResourceInterface::TYPE_CONTRACT;
    /** @var array */
    private $attributes = [
        self::ATTRIBUTE_GROUPS          => [],
        self::ATTRIBUTE_SERVICE_OPTIONS => [],
        self::ATTRIBUTE_INSURANCES      => [],
    ];

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setGroups(array $groups)
    {
        $this->attributes[self::ATTRIBUTE_GROUPS] = [];

        array_walk($groups, function ($group) {
            $this->addGroup($group);
        });

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addGroup(ServiceGroupInterface $group)
    {
        $this->attributes[self::ATTRIBUTE_GROUPS][] = $group;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getGroups()
    {
        return $this->attributes[self::ATTRIBUTE_GROUPS];
    }

    /**
     * {@inheritdoc}
     */
    public function setServiceOptions(array $options)
    {
        $this->attributes[self::ATTRIBUTE_SERVICE_OPTIONS] = [];

        array_walk($options, function ($option) {
            $this->addServiceOption($option);
        });

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addServiceOption(ServiceOptionInterface $option)
    {
        $this->attributes[self::ATTRIBUTE_SERVICE_OPTIONS][] = $option;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getServiceOptions()
    {
        return $this->attributes[self::ATTRIBUTE_SERVICE_OPTIONS];
    }

    /**
     * {@inheritdoc}
     */
    public function setInsurances(array $insurances)
    {
        $this->attributes[self::ATTRIBUTE_INSURANCES] = [];

        array_walk($insurances, function ($insurance) {
            $this->addInsurance($insurance);
        });

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addInsurance(ServiceInsuranceInterface $insurance)
    {
        $this->attributes[self::ATTRIBUTE_INSURANCES][] = $insurance;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getInsurances()
    {
        return $this->attributes[self::ATTRIBUTE_INSURANCES];
    }
}
