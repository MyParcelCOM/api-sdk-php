<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources;

use JsonSerializable;
use MyParcelCom\ApiSdk\Enums\TaxTypeEnum;
use MyParcelCom\ApiSdk\Exceptions\ResourceFactoryException;
use MyParcelCom\ApiSdk\MyParcelComApiInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\AddressInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\CarrierInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\CarrierStatusInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\CollectionTimeInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\CustomsInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ErrorInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\FileInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\OpeningHourInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\OrganizationInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\PhysicalPropertiesInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\PickUpDropOffLocationInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\PositionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\RegionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceFactoryInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceProxyInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceOptionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceRateInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentItemInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentStatusInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\StatusInterface;
use MyParcelCom\ApiSdk\Resources\Proxy\CarrierProxy;
use MyParcelCom\ApiSdk\Resources\Proxy\CollectionProxy;
use MyParcelCom\ApiSdk\Resources\Proxy\ContractProxy;
use MyParcelCom\ApiSdk\Resources\Proxy\FileProxy;
use MyParcelCom\ApiSdk\Resources\Proxy\FileStreamProxy;
use MyParcelCom\ApiSdk\Resources\Proxy\ManifestProxy;
use MyParcelCom\ApiSdk\Resources\Proxy\OrganizationProxy;
use MyParcelCom\ApiSdk\Resources\Proxy\RegionProxy;
use MyParcelCom\ApiSdk\Resources\Proxy\ServiceOptionProxy;
use MyParcelCom\ApiSdk\Resources\Proxy\ServiceProxy;
use MyParcelCom\ApiSdk\Resources\Proxy\ShipmentProxy;
use MyParcelCom\ApiSdk\Resources\Proxy\ShipmentStatusProxy;
use MyParcelCom\ApiSdk\Resources\Proxy\ShopProxy;
use MyParcelCom\ApiSdk\Resources\Proxy\StatusProxy;
use MyParcelCom\ApiSdk\Utils\StringUtils;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;

class ResourceFactory implements ResourceFactoryInterface, ResourceProxyInterface
{
    protected MyParcelComApiInterface $api;

    /**
     * Mapping of resource types and interface to concrete implementation.
     * Note that resources with a defined resource factory are not included here.
     */
    private array $typeFactory = [
        ResourceInterface::TYPE_CARRIER         => Carrier::class,
        ResourceInterface::TYPE_COLLECTION      => Collection::class,
        ResourceInterface::TYPE_CONTRACT        => Contract::class,
        ResourceInterface::TYPE_MANIFEST        => Manifest::class,
        ResourceInterface::TYPE_ORGANIZATION    => Organization::class,
        ResourceInterface::TYPE_PUDO_LOCATION   => PickUpDropOffLocation::class,
        ResourceInterface::TYPE_REGION          => Region::class,
        ResourceInterface::TYPE_SERVICE_OPTION  => ServiceOption::class,
        ResourceInterface::TYPE_SHIPMENT_STATUS => ShipmentStatus::class,
        ResourceInterface::TYPE_SHOP            => Shop::class,
        ResourceInterface::TYPE_STATUS          => Status::class,

        AddressInterface::class               => Address::class,
        CarrierInterface::class               => Carrier::class,
        CarrierStatusInterface::class         => CarrierStatus::class,
        CollectionTimeInterface::class        => CollectionTime::class,
        ContractInterface::class              => Contract::class,
        CustomsInterface::class               => Customs::class,
        ErrorInterface::class                 => Error::class,
        OpeningHourInterface::class           => OpeningHour::class,
        OrganizationInterface::class          => Organization::class,
        PhysicalPropertiesInterface::class    => PhysicalProperties::class,
        PositionInterface::class              => Position::class,
        PickUpDropOffLocationInterface::class => PickUpDropOffLocation::class,
        RegionInterface::class                => Region::class,
        ServiceOptionInterface::class         => ServiceOption::class,
        ShipmentStatusInterface::class        => ShipmentStatus::class,
        ShopInterface::class                  => Shop::class,
        StatusInterface::class                => Status::class,
    ];

    /**
     * Mapping of resource types to proxies. These are mainly used to proxy
     * resources that are part of relationships.
     */
    private array $proxies = [
        ResourceInterface::TYPE_CARRIER         => CarrierProxy::class,
        ResourceInterface::TYPE_COLLECTION      => CollectionProxy::class,
        ResourceInterface::TYPE_CONTRACT        => ContractProxy::class,
        ResourceInterface::TYPE_FILE            => FileProxy::class,
        ResourceInterface::TYPE_MANIFEST        => ManifestProxy::class,
        ResourceInterface::TYPE_ORGANIZATION    => OrganizationProxy::class,
        ResourceInterface::TYPE_REGION          => RegionProxy::class,
        ResourceInterface::TYPE_SERVICE         => ServiceProxy::class,
        ResourceInterface::TYPE_SERVICE_OPTION  => ServiceOptionProxy::class,
        ResourceInterface::TYPE_SHIPMENT        => ShipmentProxy::class,
        ResourceInterface::TYPE_SHIPMENT_STATUS => ShipmentStatusProxy::class,
        ResourceInterface::TYPE_SHOP            => ShopProxy::class,
        ResourceInterface::TYPE_STATUS          => StatusProxy::class,
    ];

    public function __construct()
    {
        $shipmentFactory = [$this, 'shipmentFactory'];
        $serviceFactory = [$this, 'serviceFactory'];
        $serviceRateFactory = [$this, 'serviceRateFactory'];
        $fileFactory = [$this, 'fileFactory'];
        $shipmentItemFactory = [$this, 'shipmentItemFactory'];
        $customsFactory = [$this, 'customsFactory'];
        $carrierFactory = [$this, 'carrierFactory'];

        $this->setFactoryForType(ResourceInterface::TYPE_SHIPMENT, $shipmentFactory);
        $this->setFactoryForType(ShipmentInterface::class, $shipmentFactory);

        $this->setFactoryForType(ResourceInterface::TYPE_SERVICE, $serviceFactory);
        $this->setFactoryForType(ServiceInterface::class, $serviceFactory);

        $this->setFactoryForType(ResourceInterface::TYPE_SERVICE_RATE, $serviceRateFactory);
        $this->setFactoryForType(ServiceRateInterface::class, $serviceRateFactory);

        $this->setFactoryForType(ResourceInterface::TYPE_FILE, $fileFactory);
        $this->setFactoryForType(FileInterface::class, $fileFactory);

        $this->setFactoryForType(ShipmentItemInterface::class, $shipmentItemFactory);

        $this->setFactoryForType(CustomsInterface::class, $customsFactory);

        $this->setFactoryForType(ResourceInterface::TYPE_CARRIER, $carrierFactory);
    }

    /**
     * Shipment factory method that creates proxies for all relationships.
     */
    protected function shipmentFactory(array &$properties): Shipment
    {
        $shipment = new Shipment();

        if (isset($properties['attributes']['price']['amount'])) {
            $shipment->setPrice($properties['attributes']['price']['amount']);
            if (isset($properties['attributes']['price']['currency'])) {
                $shipment->setCurrency($properties['attributes']['price']['currency']);
            }

            unset($properties['attributes']['price']);
        }

        if (isset($properties['attributes']['total_value']['amount'])) {
            $shipment->setTotalValueAmount($properties['attributes']['total_value']['amount']);
            if (isset($properties['attributes']['total_value']['currency'])) {
                $shipment->setTotalValueCurrency($properties['attributes']['total_value']['currency']);
            }

            unset($properties['attributes']['total_value']);
        }

        if (isset($properties['attributes']['pickup_location']['code'])) {
            $shipment->setPickupLocationCode($properties['attributes']['pickup_location']['code']);
        }

        if (isset($properties['attributes']['pickup_location']['address'])) {
            /** @var AddressInterface $pudoAddress */
            $pudoAddress = $this->create(
                AddressInterface::class,
                $properties['attributes']['pickup_location']['address'],
            );

            $shipment->setPickupLocationAddress($pudoAddress);

            unset($properties['attributes']['pickup_location']);
        }

        if (isset($properties['id'])) {
            $shipment->setStatusHistoryCallback(function () use ($properties) {
                return $this->api->getResourcesFromUri(
                    str_replace(
                        '{shipment_id}',
                        $properties['id'],
                        MyParcelComApiInterface::PATH_SHIPMENT_STATUSES,
                    ),
                );
            });
        }

        if (isset($properties['attributes']['sender_tax_identification_numbers'])) {
            foreach ($properties['attributes']['sender_tax_identification_numbers'] as $taxIdentificationNumber) {
                $shipment->addSenderTaxIdentificationNumber(
                    (new TaxIdentificationNumber())
                        ->setType(new TaxTypeEnum($taxIdentificationNumber['type']))
                        ->setNumber($taxIdentificationNumber['number'])
                        ->setCountryCode($taxIdentificationNumber['country_code'])
                        ->setDescription($taxIdentificationNumber['description'] ?? null),
                );
            }
            unset($properties['attributes']['sender_tax_identification_numbers']);
        }

        if (isset($properties['attributes']['recipient_tax_identification_numbers'])) {
            foreach ($properties['attributes']['recipient_tax_identification_numbers'] as $taxIdentificationNumber) {
                $shipment->addRecipientTaxIdentificationNumber(
                    (new TaxIdentificationNumber())
                        ->setType(new TaxTypeEnum($taxIdentificationNumber['type']))
                        ->setNumber($taxIdentificationNumber['number'])
                        ->setCountryCode($taxIdentificationNumber['country_code'])
                        ->setDescription($taxIdentificationNumber['description'] ?? null),
                );
            }
            unset($properties['attributes']['recipient_tax_identification_numbers']);
        }

        return $shipment;
    }

    protected function serviceFactory(array &$properties): Service
    {
        $service = new Service();

        if (isset($properties['attributes']['transit_time']['min'])) {
            $service->setTransitTimeMin($properties['attributes']['transit_time']['min']);

            unset($properties['attributes']['transit_time']['min']);
        }

        if (isset($properties['attributes']['transit_time']['max'])) {
            $service->setTransitTimeMax($properties['attributes']['transit_time']['max']);

            unset($properties['attributes']['transit_time']['max']);
        }

        // TODO: Remove this when we remove the region relationships from services.
        unset($properties['relationships']['region_from']);
        unset($properties['relationships']['region_to']);

        if (isset($properties['id'])) {
            $service->setServiceRatesCallback(function (array $filters = []) use ($properties) {
                $filters['has_active_contract'] = 'true';
                $filters['service'] = $properties['id'];

                return $this->api->getServiceRates($filters)->get();
            });
        }

        return $service;
    }

    protected function serviceRateFactory(array &$properties): ServiceRate
    {
        $serviceRate = new ServiceRate();

        if (isset($properties['attributes']['price']['amount'])) {
            $serviceRate->setPrice($properties['attributes']['price']['amount']);
            $serviceRate->setCurrency($properties['attributes']['price']['currency']);

            unset($properties['attributes']['price']);
        }

        if (isset($properties['attributes']['fuel_surcharge']['amount'])) {
            $serviceRate->setFuelSurchargeAmount($properties['attributes']['fuel_surcharge']['amount']);
            $serviceRate->setFuelSurchargeCurrency($properties['attributes']['fuel_surcharge']['currency']);

            unset($properties['attributes']['fuel_surcharge']);
        }

        if (isset($properties['relationships']['service_options'])) {
            $serviceOptions = $properties['relationships']['service_options']['data'];

            foreach ($serviceOptions as $serviceOption) {
                $serviceOptionProxy = (new ServiceOptionProxy())
                    ->setMyParcelComApi($this->api)
                    ->setId($serviceOption['id']);

                if (isset($serviceOption['meta']['price']['amount'])) {
                    $serviceOptionProxy
                        ->setPrice($serviceOption['meta']['price']['amount'])
                        ->setCurrency($serviceOption['meta']['price']['currency']);
                }

                if (isset($serviceOption['meta']['included'])) {
                    $serviceOptionProxy->setIncluded($serviceOption['meta']['included']);
                }

                $serviceRate->addServiceOption($serviceOptionProxy);
            }

            unset($properties['relationships']['service_options']);
        }

        if (isset($properties['meta']['bracket_price']['amount'])) {
            $serviceRate->setBracketPrice($properties['meta']['bracket_price']['amount']);
            $serviceRate->setBracketCurrency($properties['meta']['bracket_price']['currency']);

            unset($properties['meta']['bracket_price']);
        }

        if (isset($properties['id'])) {
            $serviceRate->setResolveDynamicRateForShipmentCallback(
                function (ShipmentInterface $shipment, ServiceRateInterface $serviceRate) {
                    $serviceRates = $this->api->resolveDynamicServiceRates($shipment, $serviceRate);

                    return $serviceRates[0] ?? $serviceRate;
                },
            );
        }

        return $serviceRate;
    }

    /**
     * Factory method for creating file resources, adds proxy streams to the
     * file for requesting the file data.
     */
    protected function fileFactory(array &$properties): File
    {
        $file = new File();

        if (!isset($properties['attributes']['formats'])) {
            return $file;
        }

        array_walk($properties['attributes']['formats'], function ($format) use ($file, $properties) {
            $file->setStream(
                new FileStreamProxy($properties['id'], $format['mime_type'], $this->api),
                $format['mime_type'],
            );
        });

        return $file;
    }

    protected function shipmentItemFactory(array &$attributes): ShipmentItem
    {
        $item = new ShipmentItem();

        if (isset($attributes['item_value']['amount'])) {
            $item->setItemValue($attributes['item_value']['amount']);
            $item->setCurrency($attributes['item_value']['currency']);

            unset($attributes['item_value']);
        }

        return $item;
    }

    protected function customsFactory(array &$attributes): Customs
    {
        $customs = new Customs();

        if (isset($attributes['shipping_value']['amount'])) {
            $customs->setShippingValueAmount($attributes['shipping_value']['amount']);
            $customs->setShippingValueCurrency($attributes['shipping_value']['currency']);

            unset($attributes['shipping_value']);
        }

        return $customs;
    }

    protected function carrierFactory(array &$attributes): Carrier
    {
        $carrier = new Carrier();

        if (isset($attributes['attributes']['collections'])) {
            $carrier->setOffersCollections($attributes['attributes']['collections']['offers_collections']);
            $carrier->setVoidsRegisteredCollections($attributes['attributes']['collections']['voids_registered_collections']);
            $carrier->setAllowsAddingRegisteredShipmentsToCollection($attributes['attributes']['collections']['allows_adding_registered_shipments_to_collection']);

            unset($attributes['attributes']['collections']);
        }

        return $carrier;
    }

    public function create(string $type, array $properties = []): ResourceInterface|JsonSerializable
    {
        $resource = $this->createResource($type, $properties);

        /**
         * Hydrate the top level properties, this is usually only used for
         * nested classes (like Address).
         */
        $this->hydrate($resource, $properties);

        /**
         * Hydrate all the resource attributes.
         */
        if (!empty($properties['attributes'])) {
            $this->hydrate($resource, $properties['attributes']);
        }

        /**
         * Some resources have extra metadata (eg. pickup-dropoff-locations.meta.distance).
         * These should hydrate the model as well.
         */
        if (!empty($properties['meta'])) {
            $this->hydrate($resource, $properties['meta']);
        }

        /**
         * Most resources have relationships. We will hydrate these with proxies.
         */
        if (!empty($properties['relationships'])) {
            $this->hydrateRelationships($resource, $properties['relationships']);
        }

        return $resource;
    }

    /**
     * Set a factory method or class string for given resource type.
     */
    public function setFactoryForType(string $type, callable|string $factory): void
    {
        if (!is_callable($factory) && !class_exists($factory)) {
            throw new ResourceFactoryException(
                sprintf(
                    'Cannot assign factory for type `%s`, given factory was not a valid callable or class',
                    $type,
                ),
            );
        }

        $this->typeFactory[$type] = $factory;
    }

    /**
     * Checks if the given type has a factory associated with it.
     */
    protected function typeHasFactory(string $type): bool
    {
        return array_key_exists($type, $this->typeFactory);
    }

    /**
     * Create a resource for type using its factory or the class associated with it.
     *
     * @throws ResourceFactoryException
     */
    protected function createResource(string $type, array &$attributes = []): ResourceInterface|JsonSerializable
    {
        if (!$this->typeHasFactory($type)) {
            throw new ResourceFactoryException(
                sprintf(
                    'Could not create resource of type `%s`, no class or factory specified',
                    $type,
                ),
            );
        }

        $factory = $this->typeFactory[$type];

        if (is_callable($factory)) {
            return $factory($attributes);
        } elseif (class_exists($factory)) {
            return new $factory();
        }

        throw new ResourceFactoryException(
            sprintf(
                'Could not determine how to create a resource of type `%s`, no factory method or class defined',
                $type,
            ),
        );
    }

    /**
     * Hydrates resource with given attributes. Uses reflection do determine if
     * other resources need to be created and tries to instantiate them where
     * possible.
     * @todo Refactor this huge moth.
     */
    protected function hydrate(
        ResourceInterface|JsonSerializable $resource,
        array $attributes,
    ): ResourceInterface|JsonSerializable {
        array_walk($attributes, function ($value, $key) use ($resource) {
            $setter = 'set' . StringUtils::snakeToPascalCase($key);

            // Can't use setter if it doesn't exist.
            if (!method_exists($resource, $setter)) {
                return;
            }

            $param = $this->getFillableParam($resource, $setter);
            // Can't use the setter if we cannot determine the param to fill.
            if ($param === null) {
                return;
            }

            if ($param->getType() instanceof ReflectionNamedType && $param->getType()->getName() === 'array') {
                // Can't use setter if the types don't match.
                if (!is_array($value)) {
                    return;
                }

                $adder = 'add' . rtrim(rtrim(StringUtils::snakeToPascalCase($key), 's'), 'e');

                if (($adderParam = $this->getFillableParam($resource, $adder)) !== null) {
                    $adderParamClass = $adderParam->getType() && !$adderParam->getType()->isBuiltin()
                        ? new ReflectionClass($adderParam->getType()->getName())
                        : null;

                    if ($adderParamClass !== null) {
                        $className = $adderParamClass->getName();

                        foreach ($value as $entry) {
                            if (is_array($entry) && $this->typeHasFactory($className)) {
                                $resource->$adder($this->create($className, $entry));

                                continue;
                            }
                            if ($entry instanceof $className) {
                                $resource->$adder($entry);

                                continue;
                            }
                        }

                        return;
                    }
                }
            }

            $paramClass = $param->getType() instanceof ReflectionNamedType && !$param->getType()->isBuiltin()
                ? new ReflectionClass($param->getType()->getName())
                : null;
            if ($paramClass !== null) {
                $className = $paramClass->getName();

                if (is_array($value) && $this->typeHasFactory($className)) {
                    $resource->$setter($this->create($className, $value));

                    return;
                }

                if (!$value instanceof $className) {
                    return;
                }
            }

            $resource->$setter($value);
        });

        return $resource;
    }

    /**
     * Hydrates the given resource with its relationships, uses proxies to
     * accomplish this.
     */
    protected function hydrateRelationships(ResourceInterface $resource, array $relationships): ResourceInterface
    {
        foreach ($relationships as $name => $relationship) {
            // If the relationship is empty, we skip it.
            if (empty($relationship['data'])) {
                continue;
            }

            // If there is no setter for the relationship, we skip it.
            $setter = 'set' . StringUtils::snakeToPascalCase($name);
            if (!method_exists($resource, $setter)) {
                continue;
            }

            $uri = $relationship['links']['related'] ?? null;
            $value = isset($relationship['data']['type'])
                ? $this->createProxy($relationship['data'], $uri)
                : array_map(function (array $identifier) {
                    return $this->createProxy($identifier);
                }, $relationship['data']);

            $resource->$setter($value);
        }

        return $resource;
    }

    private function createProxy(array $identifier, ?string $uri = null): ResourceInterface
    {
        if (empty($identifier['type'])) {
            throw new ResourceFactoryException('Cannot create proxy, no `type` available in identifier.');
        }
        if (empty($identifier['id'])) {
            throw new ResourceFactoryException('Cannot create proxy, no `id` available in identifier.');
        }
        $type = $identifier['type'];

        if (empty($this->proxies[$type])) {
            throw new ResourceFactoryException(
                "Cannot create proxy, no proxy configured for resource with type `{$type}`.",
            );
        }

        $resource = new $this->proxies[$type]();
        if ($resource instanceof ResourceProxyInterface) {
            $resource->setMyParcelComApi($this->api);
            if (method_exists($resource, 'setResourceUri')) {
                $resource->setResourceUri($uri);
            }
        }
        $this->hydrate($resource, $identifier);

        return $resource;
    }

    private function getFillableParam(
        ResourceInterface|JsonSerializable $resource,
        string $method,
    ): ?ReflectionParameter {
        // Check if the method exists, if it doesn't return null.
        if (!method_exists($resource, $method)) {
            return null;
        }

        $params = (new ReflectionMethod($resource, $method))
            ->getParameters();

        // Can't use setter if it requires no params.
        if (count($params) === 0) {
            return null;
        }

        // Check if all parameters after the 1st are optional. If not, we
        // cannot use the setter.
        foreach (array_slice($params, 1) as $param) {
            if (!$param->isOptional()) {
                return null;
            }
        }

        return reset($params);
    }

    public function setMyParcelComApi(MyParcelComApiInterface $api): self
    {
        $this->api = $api;

        return $this;
    }
}
