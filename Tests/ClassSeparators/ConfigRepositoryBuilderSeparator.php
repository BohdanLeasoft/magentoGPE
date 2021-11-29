<?php

namespace GingerPay\Payment\Tests\ClassSeparators;

require_once __DIR__.'/../../Redefiners/Model/ModelBuilderRedefiner.php';

use GingerPay\Payment\Redefiners\Model\ModelBuilderRedefiner;

class ConfigRepositoryBuilderSeparator extends ModelBuilderRedefiner
{
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

    public function getExtensionVersion(): string
    {
        return '1.1.0';
    }
}
