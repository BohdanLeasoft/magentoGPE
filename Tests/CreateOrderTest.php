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


//        $this->expectedArray = array(
//            "currency" => "EUR",
//            "amount" => (double)500,
//            "merchant_order_id" => (string)638,
//            "customer"  => [
//                "address_type" => "customer",
//                "locale" => "en_GB",
//                "phone_numbers" => [""],
//                'gender' => "male",
//                'birthdate' => "2021-09-01",
//                "additional_addresses" => [["address_type" => "billing"]]],
//            "description" => "Your order 638 at Your order %id% at %name%",
//            "return_url" => "ext/modules/payment/ginger/router.php",
//            "transactions" => [["payment_method" => (string)$this->payment->id]],
//            "extra" => [
//                "user_agent" => null,
//                "platform_name" => "osCommerce",
//                "platform_version" => null,
//                "plugin_name" => "ems-online-oscommerce",
//                "plugin_version" => $this->ginger->plugin_version],
//            "webhook_url" => "ext/modules/payment/ginger/router.php"
//        );
//
//        $this->expectedOrderLines = array(
//            '0' => [
//                'amount' => 202,
//                'currency' => "EUR",
//                'merchant_order_line_id' => "0_10",
//                'name' => "productName",
//                'quantity' => 2,
//                'type' => "physical",
//                'vat_percentage' => 1,
//                'url' => "FILENAME_PRODUCT_INFO",
//            ],
//            '1' => [
//                'amount' => 101,
//                'currency' => "EUR",
//                'merchant_order_line_id' => "0_shipping",
//                'name' => "shipping",
//                'quantity' => 1,
//                'type' => "shipping_fee",
//                'vat_percentage' => 1,
//            ]
//        );

    }

    public function testOrderCreation()
    {
      //  var_dump($this->orderLines->get('dd')); die();
        $arr = $this->orderBuilder->collectDataForOrder($this->order, 'ideal', 'ginger_methods_ideal', $this->urlProvider, $this->orderLines, $this->customerData);
        var_dump($arr);//['order_lines']
    }
}