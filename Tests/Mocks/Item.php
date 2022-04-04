<?php

namespace GingerPay\Payment\Tests\Mocks;

class Item
{
    public function getProductId()
    {
        return 42;
    }

    public function getById($id)
    {
        return $this;
    }

    public function getQtyOrdered()
    {
        return 3;
    }
}
