<?php

namespace GingerPay\Payment\Tests\ClassSeparators;

require_once  '../Redefiners/Model/PaymentLibraryRedefiner.php';

require_once 'ConfigRepositoryBuilderSeparator.php';
require_once '../Mocks/UrlProvider.php';
require_once '../Mocks/ExtraLines.php';

use GingerPay\Payment\Redefiners\Model\PaymentLibraryRedefiner;
use GingerPay\Payment\Tests\Mocks\ExtraLines;
use GingerPay\Payment\Tests\Mocks\UrlProvider;
use GingerPay\Payment\Tests\ClassSeparators\ConfigRepositoryBuilderSeparator;

class PaymentLibraryRedefinerSeparator extends PaymentLibraryRedefiner
{
    public function __construct()
    {
        //Do not use parent constructor
        $this->urlProvider = new UrlProvider();
        $this->configRepository = new ConfigRepositoryBuilderSeparator();
        $this->extraLines = new ExtraLines();
    }
}