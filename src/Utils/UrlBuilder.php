<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Utils;

class UrlBuilder
{
    protected ?string $url = null;
    protected ?string $scheme = null;
    protected ?string $host = null;
    protected ?int $port = null;
    protected ?string $user = null;
    protected ?string $password = null;
    protected ?string $path = null;
    protected ?string $fragment = null;
    protected array $query = [];

    public function __construct(?string $url = null)
    {
        if ($url !== null) {
            $this->setUrl($url);
        }
    }

    /**
     * Set the base url.
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;
        $parts = parse_url($url);

        $this->scheme = $parts['scheme'] ?? null;
        $this->host = $parts['host'] ?? null;
        $this->port = $parts['port'] ?? null;
        $this->user = $parts['user'] ?? null;
        $this->password = $parts['pass'] ?? null;
        $this->path = $parts['path'] ?? null;
        $this->fragment = $parts['fragment'] ?? null;

        if (isset($parts['query'])) {
            parse_str($parts['query'], $this->query);
        }

        return $this;
    }

    /**
     * Get the compiled url.
     */
    public function getUrl(): string
    {
        $url = '';
        if ($this->scheme) {
            $url .= $this->scheme . '://';
        }

        if ($this->user && $this->password) {
            $url .= $this->user . ':' . $this->password . '@';
        } elseif ($this->user) {
            $url .= $this->user . '@';
        }

        if ($this->host) {
            $url .= $this->host;
        }
        if ($this->port) {
            $url .= ':' . $this->port;
        }
        if ($this->path) {
            $url .= $this->path;
        }
        if ($this->query) {
            $url .= '?' . urldecode(http_build_query($this->query));
        }
        if ($this->fragment) {
            $url .= '#' . $this->fragment;
        }

        return $url;
    }

    /**
     * Get the GET query params.
     */
    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * Set the GET query params.
     */
    public function setQuery(array $query): self
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Add GET query params.
     */
    public function addQuery(array $query): self
    {
        $this->query = array_merge($this->query, $query);

        return $this;
    }

    /**
     * Get the scheme of the url.
     */
    public function getScheme(): ?string
    {
        return $this->scheme;
    }

    /**
     * Set the scheme of the url.
     */
    public function setScheme(string $scheme): self
    {
        $this->scheme = $scheme;

        return $this;
    }

    /**
     * Get the host of the url.
     */
    public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * Set the host of the url.
     */
    public function setHost(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Get the url port.
     */
    public function getPort(): ?int
    {
        return $this->port;
    }

    /**
     * Set the url port.
     */
    public function setPort(int $port): self
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Get the HTTP auth user.
     */
    public function getUser(): ?string
    {
        return $this->user;
    }

    /**
     * Set the HTTP auth user.
     */
    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the HTTP auth password.
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Set the HTTP auth password.
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the path in the url.
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * Set the url path.
     */
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get the string after `#` in the url.
     */
    public function getFragment(): ?string
    {
        return $this->fragment;
    }

    /**
     * Set the string after `#` in the url.
     */
    public function setFragment(string $fragment): self
    {
        $this->fragment = $fragment;

        return $this;
    }

    /**
     * Get the compiled url string.
     */
    public function __toString(): string
    {
        return $this->getUrl();
    }
}
