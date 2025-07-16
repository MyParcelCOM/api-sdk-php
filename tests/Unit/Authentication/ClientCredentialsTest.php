<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Unit\Authentication;

use GuzzleHttp\Psr7\Message;
use GuzzleHttp\Psr7\Request;
use MyParcelCom\ApiSdk\Authentication\ClientCredentials;
use MyParcelCom\ApiSdk\Exceptions\AuthenticationException;
use MyParcelCom\ApiSdk\Http\Exceptions\RequestException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class ClientCredentialsTest extends TestCase
{
    /** @var ClientInterface */
    private $httpClient;

    /** @var int */
    private $delay = 0;

    /** @var int */
    private $tokenSuffix = 1;

    /** @var ResponseInterface */
    private $response;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock a response from the http client.
        $this->response = $this->getMockBuilder(ResponseInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $this->response->method('getBody')
            ->willReturnCallback(function () {
                $streamMock = $this->createMock(StreamInterface::class);
                $streamMock->method('__toString')->willReturn(json_encode([
                    'token_type'   => 'Bearer',
                    'expires_in'   => 86400,
                    'access_token' => 'an-access-token-for-the-myparcelcom-api-' . $this->tokenSuffix,
                ]));

                return $streamMock;
            });
        $this->response->method('getStatusCode')
            ->willReturn(200);

        // Mock an http client.
        $this->httpClient = $this->getMockBuilder(ClientInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        // Check that if an async request is done, that the correct values are used.
        $this->httpClient->method('sendRequest')
            ->willReturnCallback(function (RequestInterface $request) {
                $method = strtolower($request->getMethod());
                $path = urldecode((string) $request->getUri());
                $jsonBody = json_decode((string) $request->getBody(), true);

                if ($this->response->getStatusCode() >= 400) {
                    throw new RequestException(new Request($method, $path), $this->response);
                }

                $this->assertEquals(
                    'POST',
                    strtoupper($method),
                    'Post method should be used when trying to request an access token'
                );
                $this->assertEquals(
                    'https://auth.myparcel.com/access-token',
                    strtolower($path),
                    'Requested path should match set uri followed by `/access-token`'
                );

                $expectedJson = [
                    'grant_type'    => 'client_credentials',
                    'client_id'     => 'client-id',
                    'client_secret' => 'shhh-dont-tell-anyone',
                    'scope'         => ClientCredentials::SCOPES,
                ];

                if (isset($jsonBody)) {
                    $this->assertEquals(
                        $expectedJson,
                        $jsonBody,
                        'Request body did not contain required json fields'
                    );
                }

                // Wait for a bit to simulate request delay.
                usleep($this->delay);

                return $this->response;
            });
    }

    /** @test */
    public function testGetAuthorizationHeader()
    {
        $clientCredentials = (new ClientCredentials(
            'client-id',
            'shhh-dont-tell-anyone',
            'https://auth.myparcel.com'
        ))->setHttpClient($this->httpClient);

        $this->assertEquals(
            [
                'Authorization' => 'Bearer an-access-token-for-the-myparcelcom-api-1',
            ],
            $clientCredentials->getAuthorizationHeader(true)
        );
    }

    /** @test */
    public function testGetCachedAuthorizationHeader()
    {
        $clientCredentials = (new ClientCredentials(
            'client-id',
            'shhh-dont-tell-anyone',
            'https://auth.myparcel.com'
        ))->setHttpClient($this->httpClient);

        $headerA = $clientCredentials->getAuthorizationHeader(true);
        // Change the token suffix, so a new request will yield a new token.
        $this->tokenSuffix = 12452;
        $headerB = $clientCredentials->getAuthorizationHeader();
        $clientCredentials->clearCache();
        $headerC = $clientCredentials->getAuthorizationHeader();

        $this->assertEquals(
            $headerA,
            $headerB,
            'Subsequent auth requests should return the same headers'
        );
        $this->assertNotEquals(
            $headerB,
            $headerC,
            'Requesting auth headers after clearing the cache should return new headers'
        );
    }

    /** @test */
    public function testGetAuthorizationHeaderInvalidCredentials()
    {
        $clientCredentials = (new ClientCredentials(
            'wrong-client-id',
            'shhh-dont-tell-anyone',
            'https://auth.myparcel.com'
        ))->setHttpClient($this->httpClient);

        $this->response = Message::parseResponse('HTTP/1.1 403
status: 403
content-type: application/vnd.api+json

{"errors":[{"status":"403","code":"14000","title":"Invalid OAuth Client","detail":"The supplied client credentials are invalid or the client does not have access to this grant type."}]}');

        $exceptionThrown = false;
        try {
            $clientCredentials->getAuthorizationHeader(true);
        } catch (AuthenticationException $exception) {
            $this->assertNotFalse(
                stripos($exception->getMessage(), 'client id'),
                'The authentication exception should mention the client id'
            );
            $this->assertNotFalse(
                stripos($exception->getMessage(), 'client secret'),
                'The authentication exception should mention the client secret'
            );
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown, 'No exception was thrown during authentication with invalid credentials');
    }

    /**
     * Test that 2 auth requests being sent at the same time should yield the
     * same access tokens.
     *
     * @note This test is quite different from all the other tests. What we need
     *       to do is start 2 separate php processes that each test retrieving
     *       the auth headers. Because they are using the same cache, they
     *       should be able to wait for each other and return the same headers.
     *
     * @test
     */
    public function testGetAuthorizationHeaderConcurrentRequests()
    {
        [$processA, $outputA] = $this->runEchoAuthHeader();
        // After firing the first process, we need to wait for a bit to make
        // sure it is actually running.
        // Note that the fact that we have to wait here implies that 2 auth
        // attempts at exactly the same time will still cause 2 requests to the
        // server.
        usleep(10000);
        [$processB, $outputB] = $this->runEchoAuthHeader();

        // Wait for the contents (which should be the headers exported).
        $headerA = stream_get_contents($outputA);
        $headerB = stream_get_contents($outputB);

        // Clean up streams and processes.
        fclose($outputA);
        fclose($outputB);
        proc_close($processA);
        proc_close($processB);

        $this->assertEquals(
            $headerA,
            $headerB,
            'Two authentication attempts at roughly the same time should yield the same headers'
        );
    }

    /**
     * Helper method that starts a new php process that calls `echoAuthHeader()`
     * and returns the process resource and the output resource in an array.
     *
     * @return array
     */
    private function runEchoAuthHeader()
    {
        // Start a php process that reads from '0' and writes to '1'.
        $proc = proc_open(
            'php',
            [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
            ],
            $pipes,
            // Use the root dir of the project.
            dirname(dirname(dirname(__DIR__)))
        );

        // Send a simple php script to the php process that includes the
        // autoloader and then calls `echoAuthHeader()`.
        fwrite($pipes[0], '<?php 
        include \'vendor/autoload.php\';
        $test = new MyParcelCom\\ApiSdk\\Tests\\Unit\\Authentication\\ClientCredentialsTest();
        $test->echoAuthHeader();
        ?>');
        // Close the pipe, we don't want to send anymore commands to the php
        // process.
        fclose($pipes[0]);

        return [$proc, $pipes[1]];
    }

    /**
     * Get the auth headers and echo them.
     *
     * @note This method is not meant to be called directly in a test, but from
     *       an external php process.
     *
     * @see  ClientCredentialsTest::runEchoAuthHeader()
     */
    public function echoAuthHeader()
    {
        $this->setUp();

        // Randomly generate the token for each time this is called.
        $this->tokenSuffix = rand(2, 100000);
        // Make the request take 2s, which should give any other process
        // enough time to start.
        $this->delay = 2000000;

        $clientCredentials = (new ClientCredentials(
            'client-id',
            'shhh-dont-tell-anyone',
            'https://auth.myparcel.com'
        ))->setHttpClient($this->httpClient);

        // Dump the headers to stdout.
        var_export($clientCredentials->getAuthorizationHeader(true));

        $this->tearDown();
    }
}
