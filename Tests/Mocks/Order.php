<?php

namespace GingerPay\Payment\Tests\Mocks;

require_once __DIR__.'/../Mocks/Customer.php';
require_once __DIR__.'/../Mocks/Item.php';

use GingerPay\Payment\Tests\Mocks\Customer;
use GingerPay\Payment\Tests\Mocks\Item;

class Order
{
    public function getBaseGrandTotal()
    {
        return 5;
    }

    public function getOrderCurrencyCode()
    {
        return 'EUR';
    }

    public function getStoreId()
    {
        return 1;
    }

    public function getIncrementId()
    {
        return 638;
    }

    public function getPayment()
    {
        return $this;
    }

    public function getAdditionalInformation()
    {
        return ['periodicity' => '+5 minutes'];
    }

    public function getGingerpayTransactionId()
    {
        return '12345-abcdf-zaqws-8659';
    }

    public function getGingerpayNextPaymentDate()
    {
        return '1648740720';
    }

    public function getCustomerEmail()
    {
        return 'Test@ukr.net';
    }

    public function getBillingAddress()
    {
        return new Customer();
    }

    public function get($order, $method)
    {
        return new Customer();
    }

    public function getItems()
    {
        return [new Item()];
    }

    public function getRemoteIp()
    {
        return '172.169.0.1';
    }

    public function getShippingMethod()
    {
        return 'ShippingMethod';
    }

    public function getMethod()
    {
        return 'PaymentMethod';
    }

    public function getGingerpayVaultToken()
    {
        return 'c4da3cdb-aa96-48cc-ba09-22a321c801e6';
    }
}
