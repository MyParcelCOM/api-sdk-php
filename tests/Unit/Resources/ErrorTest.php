<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Unit\Resources;

use MyParcelCom\ApiSdk\Resources\Error;
use PHPUnit\Framework\TestCase;

class ErrorTest extends TestCase
{
    /** @test */
    public function testAccessors()
    {
        $error = new Error();
        $this->assertEquals('error-id', $error->setId('error-id')->getId());
        $this->assertEquals('444', $error->setStatus('444')->getStatus());
        $this->assertEquals('wr0ng1', $error->setCode('wr0ng1')->getCode());
        $this->assertEquals('Something gone wrong', $error->setTitle('Something gone wrong')->getTitle());
        $this->assertEquals(
            'You did something bad that caused other things to go bad',
            $error->setDetail('You did something bad that caused other things to go bad')->getDetail()
        );
        $this->assertEquals(['docs' => 'https://docs.myparcel.com'], $error->setLinks(['docs' => 'https://docs.myparcel.com'])->getLinks());
        $this->assertEquals(['pointer' => '/the/invalid/attribute'], $error->setSource(['pointer' => '/the/invalid/attribute'])->getSource());
        $this->assertEquals(
            [
                'you' => [
                    'wanted' => 'some',
                    'extra'  => 'data',

                ],
            ],
            $error->setMeta(
                [
                    'you' => [
                        'wanted' => 'some',
                        'extra'  => 'data',

                    ],
                ]
            )->getMeta()
        );
    }

    /** @test */
    public function testJsonSerialize()
    {
        $error = (new Error())
            ->setId('error-id')
            ->setStatus('444')
            ->setCode('wr0ng1')
            ->setTitle('Something gone wrong')
            ->setDetail('You did something bad that caused other things to go bad')
            ->setLinks(['docs' => 'https://docs.myparcel.com'])
            ->setSource(['pointer' => '/the/invalid/attribute'])
            ->setMeta(
                [
                    'you' => [
                        'wanted' => 'some',
                        'extra'  => 'data',
                    ],
                ]
            );

        $this->assertEquals(
            [

                'id'     => 'error-id',
                'status' => '444',
                'code'   => 'wr0ng1',
                'title'  => 'Something gone wrong',
                'detail' => 'You did something bad that caused other things to go bad',
                'links'  => [
                    'docs' => 'https://docs.myparcel.com',
                ],
                'source' => [
                    'pointer' => '/the/invalid/attribute',
                ],
                'meta'   => [
                    'you' => [
                        'wanted' => 'some',
                        'extra'  => 'data',
                    ],
                ],
            ],
            $error->jsonSerialize()
        );
    }
}
