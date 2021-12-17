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
 * Ideal method class`
 */
class Ideal extends PaymentLibraryRedefiner
{
    /** Payment Code */
    const METHOD_CODE = 'ginger_methods_ideal';

    /**
     * @var string
     */
    public $method_code = self::METHOD_CODE;

    /** Platform Method Code */
    public $platform_code = 'ideal';

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
        if (isset($additionalData['selected_issuer'])) {
            $this->getInfoInstance()->setAdditionalInformation('issuer', $additionalData['selected_issuer']);
        }
        if (isset($additionalData['issuer'])) {
            $this->getInfoInstance()->setAdditionalInformation('issuer', $additionalData['issuer']);
        }
        return $this;
    }
}
