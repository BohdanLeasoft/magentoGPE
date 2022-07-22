<?php

namespace GingerPay\Payment\Tests\Mocks;

require_once '../../Redefiners/Service/ServiceOrderRedefiner.php';
require_once '../ClassSeparators/ConfigRepositoryBuilderSeparator.php';
require_once 'ProductsMetadata.php';

use GingerPay\Payment\Redefiners\Service\ServiceOrderRedefiner;
use GingerPay\Payment\Tests\ClassSeparators\ConfigRepositoryBuilderSeparator;
use GingerPay\Payment\Tests\Mocks\ProductsMetadata;

class ExtraLines extends ServiceOrderRedefiner
{
    public function __construct()
    {
        //Do not use parent constructor
        $this->configRepository = new ConfigRepositoryBuilderSeparator();
        $this->productMetadata = new ProductsMetadata();
    }
}