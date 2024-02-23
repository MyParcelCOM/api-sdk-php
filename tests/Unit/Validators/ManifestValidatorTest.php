<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Unit\Validators;

use MyParcelCom\ApiSdk\Resources\Interfaces\AddressInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\BrokerInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ManifestInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\OrganizationInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\ApiSdk\Resources\Manifest;
use MyParcelCom\ApiSdk\Utils\StringUtils;
use MyParcelCom\ApiSdk\Validators\ManifestValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ManifestValidatorTest extends TestCase
{
    private AddressInterface $address;
    private ContractInterface $contract;
    private BrokerInterface|OrganizationInterface|ShopInterface|MockObject $owner;
    /** @var ShipmentInterface&MockObject[] */
    private array $shipments;
    private string $name;

    protected function setUp(): void
    {
        parent::setUp();

        $this->address = $this->getMockBuilder(AddressInterface::class)->getMock();
        $this->address->method('getStreet1')->willReturn('Street 1');
        $this->address->method('getCity')->willReturn('Amsterdam');
        $this->address->method('getCountryCode')->willReturn('NL');

        $this->contract = $this->getMockBuilder(ContractInterface::class)->getMock();

        $this->name = 'Manifest Name';

        $this->owner = $this->getMockBuilder(ShopInterface::class)->getMock();
        $this->owner->method('getId')->willReturn('owner-id');
        $this->owner->method('getType')->willReturn(ResourceInterface::TYPE_SHOP);

        $shipment = $this->getMockBuilder(ShipmentInterface::class)->getMock();
        $shipment->method('getId')->willReturn('shipment-id');
        $this->shipments = [$shipment];
    }

    public function testHasErrors()
    {
        $validator = new ManifestValidator(new Manifest());

        $this->assertEquals([], $validator->getErrors());
        $this->assertFalse($validator->hasErrors());

        $validator->setErrors(['woei']);
        $validator->addError('boem');

        $this->assertEquals(['woei', 'boem'], $validator->getErrors());
        $this->assertTrue($validator->hasErrors());

        $validator->clearErrors();
        $this->assertEquals([], $validator->getErrors());
        $this->assertFalse($validator->hasErrors());
    }

    public function testIsValid()
    {
        $this->address->method('getStreet1')->willReturn('Street 1');
        $this->address->method('getCity')->willReturn('Amsterdam');
        $this->address->method('getCountryCode')->willReturn('NL');

        $this->owner->method('getId')->willReturn('owner-id');
        $this->owner->method('getType')->willReturn(ResourceInterface::TYPE_SHOP);

        $this->shipments[0]->method('getId')->willReturn('shipment-id');

        $manifest = (new Manifest())
            ->setName($this->name)
            ->setAddress($this->address)
            ->setContract($this->contract)
            ->setOwner($this->owner)
            ->setShipments($this->shipments);

        $validator = new ManifestValidator($manifest);

        $this->assertTrue($validator->isValid());
    }

    public function testMissingName()
    {
        $manifest = $this->createManifestMissing('name');

        $validator = new ManifestValidator($manifest);

        $this->assertFalse($validator->isValid());
    }

    public function testMissingAddress()
    {
        $manifest = $this->createManifestMissing('address');

        $validator = new ManifestValidator($manifest);

        $this->assertFalse($validator->isValid());
    }

    public function testMissingOwner()
    {
        $manifest = $this->createManifestMissing('owner');

        $validator = new ManifestValidator($manifest);

        $this->assertFalse($validator->isValid());
    }

    public function testMissingShipments()
    {
        $manifest = $this->createManifestMissing('shipments');

        $validator = new ManifestValidator($manifest);

        $this->assertFalse($validator->isValid());
    }

    /**
     * Creates and returns a Manifest model with all the required properties
     * except the given property.
     */
    private function createManifestMissing(string $missingProperty): ManifestInterface
    {
        $missingProperty = StringUtils::snakeToCamelCase($missingProperty);
        $manifest = new Manifest();
        $requiredProperties = ['name', 'address', 'contract', 'owner', 'shipments'];

        foreach ($requiredProperties as $requiredProperty) {
            $requiredProperty = StringUtils::snakeToCamelCase($requiredProperty);

            if ($missingProperty !== $requiredProperty) {
                $setter = 'set' . ucfirst($requiredProperty);
                $manifest->$setter($this->$requiredProperty);
            }
        }

        return $manifest;
    }
}
