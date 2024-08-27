<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Shipments;

use MyParcelCom\ApiSdk\Exceptions\CalculationException;
use MyParcelCom\ApiSdk\Exceptions\InvalidResourceException;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceOptionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceRateInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;

class PriceCalculator
{
    /**
     * Calculate the total price of given shipment based off its contract, service and weight.
     */
    public function calculate(ShipmentInterface $shipment, ?ServiceRateInterface $serviceRate = null): ?int
    {
        if ($serviceRate === null) {
            $serviceRate = $this->determineServiceRateForShipment($shipment);
        }

        if ($serviceRate->isDynamic()) {
            $serviceRate = $serviceRate->resolveDynamicRateForShipment($shipment);
        }

        if ($shipment->getPhysicalProperties() === null
            || $shipment->getPhysicalProperties()->getWeight() === null
            || $shipment->getPhysicalProperties()->getWeight() < $serviceRate->getWeightMin()
            || $shipment->getPhysicalProperties()->getWeight() > $serviceRate->getWeightMax()) {
            throw new CalculationException(
                'Could not calculate price for the given service rate since it does not support the shipment weight.'
            );
        }

        if ($serviceRate->getPrice() === null && !empty($serviceRate->getWeightBracket())) {
            $bracketPrice = $serviceRate->getBracketPrice()
                ? $serviceRate->getBracketPrice()
                : $serviceRate->calculateBracketPrice($shipment->getPhysicalProperties()->getWeight());
            $bracketCurrency = $serviceRate->getBracketCurrency()
                ? $serviceRate->getBracketCurrency()
                : $serviceRate->getContract()->getCurrency();

            $serviceRate->setPrice($bracketPrice);
            $serviceRate->setCurrency($bracketCurrency);
        }

        $serviceRatePrice = $serviceRate->getPrice();
        $fuelSurchargePrice = $serviceRate->getFuelSurchargeAmount() ?: 0;
        $serviceOptionPrice = $this->calculateOptionsPrice($shipment, $serviceRate);

        if ($serviceRatePrice === null || $serviceOptionPrice === null) {
            return null;
        }

        return $serviceOptionPrice + $fuelSurchargePrice + $serviceRatePrice;
    }

    /**
     * Calculate the price based on the selected options for given shipment.
     */
    public function calculateOptionsPrice(ShipmentInterface $shipment, ?ServiceRateInterface $serviceRate = null): ?int
    {
        if ($serviceRate === null) {
            $serviceRate = $this->determineServiceRateForShipment($shipment);
        }

        $price = 0;

        $prices = [];
        foreach ($serviceRate->getServiceOptions() as $option) {
            $prices[$option->getId()] = $option->getPrice();
        }

        foreach ($shipment->getServiceOptions() as $option) {
            if (!array_key_exists($option->getId(), $prices)) {
                throw new CalculationException(
                    'Cannot calculate a price for given shipment; invalid option: ' . $option->getId()
                );
            }

            $optionPrice = $prices[$option->getId()];

            if ($optionPrice === null) {
                return null;
            }

            $price += $optionPrice;
        }

        return (int) $price;
    }

    /**
     * @throws InvalidResourceException
     */
    private function validateShipment(ShipmentInterface $shipment): void
    {
        if ($shipment->getPhysicalProperties() === null
            || $shipment->getPhysicalProperties()->getWeight() === null
            || $shipment->getPhysicalProperties()->getWeight() < 0) {
            throw new InvalidResourceException(
                'Cannot calculate shipment price without a valid shipment weight.'
            );
        }

        if ($shipment->getContract() === null) {
            throw new InvalidResourceException(
                'Cannot calculate shipment price without a set contract.'
            );
        }
        if ($shipment->getService() === null) {
            throw new InvalidResourceException(
                'Cannot calculate shipment price without a set service.'
            );
        }
    }

    private function determineServiceRateForShipment(ShipmentInterface $shipment): ServiceRateInterface
    {
        $this->validateShipment($shipment);

        $serviceRates = $shipment->getService()->getServiceRates([
            'has_active_contract' => 'true',
            'contract'            => $shipment->getContract(),
            'weight'              => $shipment->getPhysicalProperties()->getWeight(),
            'volume'              => $shipment->calculateVolumeInDm3(),
        ]);

        $shipmentOptionIds = array_map(function (ServiceOptionInterface $serviceOption) {
            return $serviceOption->getId();
        }, $shipment->getServiceOptions());

        array_filter($serviceRates, function (ServiceRateInterface $serviceRate) use ($shipmentOptionIds) {
            $serviceRateOptionIds = array_map(function (ServiceOptionInterface $serviceOption) {
                return $serviceOption->getId();
            }, $serviceRate->getServiceOptions());

            return empty(array_diff($shipmentOptionIds, $serviceRateOptionIds));
        });

        $serviceRate = reset($serviceRates);

        if (!$serviceRate) {
            throw new CalculationException(
                'Cannot find a matching service rate for given shipment'
            );
        }

        return $serviceRate;
    }
}
