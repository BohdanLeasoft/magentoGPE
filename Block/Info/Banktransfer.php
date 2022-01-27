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
class Banktransfer extends Info
{

    /**
     * @var mixed
     */
    protected $_mailingAddress;

    /**
     * @var string
     */
    protected $_template = 'GingerPay_Payment::info/banktransfer.phtml';

    /**
     * @return mixed
     * @throws LocalizedException
     */
    public function getMailingAddress()
    {
        if ($this->_mailingAddress === null) {
            $this->_mailingAddress = $this->getInfo()->getAdditionalInformation('mailing_address');
        }
        return $this->_mailingAddress;
    }
}
