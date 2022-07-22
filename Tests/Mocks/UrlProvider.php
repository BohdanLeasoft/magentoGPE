<?php

namespace GingerPay\Payment\Tests\Mocks;

class UrlProvider
{
    public function getReturnUrl()
    {
        return 'https://magento2.test/ginger/checkout/process/';
    }

    public function getWebhookUrl()
    {
        return 'https://magento2.test/ginger/checkout/webhook/';
    }
}

