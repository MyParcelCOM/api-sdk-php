<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk;

use GuzzleHttp\Psr7\Message;
use GuzzleHttp\Psr7\Request;
use Http\Discovery\HttpClientDiscovery;
use MyParcelCom\ApiSdk\Authentication\AuthenticatorInterface;
use MyParcelCom\ApiSdk\Collection\ArrayCollection;
use MyParcelCom\ApiSdk\Collection\CollectionInterface as ResourceCollectionInterface;
use MyParcelCom\ApiSdk\Collection\RequestCollection;
use MyParcelCom\ApiSdk\Enums\DimensionUnitEnum;
use MyParcelCom\ApiSdk\Exceptions\InvalidResourceException;
use MyParcelCom\ApiSdk\Exceptions\MyParcelComException;
use MyParcelCom\ApiSdk\Http\Contracts\HttpClient\RequestExceptionInterface;
use MyParcelCom\ApiSdk\Http\Exceptions\RequestException;
use MyParcelCom\ApiSdk\Resources\Collection;
use MyParcelCom\ApiSdk\Resources\File;
use MyParcelCom\ApiSdk\Resources\Interfaces\CarrierInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\CollectionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\FileInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ManifestInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceFactoryInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceProxyInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceOptionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceRateInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentSurchargeInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\ApiSdk\Resources\ResourceFactory;
use MyParcelCom\ApiSdk\Resources\Service;
use MyParcelCom\ApiSdk\Resources\ServiceRate;
use MyParcelCom\ApiSdk\Resources\Shipment;
use MyParcelCom\ApiSdk\Shipments\ServiceMatcher;
use MyParcelCom\ApiSdk\Utils\UrlBuilder;
use MyParcelCom\ApiSdk\Validators\CollectionValidator;
use MyParcelCom\ApiSdk\Validators\ManifestValidator;
use MyParcelCom\ApiSdk\Validators\ShipmentSurchargeValidator;
use MyParcelCom\ApiSdk\Validators\ShipmentValidator;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

class MyParcelComApi implements MyParcelComApiInterface
{
    protected string $apiUri;
    protected CacheInterface $cache;
    protected ResourceFactoryInterface $resourceFactory;
    protected AuthenticatorInterface $authenticator;
    private ClientInterface $client;
    private bool $authRetry = false;

    private static ?MyParcelComApi $singleton = null;

    /**
     * Create a singleton instance of this class, which will be available in subsequent calls to `getSingleton()`.
     */
    public static function createSingleton(
        AuthenticatorInterface $authenticator,
        string $apiUri = 'https://api.sandbox.myparcel.com',
        ClientInterface $httpClient = null,
        CacheInterface $cache = null,
        ResourceFactoryInterface $resourceFactory = null,
    ): self {
        return self::$singleton = (new self($apiUri, $httpClient, $cache, $resourceFactory))
            ->authenticate($authenticator);
    }

    /**
     * Get the singleton instance created.
     */
    public static function getSingleton(): ?self
    {
        return self::$singleton;
    }

    /**
     * Create an instance for the api with given uri. If no cache is given, the filesystem is used for caching.
     * If no resource factory is given, the default factory is used.
     */
    public function __construct(
        string $apiUri = 'https://api.sandbox.myparcel.com',
        ClientInterface $httpClient = null,
        CacheInterface $cache = null,
        ResourceFactoryInterface $resourceFactory = null,
    ) {
        if ($httpClient === null) {
            $httpClient = HttpClientDiscovery::find();
        }

        $this
            ->setHttpClient($httpClient)
            ->setApiUri($apiUri);

        // Either use the given cache or instantiate a new one that uses the filesystem temp directory as a cache.
        if (!$cache) {
            $psr6Cache = new FilesystemAdapter('myparcelcom');
            $cache = new Psr16Cache($psr6Cache);
        }
        $this->setCache($cache);

        // Either use the given resource factory or instantiate a new one.
        $this->setResourceFactory($resourceFactory ?: new ResourceFactory());
    }

    public function authenticate(AuthenticatorInterface $authenticator): self
    {
        $this->authenticator = $authenticator;

        return $this;
    }

    /**
     * @deprecated
     */
    public function getRegions(array $filters = [], int $ttl = self::TTL_10MIN): ResourceCollectionInterface
    {
        $url = (new UrlBuilder($this->apiUri . self::PATH_REGIONS));

        foreach ($filters as $key => $value) {
            $url->addQuery(['filter[' . $key . ']' => $value]);
        }

        $regions = $this->getRequestCollection($url->getUrl(), $ttl);

        if ($regions->count() > 0 || !isset($filters['region_code'])) {
            return $regions;
        }

        // Fallback to the country if the specific region is not in the API.
        $url->addQuery(['filter[region_code]' => null]);

        return $this->getRequestCollection($url->getUrl(), $ttl);
    }

    public function getCarriers(int $ttl = self::TTL_10MIN): ResourceCollectionInterface
    {
        return $this->getRequestCollection($this->apiUri . self::PATH_CARRIERS, $ttl);
    }

    public function getPickUpDropOffLocations(
        string $countryCode,
        string $postalCode,
        ?string $streetName = null,
        ?string $streetNumber = null,
        CarrierInterface $specificCarrier = null,
        bool $onlyActiveContracts = true,
        int $ttl = self::TTL_10MIN,
    ): ResourceCollectionInterface|array {
        $carriers = $this->determineCarriersForPudoLocations($onlyActiveContracts, $specificCarrier);

        $uri = new UrlBuilder(
            $this->apiUri
            . str_replace(
                [
                    '{country_code}',
                    '{postal_code}',
                ],
                [
                    $countryCode,
                    $postalCode,
                ],
                self::PATH_PUDO_LOCATIONS,
            ),
        );

        if ($streetName) {
            $uri->addQuery(['street' => $streetName]);
        }
        if ($streetNumber) {
            $uri->addQuery(['street_number' => $streetNumber]);
        }

        $pudoLocations = [];

        foreach ($carriers as $carrier) {
            $carrierUri = str_replace('{carrier_id}', $carrier->getId(), $uri->getUrl());

            try {
                $resources = $this->getResourcesArray($carrierUri, $ttl);
            } catch (RequestException $exception) {
                // When we are trying to fetch pudo locations for a specific
                // carrier, we want to be able to distinct between 'no results'
                // or 'something went wrong'. However, when we're not looking
                // for carrier specific pudo locations, we just want to show
                // pudo locations for the failing carrier as not available (null).
                if ($specificCarrier) {
                    throw $exception;
                }

                $resources = [];
            }

            if ($specificCarrier) {
                return new ArrayCollection($resources);
            }

            // When something fails while retrieving the locations
            // for a carrier, the locations of the other carriers should
            // still be returned. The failing carrier returns null.
            $pudoLocations[$carrier->getId()] = !empty($resources) ? new ArrayCollection($resources) : null;
        }

        return $pudoLocations;
    }

    public function getShops(int $ttl = self::TTL_10MIN): ResourceCollectionInterface
    {
        return $this->getRequestCollection($this->apiUri . self::PATH_SHOPS, $ttl);
    }

    public function getDefaultShop(int $ttl = self::TTL_10MIN): ShopInterface
    {
        /** @var ShopInterface[] $shops */
        $shops = $this->getResourcesArray(self::PATH_SHOPS, $ttl);

        // For now the oldest shop will be the default shop.
        usort($shops, function (ShopInterface $shopA, ShopInterface $shopB) {
            return $shopA->getCreatedAt()->getTimestamp() - $shopB->getCreatedAt()->getTimestamp();
        });

        return reset($shops);
    }

    public function getServices(
        ShipmentInterface $shipment = null,
        array $filters = ['has_active_contract' => 'true'],
        int $ttl = self::TTL_10MIN,
    ): ResourceCollectionInterface {
        $url = new UrlBuilder($this->apiUri . self::PATH_SERVICES);
        $url->addQuery($this->arrayToFilters($filters));

        if ($shipment === null) {
            return $this->getRequestCollection($url->getUrl(), $ttl);
        }

        if ($shipment->getSenderAddress() === null) {
            $shipment->setSenderAddress($this->getDefaultShop()->getReturnAddress());
        }
        if ($shipment->getRecipientAddress() === null) {
            throw new InvalidResourceException('Missing `recipient_address` on `shipments` resource');
        }
        if ($shipment->getSenderAddress() === null) {
            throw new InvalidResourceException('Missing `sender_address` on `shipments` resource');
        }

        $url->addQuery($this->arrayToFilters([
            'address_from' => array_filter([
                'country_code' => $shipment->getSenderAddress()->getCountryCode(),
                'state_code'   => $shipment->getSenderAddress()->getStateCode(),
                'postal_code'  => $shipment->getSenderAddress()->getPostalCode(),
            ]),
            'address_to'   => array_filter([
                'country_code' => $shipment->getRecipientAddress()->getCountryCode(),
                'state_code'   => $shipment->getRecipientAddress()->getStateCode(),
                'postal_code'  => $shipment->getRecipientAddress()->getPostalCode(),
            ]),
        ]));

        $services = $this->getResourcesArray($url->getUrl(), $ttl);

        $matcher = new ServiceMatcher();
        $services = array_values(
            array_filter(
                $services,
                fn (ServiceInterface $service) => $matcher->matchesDeliveryMethod($shipment, $service),
            ),
        );

        return new ArrayCollection($services);
    }

    public function getServicesForCarrier(
        CarrierInterface $carrier,
        int $ttl = self::TTL_10MIN,
    ): ResourceCollectionInterface {
        $url = new UrlBuilder($this->apiUri . self::PATH_SERVICES);
        $url->addQuery($this->arrayToFilters([
            'has_active_contract' => 'true',
            'carrier'             => $carrier->getId(),
        ]));

        return $this->getRequestCollection($url->getUrl(), $ttl);
    }

    public function getServiceRates(
        array $filters = ['has_active_contract' => 'true'],
        int $ttl = self::TTL_10MIN,
    ): ResourceCollectionInterface {
        $url = new UrlBuilder($this->apiUri . self::PATH_SERVICE_RATES);
        $url->addQuery($this->arrayToFilters($filters));

        return $this->getRequestCollection($url->getUrl(), $ttl);
    }

    public function getServiceRatesForShipment(
        ShipmentInterface $shipment,
        int $ttl = self::TTL_10MIN,
    ): ResourceCollectionInterface {
        $services = $this->getServices($shipment, ['has_active_contract' => 'true'], $ttl);
        $serviceIds = [];
        foreach ($services as $service) {
            $serviceIds[] = $service->getId();
        }

        if (empty($serviceIds)) {
            return new ArrayCollection([]);
        }

        $url = new UrlBuilder($this->apiUri . self::PATH_SERVICE_RATES);
        $url->addQuery($this->arrayToFilters([
            'has_active_contract' => 'true',
            'weight'              => $shipment->getPhysicalProperties()->getWeight(),
            'volume'              => $shipment->calculateVolume(DimensionUnitEnum::DM3),
            'service'             => implode(',', $serviceIds),
        ]));
        if ($shipment->getShop()) {
            $url->addQuery($this->arrayToFilters([
                'organization' => $shipment->getShop()->getOrganization()->getId(),
            ]));
        }
        // Include the services to avoid extra http requests when the result is looped with: $serviceRate->getService().
        $url->addQuery([
            'include' => implode(',', [
                ServiceRate::RELATIONSHIP_CONTRACT,
                ServiceRate::RELATIONSHIP_SERVICE,
            ]),
        ]);

        /** @var ServiceRate[] $serviceRates */
        $serviceRates = $this->getRequestCollection($url->getUrl(), $ttl);

        $availableServiceRates = [];
        foreach ($serviceRates as $serviceRate) {
            if ($serviceRate->isDynamic()) {
                // Resolve the price and currency for service-rates with a dynamic price.
                try {
                    $serviceRates = $this->resolveDynamicServiceRates($shipment, $serviceRate);

                    if (!empty($serviceRates)) {
                        // Hydrate the service and contract resources to prevent additional API calls to retrieve these.
                        $serviceRates[0]->setService($serviceRate->getService());
                        $serviceRates[0]->setContract($serviceRate->getContract());
                        $availableServiceRates[] = $serviceRates[0];
                    }
                } catch (RequestException $exception) {
                    // If communicating with the carrier does not result in a service rate, this service is unavailable.
                }
            } elseif ($serviceRate->getBracketPrice()) {
                // Resolve the price and currency for service-rates with a bracket price (calculated by the API).
                $serviceRate->setPrice($serviceRate->getBracketPrice());
                $serviceRate->setCurrency($serviceRate->getBracketCurrency());
                $availableServiceRates[] = $serviceRate;
            } else {
                $availableServiceRates[] = $serviceRate;
            }
        }

        $shipmentOptionIds = array_map(function (ServiceOptionInterface $serviceOption) {
            return $serviceOption->getId();
        }, $shipment->getServiceOptions());

        $matchingServiceRates = [];
        foreach ($availableServiceRates as $serviceRate) {
            $serviceRateOptionIds = array_map(function (ServiceOptionInterface $serviceOption) {
                return $serviceOption->getId();
            }, $serviceRate->getServiceOptions());

            if (empty(array_diff($shipmentOptionIds, $serviceRateOptionIds))) {
                $matchingServiceRates[] = $serviceRate;
            }
        }

        return new ArrayCollection($matchingServiceRates);
    }

    public function resolveDynamicServiceRates(
        ShipmentInterface|array $shipmentData,
        ?ServiceRateInterface $dynamicServiceRate = null,
    ): array {
        $data = ($shipmentData instanceof ShipmentInterface) ? $shipmentData->jsonSerialize() : $shipmentData;

        if (!isset($data['relationships'])) {
            $data['relationships'] = [];
        }
        if (!isset($data['relationships']['shop'])) {
            $data['relationships']['shop'] = [
                'data' => [
                    'type' => ResourceInterface::TYPE_SHOP,
                    'id'   => $this->getDefaultShop()->getId(),
                ],
            ];
        }

        if ($dynamicServiceRate) {
            $data['relationships']['service'] = [
                'data' => [
                    'type' => ResourceInterface::TYPE_SERVICE,
                    'id'   => $dynamicServiceRate->getService()->getId(),
                ],
            ];
            $data['relationships']['contract'] = [
                'data' => [
                    'type' => ResourceInterface::TYPE_CONTRACT,
                    'id'   => $dynamicServiceRate->getContract()->getId(),
                ],
            ];
        }

        $response = $this->doRequest('/get-dynamic-service-rates', 'post', ['data' => $data], [
            AuthenticatorInterface::HEADER_ACCEPT => AuthenticatorInterface::MIME_TYPE_JSON,
        ]);
        $json = json_decode((string) $response->getBody(), true);
        $included = $json['included'] ?? null;

        return $this->jsonToResources($json['data'], $included);
    }

    public function getShipments(ShopInterface $shop = null, int $ttl = self::TTL_NO_CACHE): ResourceCollectionInterface
    {
        $url = new UrlBuilder($this->apiUri . self::PATH_SHIPMENTS);

        $url->addQuery([
            'include' => implode(',', [
                Shipment::RELATIONSHIP_SHOP,
                Shipment::RELATIONSHIP_STATUS,
                Shipment::RELATIONSHIP_CONTRACT,
                Shipment::RELATIONSHIP_SERVICE,
                Shipment::RELATIONSHIP_SERVICE_OPTIONS,
                Shipment::RELATIONSHIP_FILES,
                Shipment::RELATIONSHIP_SHIPMENT_SURCHARGES,
            ]),
        ]);

        if (isset($shop)) {
            $url->addQuery(['filter[shop]' => $shop->getId()]);
        }

        return $this->getRequestCollection($url->getUrl(), $ttl);
    }

    public function getShipment(string $id, int $ttl = self::TTL_NO_CACHE): ShipmentInterface
    {
        $includes = [
            Shipment::RELATIONSHIP_SHOP,
            Shipment::RELATIONSHIP_STATUS,
            Shipment::RELATIONSHIP_CONTRACT,
            Shipment::RELATIONSHIP_SERVICE,
            Shipment::RELATIONSHIP_SERVICE_OPTIONS,
            Shipment::RELATIONSHIP_FILES,
            Shipment::RELATIONSHIP_SHIPMENT_SURCHARGES,
        ];

        return $this->getResourceById(ResourceInterface::TYPE_SHIPMENT, $id, $ttl, $includes);
    }

    public function saveShipment(ShipmentInterface $shipment): ShipmentInterface
    {
        if ($shipment->getId()) {
            return $this->updateShipment($shipment);
        } else {
            return $this->createShipment($shipment);
        }
    }

    protected function populateShipmentWithDefaultsFromShop(ShipmentInterface $shipment): void
    {
        // If no shop is set, use the default shop.
        if ($shipment->getShop() === null) {
            $shipment->setShop($this->getDefaultShop());
        }

        // If no sender address is set, use the sender address of the shop (or the return address of the shipment).
        $shop = $shipment->getShop();
        if ($shipment->getSenderAddress() === null) {
            $shipment->setSenderAddress(
                $shop->getSenderAddress()
                    ?: $shipment->getReturnAddress()
                    ?: $shop->getReturnAddress(),
            );
        }
        // If no return address is set, use the return address of the shop (or the sender address of the shipment).
        if ($shipment->getReturnAddress() === null) {
            $shipment->setReturnAddress(
                $shop->getReturnAddress()
                    ?: $shipment->getSenderAddress(),
            );
        }
    }

    public function validateShipment(ShipmentInterface $shipment): void
    {
        $validator = new ShipmentValidator($shipment);

        if (!$validator->isValid()) {
            $exception = new InvalidResourceException(
                'This shipment contains invalid data. ' . implode('. ', $validator->getErrors()) . '.',
            );
            $exception->setErrors($validator->getErrors());

            throw $exception;
        }
    }

    public function createShipment(ShipmentInterface $shipment, ?string $idempotencyKey = null): ShipmentInterface
    {
        $this->populateShipmentWithDefaultsFromShop($shipment);
        $this->validateShipment($shipment);

        $headers = [];

        if ($idempotencyKey) {
            $headers[self::HEADER_IDEMPOTENCY_KEY] = $idempotencyKey;
        }

        return $this->postResource($shipment, $shipment->getMeta(), $headers);
    }

    public function updateShipment(ShipmentInterface $shipment): ShipmentInterface
    {
        if (!$shipment->getId()) {
            throw new InvalidResourceException(
                'Could not update shipment. This shipment does not have an id, use createShipment() to save it.',
            );
        }

        $this->validateShipment($shipment);

        return $this->patchResource($shipment, $shipment->getMeta());
    }

    /**
     * This function is similar to createShipment() but will immediately communicate the shipment to the carrier.
     * The carrier response is processed before your request is completed, so files and base64 data will be available.
     *
     * This removes the need to `poll` for files, but has some side effects (exceptions instead of registration-failed).
     * @see https://docs.myparcel.com/api/create-a-shipment.html#registering-your-shipment-with-the-carrier
     */
    public function createAndRegisterShipment(
        ShipmentInterface $shipment,
        ?string $idempotencyKey = null,
    ): ShipmentInterface {
        $this->populateShipmentWithDefaultsFromShop($shipment);
        $this->validateShipment($shipment);

        $headers = [];

        if ($idempotencyKey) {
            $headers[self::HEADER_IDEMPOTENCY_KEY] = $idempotencyKey;
        }

        $response = $this->doRequest(
            '/registered-shipments?' . http_build_query(['include' => Shipment::RELATIONSHIP_FILES]),
            'post',
            [
                'data' => $shipment,
                'meta' => array_filter($shipment->getMeta()),
            ],
            $this->authenticator->getAuthorizationHeader() + [
                AuthenticatorInterface::HEADER_ACCEPT => AuthenticatorInterface::MIME_TYPE_JSONAPI,
            ] + $headers,
        );

        $json = json_decode((string) $response->getBody(), true);

        /** @var Shipment $registeredShipment */
        $registeredShipment = $this->resourceFactory->create('shipments', $json['data']);
        $included = $json['included'] ?? [];
        $metaFiles = $json['meta']['files'] ?? [];

        if (empty($included)) {
            return $registeredShipment;
        }

        $includedResources = $this->jsonToResources($included);
        $registeredShipment->processIncludedResources($includedResources);

        // After the included file models have been populated, we hydrate them with the base64 data from the meta.
        foreach ($registeredShipment->getFiles() as $file) {
            $file->setBase64DataFromResponseMeta($metaFiles);
        }

        return $registeredShipment;
    }

    /**
     * This function is similar to createAndRegisterShipment() and immediately communicates the shipment to the carrier.
     * The carrier response is processed before your request is completed, so files and base64 data will be available.
     * Note that files will not be set on the `master` shipment, but each individual `collo` shipment will have a file.
     */
    public function createAndRegisterMultiColliShipment(
        ShipmentInterface $shipment,
        ?string $idempotencyKey = null,
    ): ShipmentInterface {
        $this->populateShipmentWithDefaultsFromShop($shipment);
        $this->validateShipment($shipment);

        if (count($shipment->getColli()) < 1) {
            throw new InvalidResourceException(
                'Could not create multi-colli shipment without any colli. Please add one or more collo shipments to this master shipment.',
            );
        }

        $headers = [];

        if ($idempotencyKey) {
            $headers[self::HEADER_IDEMPOTENCY_KEY] = $idempotencyKey;
        }

        $response = $this->doRequest(
            '/multi-colli-shipments?' . http_build_query([
                'include' => implode(',', [
                    Shipment::RELATIONSHIP_COLLI,
                    Shipment::RELATIONSHIP_COLLI . '.' . Shipment::RELATIONSHIP_FILES,
                ]),
            ]),
            'post',
            [
                'data' => $shipment,
                'meta' => array_merge(
                    [
                        'colli' => array_map(
                            fn (ShipmentInterface $collo) => $collo->jsonSerialize()['attributes'] ?? null,
                            $shipment->getColli(),
                        ),
                    ],
                    array_filter($shipment->getMeta()),
                ),
            ],
            $this->authenticator->getAuthorizationHeader() + [
                AuthenticatorInterface::HEADER_ACCEPT => AuthenticatorInterface::MIME_TYPE_JSONAPI,
            ] + $headers,
        );

        $json = json_decode((string) $response->getBody(), true);

        /** @var Shipment $registeredShipment */
        $registeredShipment = $this->resourceFactory->create('shipments', $json['data']);
        $included = $json['included'] ?? [];
        $relationshipColli = $json['data']['relationships']['colli']['data'] ?? [];

        if (empty($included)) {
            return $registeredShipment;
        }

        $includedResources = $this->jsonToResources($included);
        $registeredShipment->processIncludedResources($includedResources);

        // After the included colli models have been populated, we hydrate them with the base64 data from the meta.
        foreach ($registeredShipment->getColli() as $collo) {
            foreach ($collo->getFiles() as $file) {
                if ($file instanceof ResourceProxyInterface) {
                    $file->setResourceFromIncludes($includedResources);
                }

                foreach ($relationshipColli as $relationshipCollo) {
                    if ($relationshipCollo['meta']['collo_number'] === $collo->getColloNumber()) {
                        $metaFiles = $relationshipCollo['meta']['files'] ?? [];

                        $file->setBase64DataFromResponseMeta($metaFiles);
                    }
                }
            }
        }

        return $registeredShipment;
    }

    /**
     * Get all manifests from the API.
     *
     * @throws MyParcelComException
     */
    public function getManifests(int $ttl = self::TTL_10MIN): ResourceCollectionInterface
    {
        return $this->getRequestCollection($this->apiUri . self::PATH_MANIFESTS, $ttl);
    }

    /**
     * Get a specific manifest from the API.
     *
     * @throws MyParcelComException
     */
    public function getManifest(string $id, int $ttl = self::TTL_NO_CACHE): ManifestInterface
    {
        return $this->getResourceById(ResourceInterface::TYPE_MANIFEST, $id, $ttl);
    }

    /**
     * @throws RequestException
     */
    public function createManifest(ManifestInterface $manifest): ManifestInterface
    {
        // If no address is set and the owner is a Shop, use the address of the owner.
        if (
            $manifest->getAddress() === null
            && $manifest->getOwner()?->getType() === ResourceInterface::TYPE_SHOP
        ) {
            $shop = $manifest->getOwner();

            $manifest->setAddress($shop->getSenderAddress() ?? $shop->getReturnAddress());
        }

        $validator = new ManifestValidator($manifest);
        if (!$validator->isValid()) {
            $message = 'This manifest contains invalid data. ' . implode('. ', $validator->getErrors()) . '.';
            $exception = new InvalidResourceException($message);
            $exception->setErrors($validator->getErrors());

            throw $exception;
        }

        return $this->postResource($manifest);
    }

    /**
     * @throws RequestException
     */
    public function getManifestFile(string $manifestId, string $fileId): FileInterface
    {
        $url = str_replace(
            ['{manifest_id}', '{file_id}'],
            [$manifestId, $fileId],
            self::PATH_MANIFESTS_ID_FILES_ID,
        );

        $headers = $this->authenticator->getAuthorizationHeader() + [
                AuthenticatorInterface::HEADER_ACCEPT => FileInterface::MIME_TYPE_PDF,
            ];

        $response = $this->doRequest($url, headers: $headers);

        return (new File())
            ->addFormat(FileInterface::MIME_TYPE_PDF, FileInterface::EXTENSION_PDF)
            ->setStream($response->getBody(), FileInterface::MIME_TYPE_PDF);
    }

    public function getCollections(array $filters = [], int $ttl = self::TTL_10MIN): ResourceCollectionInterface
    {
        $url = (new UrlBuilder($this->apiUri . self::PATH_COLLECTIONS));
        $url->addQuery($this->arrayToFilters($filters));

        return $this->getRequestCollection($url->getUrl(), $ttl);
    }

    public function getCollection(string $collectionId, int $ttl = self::TTL_NO_CACHE): CollectionInterface
    {
        return $this->getResourceById(ResourceInterface::TYPE_COLLECTION, $collectionId, $ttl);
    }

    public function createCollection(CollectionInterface $collection): CollectionInterface
    {
        if (!$collection->getShop()) {
            $collection->setShop($this->getDefaultShop());
        }

        if ($collection->getAddress() === null) {
            $collection->setAddress(
                $collection->getShop()->getSenderAddress() ?? $collection->getShop()->getReturnAddress(),
            );
        }

        $validator = new CollectionValidator($collection);
        if (!$validator->isValid()) {
            $message = 'This collection contains invalid data. ' . implode('. ', $validator->getErrors()) . '.';
            $exception = new InvalidResourceException($message);
            $exception->setErrors($validator->getErrors());

            throw $exception;
        }

        return $this->postResource($collection);
    }

    /**
     * Updates the collection with allowed attributes and relationships.
     * @see https://api-specification.myparcel.com/#tag/Collections/paths/~1collections~1%7Bcollection_id%7D/patch
     */
    public function updateCollection(CollectionInterface $collection): CollectionInterface
    {
        if (!$collection->getId()) {
            throw new InvalidResourceException(
                'Could not update collection. This collection does not have an id, use createCollection() to save it.',
            );
        }

        $collectionToUpdate = (new Collection())
            ->setId($collection->getId())
            ->setDescription($collection->getDescription())
            ->setRegister($collection->getRegister());

        // Only the `collection-collected` status can be manually assigned by users.
        if ($collection->getStatus()?->getCode() === 'collection-collected') {
            $collectionToUpdate->setStatus($collection->getStatus());
        }

        return $this->patchResource($collectionToUpdate);
    }

    public function registerCollection(CollectionInterface|string $collectionId): CollectionInterface
    {
        if ($collectionId instanceof CollectionInterface) {
            $collectionId = $collectionId->getId();
        }

        if (!$collectionId) {
            throw new InvalidResourceException(
                'Could not register collection. This collection does not have an id, use createCollection() to save it.',
            );
        }

        $collectionToRegister = (new Collection())
            ->setId($collectionId)
            ->setRegister(true);

        return $this->updateCollection($collectionToRegister);
    }

    /**
     * @throws RequestException
     */
    public function deleteCollection(CollectionInterface $collection): bool
    {
        if (!$collection->getId()) {
            throw new InvalidResourceException(
                'Could not delete collection. This collection does not have an id.',
            );
        }

        $this->deleteResource($collection);

        return true;
    }

    /**
     * @throws RequestException
     */
    public function addShipmentsToCollection(
        CollectionInterface $collection,
        array $shipments,
    ): CollectionInterface {
        if (!$collection->getId()) {
            throw new InvalidResourceException(
                'Could not add shipments to collection. This collection does not have an id.',
            );
        }

        $this->doRequest('/add-shipments-to-collection', 'post', [
            'data' => [
                'collection_id' => $collection->getId(),
                'shipment_ids'  => array_map(function (ShipmentInterface|string $shipment) {
                    return $shipment instanceof ShipmentInterface ? $shipment->getId() : $shipment;
                }, $shipments),
            ],
        ]);

        return $this->getCollection($collection->getId());
    }

    public function generateManifestForCollection(CollectionInterface $collection): ManifestInterface
    {
        if (!$collection->getId()) {
            throw new InvalidResourceException(
                'Could not generate manifest for collection. This collection does not have an id.',
            );
        }

        $response = $this->doRequest('/create-manifest-for-collection', 'post', [
            'data' => [
                'collection_id' => $collection->getId(),
            ],
        ]);

        $json = json_decode((string) $response->getBody(), true);

        return $this->resourceFactory->create('manifests', $json['data']);
    }

    /**
     * @see https://api-specification.develop.myparcel.com/#tag/ShipmentSurcharges/paths/~1shipment-surcharges/get
     */
    public function getShipmentSurcharges(int $ttl = self::TTL_NO_CACHE): ResourceCollectionInterface
    {
        return $this->getRequestCollection($this->apiUri . self::PATH_SHIPMENT_SURCHARGES, $ttl);
    }

    /**
     * @see https://api-specification.myparcel.com/#tag/ShipmentSurcharges/paths/~1shipment-surcharges~1%7Bshipment_surcharge_id%7D/get
     */
    public function getShipmentSurcharge(
        string $shipmentSurchargeId,
        int $ttl = self::TTL_NO_CACHE,
    ): ShipmentSurchargeInterface {
        return $this->getResourceById(ResourceInterface::TYPE_SHIPMENT_SURCHARGE, $shipmentSurchargeId, $ttl);
    }

    /**
     * @see https://api-specification.myparcel.com/#tag/ShipmentSurcharges/paths/~1shipment-surcharges/post
     */
    public function createShipmentSurcharge(ShipmentSurchargeInterface $shipmentSurcharge): ShipmentSurchargeInterface
    {
        $validator = new ShipmentSurchargeValidator($shipmentSurcharge);

        if (!$validator->isValid()) {
            $message = 'This shipment surcharge contains invalid data. ' . implode('. ', $validator->getErrors()) . '.';
            $exception = new InvalidResourceException($message);
            $exception->setErrors($validator->getErrors());

            throw $exception;
        }

        return $this->postResource($shipmentSurcharge);
    }

    /**
     * @see https://api-specification.myparcel.com/#tag/ShipmentSurcharges/paths/~1shipment-surcharges~1%7Bshipment_surcharge_id%7D/patch
     */
    public function updateShipmentSurcharge(ShipmentSurchargeInterface $shipmentSurcharge): ShipmentSurchargeInterface
    {
        if (!$shipmentSurcharge->getId()) {
            throw new InvalidResourceException(
                'Could not update shipment surcharge. This shipment surcharge does not have an id, use createShipmentSurcharge() to save it.',
            );
        }

        // The shipment relationship cannot be patched, so we create a clone without it to get the valid JSON body.
        $shipmentSurchargeToUpdate = clone $shipmentSurcharge;
        $shipmentSurchargeToUpdate->setShipment(null);

        return $this->patchResource($shipmentSurchargeToUpdate);
    }

    /**
     * @see https://api-specification.myparcel.com/#tag/ShipmentSurcharges/paths/~1shipment-surcharges~1%7Bshipment_surcharge_id%7D/delete
     */
    public function deleteShipmentSurcharge(ShipmentSurchargeInterface $shipmentSurcharge): bool
    {
        if (!$shipmentSurcharge->getId()) {
            throw new InvalidResourceException(
                'Could not delete shipment surcharge. This shipment surcharge does not have an id.',
            );
        }

        $this->deleteResource($shipmentSurcharge);

        return true;
    }

    /**
     * Set the URI of the MyParcel.com API.
     */
    public function setApiUri(string $apiUri): self
    {
        // Remove trailing whitespace and a trailing slash.
        $this->apiUri = rtrim($apiUri, " \t\n\r\0\x0B/");

        return $this;
    }

    /**
     * Set the factory to use when creating resources.
     */
    public function setResourceFactory(ResourceFactoryInterface $resourceFactory): self
    {
        // Let this fetch the resources if the factory allows proxying of resources.
        $this->resourceFactory = $resourceFactory->setMyParcelComApi($this);

        return $this;
    }

    /**
     * Set the cache which will be used to store resources.
     */
    public function setCache(CacheInterface $cache): self
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Clear the cached resources and the authorization cache.
     */
    public function clearCache(): self
    {
        $this->cache->clear();
        $this->authenticator->clearCache();

        return $this;
    }

    /**
     * Set the HTTP client to use to connect to the api. Given client must implement the PSR-18 client interface.
     */
    public function setHttpClient(ClientInterface $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get the HTTP client.
     */
    protected function getHttpClient(): ClientInterface
    {
        return $this->client;
    }

    /**
     * Get a promise that will return an array with resources requested from given uri.
     * A time-to-live can be specified for how long this request should be cached (defaults to 10 minutes).
     *
     * @throws RequestException
     */
    protected function getResourcesArray(string $uri, int $ttl = self::TTL_10MIN): array
    {
        $response = $this->doRequest($uri, 'get', [], [], $ttl);
        $json = json_decode((string) $response->getBody(), true);
        $included = $json['included'] ?? null;

        $resources = $this->jsonToResources($json['data'], $included);

        // If there is no next link, we don't have to retrieve any more data
        if (!isset($json['links']['next'])) {
            return $resources;
        }

        return array_merge($resources, $this->getResourcesArray($json['links']['next']));
    }

    /**
     * Get a collection of resources requested from the given uri.
     * A time-to-live can be specified for how long this request should be cached (defaults to 10 minutes).
     */
    protected function getRequestCollection(string $uri, int $ttl = self::TTL_10MIN): ResourceCollectionInterface
    {
        return new RequestCollection(function ($pageNumber, $pageSize) use ($uri, $ttl) {
            $url = (new UrlBuilder($uri))->addQuery([
                'page[number]' => $pageNumber,
                'page[size]'   => $pageSize,
            ])->getUrl();

            return $this->doRequest($url, 'get', [], [], $ttl);
        }, function ($data, $included = null) {
            return $this->jsonToResources($data, $included);
        });
    }

    public function doRequest(
        string $uri,
        string $method = 'get',
        array $body = [],
        array $headers = [],
        $ttl = self::TTL_NO_CACHE,
    ): ResponseInterface {
        if (!str_starts_with($uri, $this->apiUri)) {
            $uri = $this->apiUri . $uri;
        }
        $headers += $this->authenticator->getAuthorizationHeader() + [
                AuthenticatorInterface::HEADER_ACCEPT => AuthenticatorInterface::MIME_TYPE_JSONAPI,
            ];

        // Attempt to fetch a response from cache
        $cacheKey = sha1(join($headers) . $uri);
        if (($response = $this->cache->get($cacheKey)) && strtolower($method) === 'get') {
            return Message::parseResponse($response);
        }

        try {
            $request = new Request($method, $uri, $headers, json_encode($body));
            $response = $this->client->sendRequest($request);

            // Store the response in cache
            if (strtolower($method) === 'get') {
                $this->cache->set($cacheKey, Message::toString($response), $ttl);
            }

            if ($response->getStatusCode() >= 300) {
                throw new RequestException($request, $response);
            }

            return $response;
        } catch (RequestException $requestException) {
            return $this->handleRequestException($requestException);
        }
    }

    /**
     * Convert the data from a json request to an array of resources.
     */
    protected function jsonToResources(array $json, ?array $included = null): array
    {
        $resources = [];

        if (isset($json['type'])) {
            $json = [$json];
        }

        foreach ($json as $resourceData) {
            $resource = $this->resourceFactory->create($resourceData['type'], $resourceData);

            if (isset($included)) {
                $includedResources = $this->jsonToResources($included);
                $resource->processIncludedResources($includedResources);
            }

            $resources[] = $resource;
        }

        return $resources;
    }

    /**
     * @throws RequestExceptionInterface
     */
    protected function handleRequestException(RequestExceptionInterface $exception): ResponseInterface
    {
        $response = $exception->getResponse();
        if ($response->getStatusCode() !== 401 || $this->authRetry) {
            // TODO actually do something
            // echo (string)$exception->getRequest()->getUri();
            // echo (string)$exception->getResponse()->getBody();

            throw $exception;
        }

        $this->authRetry = true;
        $authHeaders = $this->authenticator->getAuthorizationHeader(true);

        $request = $exception->getRequest();

        $body = (string) $request->getBody();
        $jsonBody = $body
            ? json_decode($body, true)
            : [];

        return $this->doRequest(
            (string) $request->getUri(),
            $request->getMethod(),
            $jsonBody,
            $authHeaders + $request->getHeaders(),
        );
    }

    public function getResourceById(
        string $resourceType,
        string $id,
        int $ttl = self::TTL_NO_CACHE,
        array $includes = [],
    ): ResourceInterface {
        $include = empty($includes) ? '' : '?include=' . implode(',', $includes);

        $resources = $this->getResourcesArray(
            $this->getResourceUri($resourceType, $id) . $include,
            $ttl,
        );

        return reset($resources);
    }

    /**
     * @return ResourceInterface[]
     * @throws RequestException
     */
    public function getResourcesFromUri(string $uri): array
    {
        return $this->getResourcesArray($uri);
    }

    /**
     * Patch given resource and return the resource that was returned by the request.
     *
     * @throws RequestException
     */
    protected function patchResource(
        ResourceInterface $resource,
        array $meta = [],
        array $headers = [],
    ): ResourceInterface {
        return $this->sendResource($resource, 'patch', $meta, $headers);
    }

    /**
     * Post given resource and return the resource that was returned by the request.
     *
     * @throws RequestException
     */
    protected function postResource(
        ResourceInterface $resource,
        array $meta = [],
        array $headers = [],
    ): ResourceInterface {
        return $this->sendResource($resource, 'post', $meta, $headers);
    }

    /**
     * @throws RequestException
     */
    protected function deleteResource(
        ResourceInterface $resource,
        array $headers = [],
    ): ResponseInterface {
        return $this->doRequest(
            $this->getResourceUri($resource->getType(), $resource->getId()),
            'delete',
            [],
            $this->authenticator->getAuthorizationHeader() + $headers,
        );
    }

    /**
     * Send given resource to the API and return the resource that was returned.
     *
     * @throws RequestException
     */
    protected function sendResource(
        ResourceInterface $resource,
        string $method = 'post',
        array $meta = [],
        array $headers = [],
    ): ResourceInterface {
        $response = $this->doRequest(
            $this->getResourceUri($resource->getType(), $resource->getId()),
            $method,
            array_filter([
                'data' => $resource,
                'meta' => array_filter($meta),
            ]),
            $this->authenticator->getAuthorizationHeader() + [
                AuthenticatorInterface::HEADER_ACCEPT => AuthenticatorInterface::MIME_TYPE_JSONAPI,
            ] + $headers,
        );

        $json = json_decode((string) $response->getBody(), true);
        $included = $json['included'] ?? null;
        $resources = $this->jsonToResources($json['data'], $included);

        return reset($resources);
    }

    protected function getResourceUri(string $resourceType, string $id = null): string
    {
        return implode(
            '/',
            array_filter([
                $this->apiUri,
                $resourceType,
                $id,
            ]),
        );
    }

    /**
     * Converts given array to a filter array usable as query params.
     */
    private function arrayToFilters(array $array): array
    {
        $filters = [];

        $this->arrayToFilter($filters, ['filter'], $array);

        return $filters;
    }

    /**
     * Converts given array to a filter string for the query params.
     */
    private function arrayToFilter(array &$filters, array $keys, mixed $value): void
    {
        if (is_array($value)) {
            foreach ($value as $key => $nextValue) {
                $this->arrayToFilter($filters, array_merge($keys, ['[' . $key . ']']), $nextValue);
            }
        } else {
            $filters[implode('', $keys)] = $value;
        }
    }

    /**
     * Determines which carriers to look pudo locations up for.
     * The specificCarrier parameter indicates a specific carrier to look up pudo locations for. Otherwise,
     * all carriers will be used.
     * The onlyActiveContracts parameter indicates whether only carriers for which the user has an active contract
     * for services with delivery method pickup should be used for pudo location retrieval.
     */
    private function determineCarriersForPudoLocations(
        bool $onlyActiveContracts,
        CarrierInterface $specificCarrier = null,
    ): array {
        // If we're looking for a specific carrier but it doesn't
        // matter if it has active contracts, just return it immediately.
        if (!$onlyActiveContracts && $specificCarrier) {
            return [$specificCarrier];
        }

        // Return all carriers if we're not filtering for anything specific.
        if (!$onlyActiveContracts) {
            return $this->getCarriers()->get();
        }

        $parameters = [
            'has_active_contract' => 'true',
            'delivery_method'     => 'pick-up',
        ];

        if ($specificCarrier) {
            $parameters['carrier'] = $specificCarrier->getId();
        }

        $pudoServices = $this->getServices(null, $parameters)->get();

        return array_map(function (Service $service) {
            return $service->getCarrier();
        }, $pudoServices);
    }
}
