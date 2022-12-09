<?php

namespace GingerPay\Payment\Tests\ClassSeparators;

require_once __DIR__.'/../Loader/Conector.php';
require_once __DIR__.'/../Mocks/Orders.php';
require_once __DIR__.'/../Mocks/MailTransport.php';

use GingerPay\Payment\Model\Builders\RecurringHelper;
use GingerPay\Payment\Tests\Mocks\MailTransport;
use GingerPay\Payment\Tests\Mocks\Orders;
use GingerPay\Payment\Tests\Mocks\UrlProvider;

class RecurringHelperSeparator extends RecurringHelper
{
    public $orders;
    public $urlProvider;
    public $mailTransport;

    public function __construct()
    {
        $this->orders = new Orders();
        $this->urlProvider = new UrlProvider();
        $this->mailTransport = new MailTransport();
    }

}
