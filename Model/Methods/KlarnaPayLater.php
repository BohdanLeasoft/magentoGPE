<?php
/**
 * All rights reserved.
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
 * Klarna method class
 */
class KlarnaPayLater extends PaymentLibraryRedefiner
{

    /** Payment Code */
    const METHOD_CODE = 'ginger_methods_klarnapaylater';

    /**
     * @var string
     */
    public $method_code = self::METHOD_CODE;

    /** Platform Method Code */
    public $platform_code = 'klarna-pay-later';

    /**
     * @var string
     */
    protected $_infoBlockType = \GingerPay\Payment\Block\Info\KlarnaPayLater::class;

    /**
     * @var string
     */
    protected $_code = self::METHOD_CODE;

    /**
     * Assign prefix and date of birth data to checkout fields
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
     * @param InfoInterface $payment
     * @param float $amount
     *
     * @return $this
     */
    public function refund(InfoInterface $payment, $amount)
    {
        return $this->refundFunctionality($this->method_code, $payment, $amount);
    }
}
