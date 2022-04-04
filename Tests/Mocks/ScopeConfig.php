<?php

namespace GingerPay\Payment\Tests\Mocks;

class ScopeConfig
{
    public function getValue($value)
    {
        if($value == 'payment/ginger_general/version')
        {
            return '1.1.0';
        }
    }
}