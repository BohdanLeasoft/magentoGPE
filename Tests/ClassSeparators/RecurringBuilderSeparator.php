<?php

namespace GingerPay\Payment\Tests\ClassSeparators;

require_once __DIR__.'/../Loader/Conector.php';
require_once __DIR__.'/../Mocks/Orders.php';
require_once __DIR__.'/../Mocks/Customer.php';
require_once __DIR__.'/../ClassSeparators/ServiceOrderBuilderSeparator.php';
require_once __DIR__.'/../ClassSeparators/RecurringHelperSeparator.php';
require_once __DIR__.'/../ClassSeparators/HelperDataBuilderSeparator.php';
require_once __DIR__.'/../ClassSeparators/ServiceOrderBuilderSeparator.php';
require_once __DIR__.'/../Mocks/AbstractMethod.php';
require_once __DIR__.'/../../Model/PaymentLibrary.php';
require_once __DIR__.'/../../Model/AbstractPayment.php';
require_once __DIR__.'/../../Redefiners/Model/PaymentLibraryRedefiner.php';
require_once __DIR__.'/../../Model/Methods/Creditcard.php';
require_once __DIR__.'/../Mocks/OrderLines.php';
require_once __DIR__.'/../Mocks/GingerClient.php';

use GingerPay\Payment\Model\Builders\RecurringBuilder;
use GingerPay\Payment\Model\Methods\Creditcard;
use GingerPay\Payment\Redefiners\Model\PaymentLibraryRedefiner;
use GingerPay\Payment\Tests\ClassSeparators\RecurringHelperSeparator;
use GingerPay\Payment\Tests\Mocks\Orders;
use GingerPay\Payment\Tests\Mocks\Customer;
use GingerPay\Payment\Tests\Mocks\UrlProvider;
use GingerPay\Payment\Tests\ClassSeparators\ServiceOrderBuilderSeparator;
use GingerPay\Payment\Tests\ClassSeparators\HelperDataBuilderSeparator;
use GingerPay\Payment\Tests\Mocks\OrderLines;
use GingerPay\Payment\Tests\Mocks\GingerClient;

class RecurringBuilderSeparator extends RecurringBuilder
{
    public $orders;
    public $urlProvider;
    public $getOrderByTransaction;
    public $recurringHelper;
    public $helperDataBuilder;
    public $orderDataCollector;
    public $customerData;
    public $orderLines;
    public $gingerClient;

    public function __construct()
    {
        $this->orders = new Orders();
        $this->urlProvider = new UrlProvider();
        $this->getOrderByTransaction = new ServiceOrderBuilderSeparator();
        $this->recurringHelper = new RecurringHelperSeparator();
        $this->helperDataBuilder = new HelperDataBuilderSeparator();
        $this->orderDataCollector = new ServiceOrderBuilderSeparator();
        $this->customerData = new Customer();
        $this->orderLines = new OrderLines();
        $this->gingerClient = new GingerClient();
    }
}
