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
 * Klarna info class
 */
class KlarnaPayLater extends Info
{

    /**
     * @var mixed
     */
    private $testModus;

    /**
     * @var string
     */
    protected $_template = 'GingerPay_Payment::info/klarnapaylater.phtml';

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
