<?php

namespace MyParcelCom\Sdk\Shipments;

use MyParcelCom\Sdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ServiceOptionInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ShipmentInterface;

class ServiceMatcher
{
    /**
     * Returns true if given service can be used for the given shipment.
     *
     * @param ShipmentInterface $shipment
     * @param ServiceInterface  $service
     * @return bool
     */
    public function matches(ShipmentInterface $shipment, ServiceInterface $service)
    {
        return ($weightContracts = $this->getMatchedWeightGroups($shipment, $service->getContracts()))
            && ($optionContracts = $this->getMatchedOptions($shipment, $weightContracts))
            && $this->getMatchedInsurances($shipment, $optionContracts);
    }

    /**
     * Returns a subset of the given contracts that have weight groups that
     * match the weight of the shipment.
     *
     * @param ShipmentInterface   $shipment
     * @param ContractInterface[] $contracts
     * @return ContractInterface[]
     */
    public function getMatchedWeightGroups(ShipmentInterface $shipment, array $contracts)
    {
        $matches = [];
        foreach ($contracts as $contract) {
            foreach ($contract->getGroups() as $group) {
                if ($group->getWeightMin() <= $shipment->getWeight()
                    && $group->getWeightMax() >= $shipment->getWeight()) {
                    $matches[] = $contract;
                    continue 2;
                }
            }
        }

        return $matches;
    }

    /**
     * Returns a subset of the given contracts that have all the options that
     * the shipment requires.
     *
     * @param ShipmentInterface   $shipment
     * @param ContractInterface[] $contracts
     * @return ContractInterface[]
     */
    public function getMatchedOptions(ShipmentInterface $shipment, array $contracts)
    {
        $optionIds = array_map(function (ServiceOptionInterface $option) {
            return $option->getId();
        }, $shipment->getOptions());

        $matches = [];
        foreach ($contracts as $contract) {
            $contractOptionIds = array_map(function (ServiceOptionInterface $option) use ($optionIds) {
                return $option->getId();
            }, $contract->getOptions());

            if (!array_diff($optionIds, $contractOptionIds)) {
                $matches[] = $contract;
            }
        }

        return $matches;
    }

    /**
     * Returns a subset of the given contracts that can cover the desired
     * insurance of the shipment.
     *
     * @param ShipmentInterface   $shipment
     * @param ContractInterface[] $contracts
     * @return ContractInterface[]
     */
    public function getMatchedInsurances(ShipmentInterface $shipment, array $contracts)
    {
        if (!$shipment->getInsuranceAmount()) {
            return $contracts;
        }

        return array_filter($contracts, function (ContractInterface $contract) use ($shipment) {
            foreach ($contract->getInsurances() as $insurance) {
                if ($shipment->getInsuranceAmount() <= $insurance->getCovered()) {
                    return true;
                }
            }

            return false;
        });
    }
}
