<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Feature;

use MyParcelCom\ApiSdk\MyParcelComApi;
use MyParcelCom\ApiSdk\Tests\TestCase;

class MyParcelComApiTest extends TestCase
{
    public function testSingleton()
    {
        $this->assertNull(MyParcelComApi::getSingleton());

        $api = MyParcelComApi::createSingleton($this->authenticator, 'https://api', $this->client);
        $this->assertInstanceOf(MyParcelComApi::class, $api);
        $this->assertEquals($api, MyParcelComApi::getSingleton());
    }

    /** @test */
    public function testAuthenticate()
    {
        $api = new MyParcelComApi('https://api', $this->client);

        $this->assertEquals(
            $api,
            $api->authenticate($this->authenticator),
            'Api should return itself and not throw an error when a functioning authenticator is used'
        );
    }
}
