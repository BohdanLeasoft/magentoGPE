<?php
/**
 * All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace GingerPay\Payment\Model\Methods;

use GingerPay\Payment\Redefiners\Model\PaymentLibraryRedefiner;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Tikkie method class
 */
class Tikkie extends PaymentLibraryRedefiner
{

    /**
     * Payment Code
     *
     * @var string
     */
    public const METHOD_CODE = 'ginger_methods_tikkie';

    /**
     * @var string
     */
    public $method_code = self::METHOD_CODE;

    /**
     * Platform Method Code
     *
     * @var string
     */
    public $platform_code = 'tikkie-payment-request';

    /**
     * @var string
     */
    protected $_code = self::METHOD_CODE;
}
