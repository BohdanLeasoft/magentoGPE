<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace GingerPay\Payment\Model\Methods;

use GingerPay\Payment\Redefiners\Model\PaymentLibraryRedefiner;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Model\InfoInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Creditmemo;

/**
 * Afterpay method class
 */
class Afterpay extends PaymentLibraryRedefiner
{
    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = true;

    /** Afterpay terms */
    const TERMS_NL_URL = 'https://www.afterpay.nl/nl/algemeen/betalen-met-afterpay/betalingsvoorwaarden';

    /** Afterpay terms */
    const TERMS_BE_URL = 'https://www.afterpay.be/be/footer/betalen-met-afterpay/betalingsvoorwaarden';

    /** Payment Code */
    const METHOD_CODE = 'ginger_methods_afterpay';

    /**
     * @var string
     */

    public $method_code = self::METHOD_CODE;

    /** Platform Method Code */

    public $platform_code = 'afterpay';

    /**
     * @var string
     */
    protected $_infoBlockType = \GingerPay\Payment\Block\Info\Afterpay::class;

    /**
     * @var string
     */
    protected $_code = self::METHOD_CODE;

    /**
     * Assign date of birth, customer prefixm and issuer data to checkout fields
     *
     * @param DataObject $data
     *
     * @return $this
     * @throws LocalizedException
     */
    public function assignData(DataObject $data)
    {
        parent::assignData($data);

        $additionalData = $data->getAdditionalData();
        if (isset($additionalData['issuer'])) {
            $this->getInfoInstance()->setAdditionalInformation('issuer', $additionalData['issuer']);
        }
        if (isset($additionalData['prefix'])) {
            $this->getInfoInstance()->setAdditionalInformation('prefix', $additionalData['prefix']);
        }
        if (isset($additionalData['dob'])) {
            $this->getInfoInstance()->setAdditionalInformation('dob', $additionalData['dob']);
        }
        return $this;
    }

    /**
     * @param OrderInterface $order
     *
     * @return $this
     */
    public function captureOrder($order)
    {
        return $this->capturing($this->method_code, $order);
    }

    /**
     * Refund specified amount for payment
     *
     * @param \Magento\Framework\DataObject|InfoInterface $payment
     * @param float $amount
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @api
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        if (!$this->canRefund()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('The refund action is not available.'));
        }
        return $this;
    }

//    /**
//     * @param InfoInterface $payment
//     * @param float $amount
//     *
//     * @return $this
//     */
//    public function refund(InfoInterface $payment, $amount)
//    {
//        return $this->refundFunctionality($this->method_code, $payment, $amount);
//    }
}
