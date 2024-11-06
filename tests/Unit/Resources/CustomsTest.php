<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Unit\Resources;

use MyParcelCom\ApiSdk\Resources\Customs;
use MyParcelCom\ApiSdk\Resources\Interfaces\CustomsInterface;
use PHPUnit\Framework\TestCase;

class CustomsTest extends TestCase
{
    /** @test */
    public function testContentType()
    {
        $customs = new Customs();
        $this->assertEquals(CustomsInterface::CONTENT_TYPE_GIFTS, $customs->setContentType(CustomsInterface::CONTENT_TYPE_GIFTS)->getContentType());
    }

    /** @test */
    public function testInvoiceNumber()
    {
        $customs = new Customs();
        $this->assertEquals('Invoice#007', $customs->setInvoiceNumber('Invoice#007')->getInvoiceNumber());
    }

    /** @test */
    public function testNonDelivery()
    {
        $customs = new Customs();
        $this->assertEquals(CustomsInterface::NON_DELIVERY_ABANDON, $customs->setNonDelivery(CustomsInterface::NON_DELIVERY_ABANDON)->getNonDelivery());
    }

    /** @test */
    public function testIncoterm()
    {
        $customs = new Customs();
        $this->assertEquals(CustomsInterface::INCOTERM_DDP, $customs->setIncoterm(CustomsInterface::INCOTERM_DDP)->getIncoterm());
    }

    /** @test */
    public function testLicenseNumber()
    {
        $customs = new Customs();
        $this->assertEquals('512842382', $customs->setLicenseNumber('512842382')->getLicenseNumber());
    }

    /** @test */
    public function testCertificateNumber()
    {
        $customs = new Customs();
        $this->assertEquals('2112211', $customs->setCertificateNumber('2112211')->getCertificateNumber());
    }

    /** @test */
    public function testShippingValue()
    {
        $customs = new Customs();
        $this->assertEquals(4242, $customs->setShippingValueAmount(4242)->getShippingValueAmount());
        $this->assertEquals('GBP', $customs->setShippingValueCurrency('GBP')->getShippingValueCurrency());
    }

    /** @test */
    public function testJsonSerialize()
    {
        $customs = (new Customs())
            ->setIncoterm(CustomsInterface::INCOTERM_DAP)
            ->setNonDelivery(CustomsInterface::NON_DELIVERY_RETURN)
            ->setInvoiceNumber('NO.5')
            ->setContentType(CustomsInterface::CONTENT_TYPE_DOCUMENTS)
            ->setLicenseNumber('512842382')
            ->setCertificateNumber('2112211');

        $this->assertEquals([
            'content_type'       => 'documents',
            'invoice_number'     => 'NO.5',
            'non_delivery'       => 'return',
            'incoterm'           => 'DAP',
            'license_number'     => '512842382',
            'certificate_number' => '2112211',
        ], $customs->jsonSerialize());
    }
}
