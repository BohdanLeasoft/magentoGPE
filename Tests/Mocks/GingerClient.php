<?php

namespace GingerPay\Payment\Tests\Mocks;

class GingerClient
{
    public function get($storeId)
    {
        return $this;
    }

    public function createOrder($orderData)
    {
        return $orderData;
    }
}
