<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests;

use MyParcelCom\ApiSdk\Authentication\AuthenticatorInterface;
use MyParcelCom\ApiSdk\MyParcelComApi;
use MyParcelCom\ApiSdk\Tests\Traits\MocksApiCommunication;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Psr\Http\Client\ClientInterface;

class TestCase extends PHPUnitTestCase
{
    use MocksApiCommunication;

    protected AuthenticatorInterface $authenticator;
    protected MyParcelComApi $api;
    protected ClientInterface $client;

    protected function setUp(): void
    {
        $this->authenticator = $this->getAuthenticatorMock();

        $this->client = $this->getClientMock();

        $this->api = (new MyParcelComApi('https://api', $this->client))
            ->authenticate($this->authenticator);
    }
}
