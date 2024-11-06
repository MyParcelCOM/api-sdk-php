<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Unit\Utils;

use MyParcelCom\ApiSdk\Utils\StringUtils;
use PHPUnit\Framework\TestCase;

class StringUtilsTest extends TestCase
{
    /** @test */
    public function testSnakeToCamelCase()
    {
        $tests = [
            'hello_world'   => 'helloWorld',
            'round_1_fight' => 'round1Fight',
            'test'          => 'test',
        ];

        array_walk($tests, function ($expected, $actual) {
            $this->assertEquals($expected, StringUtils::snakeToCamelCase($actual));
        });
    }

    /** @test */
    public function testSnakeToPascalCase()
    {
        $tests = [
            'hello_world'   => 'HelloWorld',
            'round_1_fight' => 'Round1Fight',
            'test'          => 'Test',
        ];

        array_walk($tests, function ($expected, $actual) {
            $this->assertEquals($expected, StringUtils::snakeToPascalCase($actual));
        });
    }

    /** @test */
    public function testCamelToSnakeCase()
    {
        $tests = [
            'helloWorld'  => 'hello_world',
            'round1Fight' => 'round_1_fight',
            'test'        => 'test',
        ];

        array_walk($tests, function ($expected, $actual) {
            $this->assertEquals($expected, StringUtils::camelToSnakeCase($actual));
        });
    }
}
