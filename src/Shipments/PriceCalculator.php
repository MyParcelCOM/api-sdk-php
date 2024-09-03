<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Shipments;

use MyParcelCom\ApiSdk\Enums\DimensionUnitEnum;
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

        $billableWeight = $this->getBillableWeight($shipment);

        if ($billableWeight === null
            || $billableWeight < $serviceRate->getWeightMin()
            || $billableWeight > $serviceRate->getWeightMax()
        ) {
            throw new CalculationException(
                'Could not calculate price for the given service rate since it does not support the shipment weight.'
            );
        }

        if ($serviceRate->getPrice() === null && !empty($serviceRate->getWeightBracket())) {
            $bracketPrice = $serviceRate->getBracketPrice()
                ? $serviceRate->getBracketPrice()
                : $serviceRate->calculateBracketPrice($billableWeight);
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

    private function getBillableWeight(ShipmentInterface $shipment): ?int
    {
        $weight = $shipment->getPhysicalProperties()?->getWeight();

        if ($weight === null) {
            return null;
        }

        if ($shipment->getService()?->usesVolumetricWeight() && $shipment->calculateVolume()) {
            $divisor = $shipment->getService()->getVolumetricWeightDivisor() * $shipment->getContract()->getVolumetricWeightDivisorFactor();
            $volumetricWeight = (int) ceil($shipment->calculateVolume() / $divisor);

            return max($volumetricWeight, $weight);
        }

        return $weight;
    }

    /**
     * @throws InvalidResourceException
     */
    private function validateShipment(ShipmentInterface $shipment): void
    {
        if ($this->getBillableWeight($shipment) === null) {
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
            'weight'              => $this->getBillableWeight($shipment),
            'volume'              => $shipment->calculateVolume(DimensionUnitEnum::DM3),
        ]);

        $shipmentOptionIds = array_map(
            fn (ServiceOptionInterface $serviceOption) => $serviceOption->getId(),
            $shipment->getServiceOptions(),
        );

        $serviceRates = array_filter($serviceRates, function (ServiceRateInterface $serviceRate) use ($shipmentOptionIds) {
            $serviceRateOptionIds = array_map(
                fn (ServiceOptionInterface $serviceOption) => $serviceOption->getId(),
                $serviceRate->getServiceOptions(),
            );

            return empty(array_diff($shipmentOptionIds, $serviceRateOptionIds));
        });

        // Because this shipment has a contract + service + weight, we do not need to sort this array with max 1 result.
        $serviceRate = reset($serviceRates);

        if (!$serviceRate) {
            throw new CalculationException('Cannot find a matching service rate for given shipment');
        }

        return $serviceRate;
    }
}
