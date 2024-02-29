<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Authentication;

use GuzzleHttp\Psr7\Request;
use Http\Discovery\HttpClientDiscovery;
use MyParcelCom\ApiSdk\Exceptions\AuthenticationException;
use MyParcelCom\ApiSdk\Http\Exceptions\RequestException;
use Psr\Http\Client\ClientInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

class ClientCredentials implements AuthenticatorInterface
{
    const CACHE_TOKEN = 'auth.token';
    const CACHE_AUTHENTICATING = 'auth.lock';
    const PATH_ACCESS_TOKEN = '/access-token';
    const TTL_MARGIN = 60;

    /**
     * Create an authenticator for the client_credentials grant. Requires a client id and a client secret. When not
     * connecting to the MyParcel.com sandbox, the `$authUri` should be set to the correct uri. Optionally a cache can
     * be supplied to store the api-key in.
     */
    public function __construct(
        protected string $clientId,
        protected string $clientSecret,
        protected string $authUri = 'https://auth.sandbox.myparcel.com',
        protected ?CacheInterface $cache = null,
        protected ?ClientInterface $httpClient = null
    ) {
        // If no cache is provided, instantiate a new one that uses the filesystem temp directory as a cache.
        if (!$cache) {
            $psr6Cache = new FilesystemAdapter('myparcelcom');
            $this->cache = new Psr16Cache($psr6Cache);
        }
    }

    public function getAuthorizationHeader(bool $forceRefresh = false): array
    {
        while ($this->isAuthenticating()) {
            // Wait for 200ms
            usleep(200000);
            // Don't force another authentication cycle if we're already authenticating.
            $forceRefresh = false;
        }

        if ($forceRefresh) {
            return $this->authenticate();
        }

        return $this->getCachedHeader() ?: $this->authenticate();
    }

    /**
     * Returns true if another request is already authenticating.
     */
    protected function isAuthenticating(): bool
    {
        return (bool) $this->cache->get($this->cacheKey(self::CACHE_AUTHENTICATING));
    }

    protected function setAuthenticating(bool $authenticating = true): self
    {
        // Don't let the authenticating lock stay active for more than 30s.
        $this->cache->set($this->cacheKey(self::CACHE_AUTHENTICATING), $authenticating, 30);

        return $this;
    }

    /**
     * Authenticate with the server and return the header.
     */
    protected function authenticate(): array
    {
        $this->setAuthenticating();

        $body = json_encode([
            'grant_type'    => self::GRANT_CLIENT_CREDENTIALS,
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'scope'         => self::SCOPES,
        ]);

        $request = new Request(
            'post',
            $this->authUri . self::PATH_ACCESS_TOKEN,
            [
                self::HEADER_CONTENT_TYPE => self::MIME_TYPE_JSON,
            ],
            $body
        );

        try {
            $response = $this->getHttpClient()->sendRequest($request);
            if ($response->getStatusCode() >= 400) {
                throw new RequestException($request, $response);
            }

            $data = json_decode((string) $response->getBody(), true);

            $header = [
                self::HEADER_AUTH => $data['token_type'] . ' ' . $data['access_token'],
            ];

            $this->setAuthenticating(false);
            $this->setCachedHeader($header, $data['expires_in']);

            return $header;
        } catch (RequestException $exception) {
            $this->setAuthenticating(false);
            $this->handleRequestException($exception);
        }
    }

    /**
     * Get the cached header or `null` if no header is cached (or expired).
     */
    protected function getCachedHeader(): ?array
    {
        return $this->cache->get($this->cacheKey(self::CACHE_TOKEN));
    }

    /**
     * Set the cached header with a time to live.
     */
    protected function setCachedHeader(array $header, int $ttl): self
    {
        $this->cache->set($this->cacheKey(self::CACHE_TOKEN), $header, $ttl - self::TTL_MARGIN);

        return $this;
    }

    protected function cacheKey(string $cacheType): string
    {
        return implode('_', [$cacheType, $this->clientId]);
    }

    /**
     * Clear the cached resources.
     */
    public function clearCache(): self
    {
        $this->cache->clear();

        return $this;
    }

    /**
     * Set the http client to use. This can be used when extra options need to be set on the client.
     */
    public function setHttpClient(ClientInterface $client): self
    {
        $this->httpClient = $client;

        return $this;
    }

    /**
     * Get the http client to connect to the auth server.
     */
    protected function getHttpClient(): ClientInterface
    {
        if (!isset($this->httpClient)) {
            $this->httpClient = HttpClientDiscovery::find();
        }

        return $this->httpClient;
    }

    /**
     * Handle the request exception.
     */
    protected function handleRequestException(RequestException $exception): void
    {
        if (empty($exception->getResponse())) {
            throw new AuthenticationException(
                'The authentication server could not be reached, please check if the uri is correct',
                404,
                $exception
            );
        }

        $response = json_decode((string) $exception->getResponse()->getBody(), true);
        $message = 'An unknown error occurred while authenticating with the oauth2 server';

        if (!empty($response['errors'])) {
            $errors = $response['errors'];
            $error = reset($errors);
            $code = $error['code'];

            switch ($code) {
                case '14000': // AUTH_INVALID_CLIENT
                    $message = 'Client id or client secret is invalid';
                    break;
                case '14001': // AUTH_INVALID_SCOPE
                    $message = 'Invalid scope requested for client';
                    break;
                case '14002': // AUTH_INVALID_TOKEN
                    $message = 'Access token is invalid';
                    break;
            }
        }

        throw new AuthenticationException(
            $message,
            401,
            $exception
        );
    }
}
