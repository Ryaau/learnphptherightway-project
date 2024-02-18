<?php

namespace Tests\Unit\Services;

use App\Services\EmailService;
use App\Services\InvoiceService;
use App\Services\PaymentGatewayService;
use App\Services\SalesTaxService;
use PHPUnit\Framework\TestCase;

class InvoiceServiceTest extends TestCase
{
    public function test_it_processes_invoice()
    {
        $salesTaxServiceMock       = $this->createMock(
            SalesTaxService::class
        );
        $paymentGatewayServiceMock = $this->createMock(
            PaymentGatewayService::class
        );
        $emailServiceMock          = $this->createMock(
            EmailService::class
        );

        $paymentGatewayServiceMock->method('charge')->willReturn(true);


        // given [invoice service]
        $invoiceService = new InvoiceService(
            $salesTaxServiceMock, $paymentGatewayServiceMock, $emailServiceMock
        );


        $customer = ['name' => 'yoworu'];
        $amount   = 150;

        // when process is called
        $result = $invoiceService->process($customer, $amount);

        // then assert invoice is processed successfully
        $this->assertTrue($result);
    }

    public function test_it_sends_receipt_email_when_invoice_is_processed()
    {
        $salesTaxServiceMock       = $this->createMock(
            SalesTaxService::class
        );
        $paymentGatewayServiceMock = $this->createMock(
            PaymentGatewayService::class
        );
        $emailServiceMock          = $this->createMock(
            EmailService::class
        );
        $paymentGatewayServiceMock->method('charge')->willReturn(true);

        $emailServiceMock->expects($this->once())->method('send')->with(
            ['name' => 'yoworu'],
            'receipt'
        );

        $invoiceService = new InvoiceService(
            $salesTaxServiceMock, $paymentGatewayServiceMock, $emailServiceMock
        );

        $customer = ['name' => 'yoworu'];
        $amount   = 150;


        $invoiceService->process($customer, $amount);
    }
}
