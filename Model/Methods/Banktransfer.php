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
 * Banktransfer method class
 */
class Banktransfer extends PaymentLibraryRedefiner
{

    /** Payment Code */
    const METHOD_CODE = 'ginger_methods_banktransfer';

    /**
     * @var string
     */
    public $method_code = self::METHOD_CODE;

    /** Platform Method Code */
    public $platform_code = 'bank-transfer';


    /**
     * @var string
     */
    protected $_code = self::METHOD_CODE;

    /**
     * @var string
     */
    protected $_infoBlockType = \GingerPay\Payment\Block\Info\Banktransfer::class;

    /**
     * @return string
     */
    public function getMailingAddress(): string
    {
        if ($accountDetails = $this->configRepository->getAccountDetails()) {
            return implode(
                PHP_EOL,
                [
                    __('Amount: %1', '%AMOUNT%'),
                    __('Reference: %1', '%REFERENCE%'),
                    __('IBAN: %1', $accountDetails['iban']),
                    __('BIC: %1', $accountDetails['bic']),
                    __('Account holder: %1', $accountDetails['holder']),
                    __('City: %1', $accountDetails['city']),
                ]
            );
        }
        return '';
    }
}
