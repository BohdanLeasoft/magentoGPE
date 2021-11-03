<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace EMSPay\Payment\Block\Info;

use Magento\Payment\Block\Info;
use Magento\Framework\Exception\LocalizedException;

/**
 * Klarna info class
 */
class Klarna extends Info
{

    /**
     * @var mixed
     */
    private $testModus;

    /**
     * @var string
     */
    protected $_template = 'EMSPay_Payment::info/klarna.phtml';

    /**
     * @return mixed
     * @throws LocalizedException
     */
    public function getTestModus()
    {
        if ($this->testModus === null) {
            $testModusFlag = $this->getInfo()->getAdditionalInformation('test_modus');
            $this->testModus = $testModusFlag == 'klarna';
        }
        return $this->testModus;
    }
}
