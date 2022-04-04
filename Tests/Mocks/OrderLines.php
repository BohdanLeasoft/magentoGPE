<?php

namespace GingerPay\Payment\Tests\Mocks;

class OrderLines
{
    public function get($order)
    {
        return array([
            '0' => [
                "type" => "physical",
                "url" => "https://magento2.test/newsuperproduct.html",
                "name" => "NewSuperProduct",
                "amount" => "500",
                "currency" => "EUR",
                "quantity" => 1,
                "vat_percentage" => 0,
                "merchant_order_line_id" => 638
            ]
        ]);
    }
}
