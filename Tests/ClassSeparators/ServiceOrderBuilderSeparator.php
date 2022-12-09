<?php

namespace GingerPay\Payment\Tests\ClassSeparators;

require_once __DIR__.'/../../Redefiners/Service/ServiceOrderRedefiner.php';
require_once __DIR__.'/ConfigRepositoryBuilderSeparator.php';
require_once __DIR__.'/../Mocks/ProductsMetadata.php';
require_once __DIR__.'/../Mocks/Order.php';

use GingerPay\Payment\Redefiners\Service\ServiceOrderRedefiner;
use GingerPay\Payment\Tests\ClassSeparators\ConfigRepositoryBuilderSeparator;
use GingerPay\Payment\Tests\Mocks\ProductsMetadata;
use GingerPay\Payment\Tests\Mocks\Order;

$_SERVER['HTTP_USER_AGENT'] = "USER_AGENT";

class ServiceOrderBuilderSeparator extends ServiceOrderRedefiner
{
    private $order;

    public function __construct()
    {
        $this->configRepository = new ConfigRepositoryBuilderSeparator();
        $this->productMetadata = new ProductsMetadata();
        $this->order = new Order();
    }

    public function execute($transactionId)
    {
        switch ($transactionId)
        {
            case '12345-abcdf-zaqws-8659': return $this->order; break;
        }
    }
}
