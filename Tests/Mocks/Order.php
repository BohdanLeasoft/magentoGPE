<?php

namespace GingerPay\Payment\Tests\Mocks;

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
}