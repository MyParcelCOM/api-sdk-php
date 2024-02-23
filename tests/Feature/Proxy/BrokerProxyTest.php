<?php

declare(strict_types=1);

namespace Feature\Proxy;

use MyParcelCom\ApiSdk\MyParcelComApi;
use MyParcelCom\ApiSdk\MyParcelComApiInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Proxy\BrokerProxy;
use MyParcelCom\ApiSdk\Tests\Traits\MocksApiCommunication;
use PHPUnit\Framework\TestCase;

class BrokerProxyTest extends TestCase
{
    use MocksApiCommunication;

    private MyParcelComApiInterface $api;
    private BrokerProxy $brokerProxy;

    protected function setUp(): void
    {
        parent::setUp();

        $client = $this->getClientMock();
        $authenticator = $this->getAuthenticatorMock();
        $this->api = (new MyParcelComApi('https://api', $client))
            ->setCache($this->getNullCache())
            ->authenticate($authenticator);

        $this->brokerProxy = (new BrokerProxy())
            ->setMyParcelComApi($this->api)
            ->setId('eef00b32-177e-43d3-9b26-715365e4ce47');
    }

    /** @test */
    public function testAccessors()
    {
        $this->assertEquals('an-id-for-a-broker', $this->brokerProxy->setId('an-id-for-a-broker')->getId());
    }

    /** @test */
    public function testAttributes()
    {
        $this->assertEquals('eef00b32-177e-43d3-9b26-715365e4ce47', $this->brokerProxy->getId());
        $this->assertEquals(ResourceInterface::TYPE_BROKER, $this->brokerProxy->getType());
    }

    /** @test */
    public function testJsonSerialize()
    {
        $serviceProxy = new BrokerProxy();
        $serviceProxy
            ->setMyParcelComApi($this->api)
            ->setResourceUri('https://api/brokers/eef00b32-177e-43d3-9b26-715365e4ce47')
            ->setId('broker-id-1');

        $this->assertEquals([
            'id'   => 'broker-id-1',
            'type' => ResourceInterface::TYPE_BROKER,
        ], $serviceProxy->jsonSerialize());
    }
}
