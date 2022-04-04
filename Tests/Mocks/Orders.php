<?php

namespace GingerPay\Payment\Tests\Mocks;

class Orders
{
    public function saveInitializeData($order, $array)
    {
        return true;
    }

    public function deleteRecurringOrderData($order)
    {
        return true;
    }

    public function addComment($order, $str)
    {
        return true;
    }
}
