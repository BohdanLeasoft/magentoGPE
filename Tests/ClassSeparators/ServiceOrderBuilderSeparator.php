<?php

namespace GingerPay\Payment\Tests\ClassSeparators;

require_once __DIR__.'/../../Redefiners/Service/ServiceOrderRedefiner.php';
require_once __DIR__.'/ConfigRepositoryBuilderSeparator.php';
require_once __DIR__.'/../Mocks/ProductsMetadata.php';

use GingerPay\Payment\Redefiners\Service\ServiceOrderRedefiner;
use GingerPay\Payment\Tests\ClassSeparators\ConfigRepositoryBuilderSeparator;
use GingerPay\Payment\Tests\Mocks\ProductsMetadata;

$_SERVER['HTTP_USER_AGENT'] = "USER_AGENT";

class ServiceOrderBuilderSeparator extends ServiceOrderRedefiner
{
    public function __construct()
    {
        $this->configRepository = new ConfigRepositoryBuilderSeparator();
        $this->productMetadata = new ProductsMetadata();
    }
}
