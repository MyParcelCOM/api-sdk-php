<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Unit;

use MyParcelCom\ApiSdk\Resources\Organization;
use PHPUnit\Framework\TestCase;

class OrganizationTest extends TestCase
{
    public function testId()
    {
        $organization = new Organization();

        $this->assertNull($organization->getId());

        $this->assertEquals('organization-id', $organization->setId('organization-id')->getId());
    }

    public function testType()
    {
        $organization = new Organization();

        $this->assertEquals('organizations', $organization->getType());
    }

    public function testName()
    {
        $organization = new Organization();

        $this->assertNull($organization->getName());

        $this->assertEquals('Organization Name', $organization->setName('Organization Name')->getName());
    }

    public function testJsonSerialize()
    {
        $organization = (new Organization())
            ->setId('organization-id')
            ->setName('Organization Name');

        $this->assertEquals([
            'id'         => 'organization-id',
            'type'       => 'organizations',
            'attributes' => [
                'name' => 'Organization Name',
            ],
        ], $organization->jsonSerialize());
    }
}
