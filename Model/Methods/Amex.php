<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace EMSPay\Payment\Model\Methods;

use EMSPay\Payment\Redefiners\Model\PaymentLibraryRedefiner;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * American Express method class
 */
class Amex extends PaymentLibraryRedefiner
{

    /** Payment Code */
    const METHOD_CODE = 'ginger_methods_amex';

    /**
     * @var string
     */

    public $method_code = self::METHOD_CODE;

    /** Platform Method Code */

    public $platform_code = 'amex';

    /**
     * @var string
     */
    protected $_code = self::METHOD_CODE;
}
