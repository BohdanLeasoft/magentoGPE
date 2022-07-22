<?php

namespace GingerPay\Payment\Tests;

require_once __DIR__.'/ClassSeparators/RecurringHelperSeparator.php';

use GingerPay\Payment\Tests\Mocks\Order;
use GingerPay\Payment\Tests\ClassSeparators\RecurringHelperSeparator;
use PHPUnit\Framework\TestCase;

class RecurringHelperTest extends TestCase
{
    private $order;
    private $recurringHelper;

    public function setUp() : void    {
        $this->order = new Order();
        $this->recurringHelper = new RecurringHelperSeparator();
    }

    public function testGetNextPaymentDate()
    {
        $this->assertEquals('1648735440', $this->recurringHelper->getNextPaymentDate('1648735140', '+5 minutes'), 'Returned unexpected next payment date');
    }

    public function testInitializeRecurringOrder()
    {
        $this->assertTrue($this->recurringHelper->initializeRecurringOrder($this->order, true), 'It seems like a problem with initializeRecurringOrder');
    }

    public function testIsItRecurringTransaction()
    {
        $this->assertTrue($this->recurringHelper->isItRecurringTransaction(['transactions'=>[['payment_method_details' =>['recurring_type' => 'first']]]]), 'It should return true');
    }

    public function testGetRecurringCancelUrl()
    {
        $expectedUrl = "https://magento2.test/ginger/checkout/webhook/?order_id=12345-abcdf-zaqws-8659";
        $this->assertEquals($expectedUrl, $this->recurringHelper->getRecurringCancelUrlByOrderId($this->order->getGingerpayVaultToken()), 'Returned unexpected url');
    }

    public function testGetRecurringCancelLinkMessage()
    {
        $expectedMessage = 'This subscription payment completed. It could be canceled by: <a href="https://magento2.test/ginger/checkout/webhook/?order_id=12345-abcdf-zaqws-8659">Cancel subscription</a>';
        $this->assertEquals($expectedMessage, $this->recurringHelper->getRecurringCancelLinkMessage($this->order), 'Returned unexpected message');
    }
}
