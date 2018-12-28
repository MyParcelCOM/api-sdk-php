<?php

namespace MyParcelCom\ApiSdk\Tests\Traits;

use MyParcelCom\ApiSdk\Http\Exceptions\RequestException;
use MyParcelCom\ApiSdk\Http\Contracts\HttpClient\ClientInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use MyParcelCom\ApiSdk\Authentication\AuthenticatorInterface;
use Psr\Http\Message\RequestInterface;

trait MocksApiCommunication
{
    protected $clientCalls = [];

    protected function getClientMock()
    {
        $client = $this->getMockBuilder(ClientInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $client
            ->method('sendRequest')
            ->willReturnCallback(function (RequestInterface $request) {
                $method = strtolower($request->getMethod());
                $uri = urldecode((string)$request->getUri());
                $jsonBody = $request->getBody()->getContents();

                $filePath = implode(DIRECTORY_SEPARATOR, [
                        dirname(dirname(__FILE__)),
                        'Stubs',
                        $method,
                        str_replace(
                            ['?', '&'],
                            DIRECTORY_SEPARATOR,
                            str_replace(
                                [':', '{', '}', '(', ')', '/', '\\', '@', '[', ']', '='],
                                '-',
                                $uri
                            )),
                    ]) . '.json';

                $filePath = urldecode($filePath);

                if (strpos($filePath, 'stream') !== false) {
                    $filePath = preg_replace('/.json/', '.txt', $filePath);
                }

                if (!file_exists($filePath)) {
                    throw new \RuntimeException(sprintf(
                        'File with path `%s` does not exist, please create this file with valid response data',
                        $filePath
                    ));
                }

                $returnJson = file_get_contents($filePath);

                if ($method === 'post') {
                    // Any post will have the data from the stub added to the
                    // original request. This simulates the api creating the
                    // resource and returning it with added attributes.
                    $returnJson = json_encode(
                        array_merge_recursive(
                            json_decode($returnJson, true),
                            // You may wonder why we would encode and then
                            // decode this, but it is possible that the json in
                            // the options array is not an associative array,
                            // which we need to be able to merge.
                            json_decode(json_encode($jsonBody), true)
                        )
                    );
                }
                if ($method === 'patch') {
                    // Any patch will have the data from the stub merged with the
                    // original request data. This simulates the api updating the
                    // resource and returning it with merged attributes.
                    $returnJson = json_encode(
                        array_replace_recursive(
                            json_decode($returnJson, true),
                            json_decode(json_encode($jsonBody), true)
                        )
                    );
                }

                if (strpos($returnJson, '"errors": [') !== false && strpos($returnJson, '"data": ') === false) {
                    throw new RequestException(
                        'This carrier does not have any pickup and dropoff locations.',
                        new Request($method, $uri),
                        new Response(500, [], $returnJson)
                    );
                }

                $response = new Response(200, [], $returnJson);

                if (!isset($this->clientCalls[$uri])) {
                    $this->clientCalls[$uri] = 0;
                }
                $this->clientCalls[$uri]++;

                return $response;
            });

        return $client;
    }

    protected function getAuthenticatorMock()
    {
        $authenticator = $this->getMockBuilder(AuthenticatorInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $authenticator->method('getAuthorizationHeader')
            ->willReturn(['Authorization' => 'Bearer test-api-token']);

        return $authenticator;
    }
}
