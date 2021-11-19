<?php

namespace GingerPay\Payment\Tests;

require_once  __DIR__.'/ClassSeparators/ServiceOrderBuilderSeparator.php';
require_once  __DIR__.'/Mocks/Order.php';
require_once  __DIR__.'/Mocks/UrlProvider.php';
require_once  __DIR__.'/Mocks/OrderLines.php';

use GingerPay\Payment\Tests\Mocks\Order;
use GingerPay\Payment\Tests\Mocks\OrderLines;
use GingerPay\Payment\Tests\Mocks\UrlProvider;
use PHPUnit\Framework\TestCase;
use GingerPay\Payment\Tests\ClassSeparators\ServiceOrderBuilderSeparator;

class CreateOrderTest extends TestCase
{
    private $orderBuilder;
    private $order;
    private $urlProvider;
    private $orderLines;

    public function setUp() : void
    {
        $this->orderBuilder = new ServiceOrderBuilderSeparator();
        $this->order = new Order();
        $this->urlProvider = new UrlProvider();
        $this->orderLines = new OrderLines();
    }

    public function testOrderCreation()
    {
      //  var_dump($this->orderLines->get('dd')); die();
        $arr = $this->orderBuilder->collectDataForOrder($this->order, 'ideal', 'ginger_methods_ideal', $this->urlProvider, $this->orderLines);
        var_dump($arr['order_lines']);
    }
}