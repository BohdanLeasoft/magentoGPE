<?php

namespace GingerPay\Payment\Tests\ClassSeparators;

require_once __DIR__.'/../../Redefiners/Model/ModelBuilderRedefiner.php';
require_once __DIR__.'/../Mocks/ScopeConfig.php';

use GingerPay\Payment\Redefiners\Model\ModelBuilderRedefiner;
use GingerPay\Payment\Tests\Mocks\ScopeConfig;

class ConfigRepositoryBuilderSeparator extends ModelBuilderRedefiner
{
    protected $scopeConfig;

    public function __construct()
    {
        $this->scopeConfig = new ScopeConfig();
    }

    public function getStoreConfig(string $path, int $storeId = 0)
    {
        if ('payment/ginger_methods_ideal/description')
        {
            return "Your order %id% at %name%" ;
        }
        else
        {
            return 'TestStore';
        }
    }
}
