<?php

namespace GingerPay\Payment\Tests;

require_once  __DIR__.'/ClassSeparators/ServiceOrderBuilderSeparator.php';
require_once  __DIR__.'/Mocks/Order.php';
require_once  __DIR__.'/Mocks/UrlProvider.php';
require_once  __DIR__.'/Mocks/OrderLines.php';
require_once  __DIR__.'/Mocks/Customer.php';

use GingerPay\Payment\Tests\Mocks\Order;
use GingerPay\Payment\Tests\Mocks\OrderLines;
use GingerPay\Payment\Tests\Mocks\UrlProvider;
use GingerPay\Payment\Tests\Mocks\Customer;
use PHPUnit\Framework\TestCase;
use GingerPay\Payment\Tests\ClassSeparators\ServiceOrderBuilderSeparator;

class CreateOrderTest extends TestCase
{
    private $orderBuilder;
    private $order;
    private $urlProvider;
    private $orderLines;
    private $customerData;
    private $expectedArray;

    public function setUp() : void
    {
        $this->orderBuilder = new ServiceOrderBuilderSeparator();
        $this->order = new Order();
        $this->urlProvider = new UrlProvider();
        $this->orderLines = new OrderLines();
        $this->customerData = Customer::getCustomerData();


        $this->expectedArray = array(
            "currency" => "EUR",
            "amount" => 500,
            "merchant_order_id" => 638,
            "customer"  => [
                "merchant_customer_id" => "638",
                "email_address" => "Test3@ukr.net",
                'first_name' => "Jon",
                'last_name' => "Doe",
                'address_type' => "billing",
                'address' => "Donauweg 10",
                'postal_code' => "1043 AJ",
                'housenumber' => "10",
                'country' => "NL",
                'phone_numbers' => [ '0' => "0555869119"]],
            "description" => "Your order 638 at Your order %id% at %name%",
            "return_url" => "https://magento2.test/ginger/checkout/process/",

            "transactions" => [["payment_method" => 'ideal']],
            "extra" => [
                "user_agent" => 'USER_AGENT',
                "platform_name" => "Magento2",
                "platform_version" => '2.2.11',
                "plugin_name" => "ems-online-magento-2",
                "plugin_version" => '1.1.0'],
            "order_lines" => [[
                '0' => [
                    'type' => 'physical',
                    'url' => 'https://magento2.test/newsuperproduct.html',
                    'name' => 'NewSuperProduct',
                    'amount' => '500',
                    'currency' => 'EUR',
                    'quantity' => '1',
                    'vat_percentage' => 0,
                    'merchant_order_line_id' => 638
                ]
            ]],
            "webhook_url" => "https://magento2.test/ginger/checkout/webhook/"
        );

    }

    public function testGetTransactions()
    {
        $this->assertEquals($this->orderBuilder->getTransactions('ideal'), [["payment_method" => 'ideal']], 'Function getTransactions  return not expected array');
    }

    public function testGetVersion()
    {
        $this->assertEquals($this->orderBuilder->productMetadata->getVersion(), '2.2.11', 'Function getVersion returned not expected string');
    }



    public function testGetUserAgent()
    {
        $this->assertEquals($this->orderBuilder->getUserAgent(), 'USER_AGENT', 'Function getUserAgent returned not expected string');
    }

    public function testGetExtraLines()
    {
        $expectedExtraLines = [
            "user_agent" => 'USER_AGENT',
            "platform_name" => "Magento2",
            "platform_version" => '2.2.11',
            "plugin_name" => "ems-online-magento-2",
            "plugin_version" => '1.1.0'];
        $this->assertEquals($this->orderBuilder->getExtraLines(), $expectedExtraLines, 'Function getExtraLines returned not expected array');
    }

    public function testOrderCreation()
    {
        $orderArray = $this->orderBuilder->collectDataForOrder($this->order, 'ideal', 'ginger_methods_ideal', $this->urlProvider, $this->orderLines, $this->customerData);
        $this->assertEquals($this->expectedArray, $orderArray, 'Order array does not match the expectation');
    }
}