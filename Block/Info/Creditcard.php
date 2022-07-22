<?php
/**
 * All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace GingerPay\Payment\Block\Info;

use Magento\Payment\Block\Info;
use Magento\Framework\Exception\LocalizedException;

/**
 * Banktransfer info class
 */
class Creditcard extends Info
{

    /**
     * @var mixed
     */
    protected $_recurringData;

    /**
     * @var string
     */
    protected $_template = 'GingerPay_Payment::info/creditcard.phtml';

    /**
     * Get test mod
     *
     * @return mixed
     * @throws LocalizedException
     */
    public function getRecurringData()
    {
        if ($this->_recurringData === null) {
            $this->_recurringData = $this->getInfo()->getAdditionalInformation('recurring_data');
        }
        return $this->_recurringData;
    }
}
