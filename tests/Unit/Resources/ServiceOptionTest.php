<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Unit\Resources;

use MyParcelCom\ApiSdk\Resources\ServiceOption;
use PHPUnit\Framework\TestCase;

class ServiceOptionTest extends TestCase
{
    /** @test */
    public function testId(): void
    {
        $option = new ServiceOption();
        $this->assertEquals('service-option-id', $option->setId('service-option-id')->getId());
    }

    /** @test */
    public function testType(): void
    {
        $option = new ServiceOption();
        $this->assertEquals('service-options', $option->getType());
    }

    /** @test */
    public function testName(): void
    {
        $option = new ServiceOption();
        $this->assertEquals('Sign on delivery', $option->setName('Sign on delivery')->getName());
    }

    /** @test */
    public function testCode(): void
    {
        $option = new ServiceOption();
        $this->assertEquals('some-code', $option->setCode('some-code')->getCode());
    }

    /** @test */
    public function testCategory(): void
    {
        $option = new ServiceOption();
        $this->assertEquals('some-category', $option->setCategory('some-category')->getCategory());
    }

    /** @test */
    public function testItSetsAndGetsPrice(): void
    {
        $option = new ServiceOption();
        $this->assertNull($option->getPrice());
        $this->assertEquals(200, $option->setPrice(200)->getPrice());
    }

    /** @test */
    public function testItSetsAndGetsCurrency(): void
    {
        $option = new ServiceOption();
        $this->assertNull($option->getCurrency());
        $this->assertEquals('GBP', $option->setCurrency('GBP')->getCurrency());
    }

    /** @test */
    public function testItSetsAndGetsIncluded(): void
    {
        $option = new ServiceOption();
        $this->assertFalse($option->isIncluded());
        $this->assertTrue($option->setIncluded(true)->isIncluded());
    }

    /** @test */
    public function testItSetsAndGetsValues(): void
    {
        $option = new ServiceOption();
        $this->assertNull($option->getValues());
        $this->assertEquals(['val'], $option->setValues(['val'])->getValues());
        $this->assertNull($option->setValues(null)->getValues());
    }

    /** @test */
    public function testJsonSerialize(): void
    {
        $option = (new ServiceOption())
            ->setId('service-option-id')
            ->setName('Sign on delivery')
            ->setCode('some-code')
            ->setCategory('some-category')
            ->setValues(['val']);

        $this->assertEquals([
            'id'         => 'service-option-id',
            'type'       => 'service-options',
            'attributes' => [
                'name'     => 'Sign on delivery',
                'code'     => 'some-code',
                'category' => 'some-category',
            ],
            'meta'       => [
                'values' => ['val'],
            ],
        ], $option->jsonSerialize());
    }
}
