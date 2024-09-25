<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Feature\Proxy;

use MyParcelCom\ApiSdk\Authentication\AuthenticatorInterface;
use MyParcelCom\ApiSdk\MyParcelComApi;
use MyParcelCom\ApiSdk\MyParcelComApiInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Proxy\OrganizationProxy;
use MyParcelCom\ApiSdk\Tests\Traits\MocksApiCommunication;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;

class OrganizationProxyTest extends TestCase
{
    use MocksApiCommunication;

    /** @var ClientInterface */
    private $client;
    /** @var AuthenticatorInterface */
    private $authenticator;
    /** @var MyParcelComApiInterface */
    private $api;
    /** @var OrganizationProxy */
    private $organizationProxy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->getClientMock();
        $this->authenticator = $this->getAuthenticatorMock();
        $this->api = (new MyParcelComApi('https://api', $this->client))
            ->setCache($this->getNullCache())
            ->authenticate($this->authenticator);

        $this->organizationProxy = (new OrganizationProxy())
            ->setMyParcelComApi($this->api)
            ->setId('eef00b32-177e-43d3-9b26-715365e4ce46');
    }

    /** @test */
    public function testAccessors()
    {
        $this->assertEquals('Organization Name', $this->organizationProxy->setName('Organization Name')->getName());
        $this->assertEquals('an-id-for-a-organization', $this->organizationProxy->setId('an-id-for-a-organization')->getId());
    }

    /** @test */
    public function testAttributes()
    {
        $this->assertEquals('eef00b32-177e-43d3-9b26-715365e4ce46', $this->organizationProxy->getId());
        $this->assertEquals(ResourceInterface::TYPE_ORGANIZATION, $this->organizationProxy->getType());
        $this->assertEquals('Test Organization', $this->organizationProxy->getName());
    }

    /** @test */
    public function testJsonSerialize()
    {
        $organizationProxy = new OrganizationProxy();
        $organizationProxy
            ->setMyParcelComApi($this->api)
            ->setResourceUri('https://api/organizations/eef00b32-177e-43d3-9b26-715365e4ce46')
            ->setId('organization-id-1');

        $this->assertEquals([
            'id'   => 'organization-id-1',
            'type' => ResourceInterface::TYPE_ORGANIZATION,
        ], $organizationProxy->jsonSerialize());
    }
}
