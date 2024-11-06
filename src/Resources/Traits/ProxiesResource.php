<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Traits;

use MyParcelCom\ApiSdk\Exceptions\MyParcelComException;
use MyParcelCom\ApiSdk\MyParcelComApiInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;

trait ProxiesResource
{
    private ?ResourceInterface $resource = null;
    private ?MyParcelComApiInterface $api = null;
    private ?string $uri = null;

    /**
     * Set the api to use when retrieving the resource.
     */
    public function setMyParcelComApi(MyParcelComApiInterface $api): self
    {
        $this->api = $api;

        return $this;
    }

    public function setResourceUri(?string $uri): self
    {
        $this->uri = $uri;

        return $this;
    }

    public function setResource(ResourceInterface $resource): self
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Get the resource that this instance is a proxy for.
     */
    protected function getResource(): ?ResourceInterface
    {
        if (isset($this->resource)) {
            return $this->resource;
        }

        if (!isset($this->api)) {
            throw new MyParcelComException('No API object set on proxy, cannot retrieve resource');
        }

        if (isset($this->uri)) {
            $resources = $this->api->getResourcesFromUri($this->uri);
            $this->resource = reset($resources);
        } else {
            $this->resource = $this->api->getResourceById($this->getType(), $this->getId());
        }

        return $this->resource;
    }
}
