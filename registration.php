<?php
/**
 * All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(ComponentRegistrar::MODULE, 'GingerPay_Payment', __DIR__);

if (file_exists(__DIR__ ."/Library/vendor/autoload.php"))
{
    require_once __DIR__ ."/Library/vendor/autoload.php";
}
