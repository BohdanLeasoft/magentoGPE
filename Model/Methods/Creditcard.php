<?php
/**
 * All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace GingerPay\Payment\Model\Methods;

use GingerPay\Payment\Redefiners\Model\PaymentLibraryRedefiner;
use Magento\Framework\DataObject;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Creditcard method class
 */
class Creditcard extends PaymentLibraryRedefiner
{
    /**
     * Payment Code
     *
     * @var string
     */
    public const METHOD_CODE = 'ginger_methods_creditcard';

    /**
     * @var string
     */
    public $method_code = self::METHOD_CODE;

    /**
     * Platform Code
     *
     * @var string
     */
    public const PLATFORM_CODE = 'credit-card';
    /**
     * Platform Method Code
     *
     * @var string
     */
    public $platform_code = self::PLATFORM_CODE;

    /**
     * @var string
     */
    protected $_code = self::METHOD_CODE;
    /**
     * Assign issuer data to checkout fields
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
        if (isset($additionalData['periodicity'])) {
            $this->getInfoInstance()->setAdditionalInformation('periodicity', $additionalData['periodicity']);
        }
        return $this;
    }

    /**
     * @var string
     */
    protected $_infoBlockType = \GingerPay\Payment\Block\Info\Creditcard::class;

    /**
     * Get mailing address
     *
     * @return string
     */
    public function getRecurringData(): string
    {
        return 'RecurringData';
    }
}
