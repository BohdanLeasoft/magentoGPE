<?php
/**
 * All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace GingerPay\Payment\Service\Transaction\Process;

use GingerPay\Payment\Service\Transaction\AbstractTransaction;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\Payment;

/**
 * Complete process class
 */
class Complete extends AbstractTransaction
{
    /**
     * @var string
     */
    public $status = 'complete';

    /**
     * Execute "complete" return status
     *
     * @param array $transaction
     * @param OrderInterface $order
     * @param string $type
     *
     * @return array
     * @throws LocalizedException
     */
    public function execute(array $transaction, OrderInterface $order, string $type): array
    {
        return $this->complete($transaction, $order, $type);
    }
}
