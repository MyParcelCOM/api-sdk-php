<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources;

use MyParcelCom\ApiSdk\Resources\Interfaces\CarrierInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;

class Carrier implements CarrierInterface
{
    use JsonSerializable {
        jsonSerialize as private serialize;
    }
    use Resource;

    const ATTRIBUTE_NAME = 'name';
    const ATTRIBUTE_CODE = 'code';
    const ATTRIBUTE_CREDENTIALS_FORMAT = 'credentials_format';
    const ATTRIBUTE_LABEL_MIME_TYPES = 'label_mime_types';
    const ATTRIBUTE_OFFERS_COLLECTIONS = 'offers_collections';
    const ATTRIBUTE_VOIDS_REGISTERED_COLLECTIONS = 'voids_registered_collections';
    const ATTRIBUTE_ALLOWS_ADDING_REGISTERED_SHIPMENTS_TO_COLLECTION = 'allows_adding_registered_shipments_to_collection';

    private ?string $id = null;

    private string $type = ResourceInterface::TYPE_CARRIER;

    private array $attributes = [
        self::ATTRIBUTE_NAME                                             => null,
        self::ATTRIBUTE_CODE                                             => null,
        self::ATTRIBUTE_CREDENTIALS_FORMAT                               => [],
        self::ATTRIBUTE_LABEL_MIME_TYPES                                 => [],
        self::ATTRIBUTE_OFFERS_COLLECTIONS                               => null,
        self::ATTRIBUTE_VOIDS_REGISTERED_COLLECTIONS                     => null,
        self::ATTRIBUTE_ALLOWS_ADDING_REGISTERED_SHIPMENTS_TO_COLLECTION => null,
    ];

    public function setName(string $name): self
    {
        $this->attributes[self::ATTRIBUTE_NAME] = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->attributes[self::ATTRIBUTE_NAME];
    }

    public function setCode(string $code): self
    {
        $this->attributes[self::ATTRIBUTE_CODE] = $code;

        return $this;
    }

    public function getCode(): string
    {
        return $this->attributes[self::ATTRIBUTE_CODE];
    }

    public function setCredentialsFormat(array $format): self
    {
        $this->attributes[self::ATTRIBUTE_CREDENTIALS_FORMAT] = $format;

        return $this;
    }

    public function getCredentialsFormat(): array
    {
        return $this->attributes[self::ATTRIBUTE_CREDENTIALS_FORMAT];
    }

    public function setLabelMimeTypes(array $labelMimeTypes): self
    {
        $this->attributes[self::ATTRIBUTE_LABEL_MIME_TYPES] = $labelMimeTypes;

        return $this;
    }

    public function getLabelMimeTypes(): array
    {
        return $this->attributes[self::ATTRIBUTE_LABEL_MIME_TYPES];
    }

    public function setOffersCollections(bool $offersCollections): self
    {
        $this->attributes[self::ATTRIBUTE_OFFERS_COLLECTIONS] = $offersCollections;

        return $this;
    }

    public function getOffersCollections(): bool
    {
        return $this->attributes[self::ATTRIBUTE_OFFERS_COLLECTIONS];
    }

    public function setVoidsRegisteredCollections(bool $voidsRegisteredCollections): self
    {
        $this->attributes[self::ATTRIBUTE_VOIDS_REGISTERED_COLLECTIONS] = $voidsRegisteredCollections;

        return $this;
    }

    public function getVoidsRegisteredCollections(): bool
    {
        return $this->attributes[self::ATTRIBUTE_VOIDS_REGISTERED_COLLECTIONS];
    }

    public function setAllowsAddingRegisteredShipmentsToCollection(bool $allowsAddingRegisteredShipments,
    ): self {
        $this->attributes[self::ATTRIBUTE_VOIDS_REGISTERED_COLLECTIONS] = $allowsAddingRegisteredShipments;

        return $this;
    }

    public function getAllowsAddingRegisteredShipmentsToCollection(): bool
    {
        return $this->attributes[self::ATTRIBUTE_VOIDS_REGISTERED_COLLECTIONS];
    }

    /**
     * This function puts all object properties in an array and returns it.
     */
    public function jsonSerialize(): array
    {
        // The 'credentials_format' can have camelCased properties, which get
        // changed to snake_case by the jsonSerialize() method. So ro prevent
        // that, we unset it and then reset it after serialization is done.
        $credentialsFormat = $this->attributes[self::ATTRIBUTE_CREDENTIALS_FORMAT];
        unset($this->attributes[self::ATTRIBUTE_CREDENTIALS_FORMAT]);

        $json = $this->serialize();

        if (!empty($credentialsFormat)) {
            $json['attributes'][self::ATTRIBUTE_CREDENTIALS_FORMAT] = $credentialsFormat;
        }

        return $json;
    }
}
