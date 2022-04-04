<?php

namespace GingerPay\Payment\Tests\Mocks;

require_once __DIR__.'/Order.php';

use GingerPay\Payment\Tests\Mocks\Order;

class Quote
{
    public $store;

    public function create()
    {
        return $this;
    }

    public function getBillingAddress()
    {
        return $this;
    }

    public function getShippingAddress()
    {
        return $this;
    }

    public function addData() {  }

    public function setStore($store)
    {
        $this->store = $store;
    }

    public function addProduct($product, $qt) {}

    public function setCurrency() { }

    public function assignCustomer($customer) { }

    public function setRemoteIp($remoteIp) { }

    public function setCollectShippingRates($bool) { return $this; }

    public function collectShippingRates() { return $this; }

    public function setShippingMethod($shippingMethod) { }

    public function setPaymentMethod($paymentMethod) { }

    public function setInventoryProcessed() { return $this; }

    public function save() { }

    public function getPayment() { return $this; }

    public function importData($data) { }

    public function collectTotals() { return $this; }

    public function submit($quote) { return new Order(); }

    public function addTolog(string $type, $data)
    {
    }

}

