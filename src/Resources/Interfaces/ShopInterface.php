<?php

namespace MyParcelCom\ApiSdk\Resources\Interfaces;

use DateTime;

interface ShopInterface extends ResourceInterface
{
    /**
     * @param string $id
     * @return $this
     */
    public function setId($id);

    /**
     * @param $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param AddressInterface $billingAddress
     * @return $this
     */
    public function setBillingAddress(AddressInterface $billingAddress);

    /**
     * @return AddressInterface
     */
    public function getBillingAddress();

    /**
     * @param AddressInterface $senderAddress
     * @return $this
     */
    public function setSenderAddress(AddressInterface $senderAddress);

    /**
     * @return AddressInterface
     */
    public function getSenderAddress();

    /**
     * @param AddressInterface $returnAddress
     * @return $this
     */
    public function setReturnAddress(AddressInterface $returnAddress);

    /**
     * @return AddressInterface
     */
    public function getReturnAddress();

    /**
     * @param RegionInterface $region
     * @return $this
     */
    public function setRegion(RegionInterface $region);

    /**
     * @return RegionInterface
     */
    public function getRegion();

    /**
     * @param int|DateTime $time
     * @return $this
     */
    public function setCreatedAt($time);

    /**
     * @return DateTime
     */
    public function getCreatedAt();
}
