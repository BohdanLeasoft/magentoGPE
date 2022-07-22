<?php

namespace GingerPay\Payment\Tests\ClassSeparators;

require_once __DIR__.'/../Mocks/AbstractHelper.php';
require_once __DIR__.'/../Mocks/Area.php';
require_once __DIR__.'/../Mocks/Customer.php';
require_once __DIR__.'/../../Model/Builders/HelperDataBuilder.php';
require_once __DIR__.'/../Mocks/StoreManager.php';
require_once __DIR__.'/../Mocks/Quote.php';
require_once __DIR__.'/../Mocks/Item.php';
require_once __DIR__.'/RepositoryInterfaceSeparator.php';


use GingerPay\Payment\Tests\ClassSeparators\ConfigRepositoryBuilderSeparator as ConfigRepository;
use GingerPay\Payment\Model\Builders\HelperDataBuilder;
use GingerPay\Payment\Tests\Mocks\StoreManager;
use GingerPay\Payment\Tests\Mocks\Customer;
use GingerPay\Payment\Tests\Mocks\Quote;
use GingerPay\Payment\Tests\Mocks\Item;

class HelperDataBuilderSeparator extends HelperDataBuilder
{
    public $configRepository;

    public function __construct()
    {

        $this->configRepository = new ConfigRepository();
        $this->storeManager = new StoreManager();
        $this->emulation = $this->storeManager;
        $this->customerFactory = new Customer();
        $this->customerRepository = $this->customerFactory;
        $this->quote = new Quote();
        $this->productRepository = new Item();
        $this->quoteManagement = $this->quote;
    }
}
