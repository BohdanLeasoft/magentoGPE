<?php
/**
 * All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace GingerPay\Payment\Service\Transaction\Process;

use GingerPay\Payment\Model\Methods\Banktransfer;
use GingerPay\Payment\Service\Transaction\AbstractTransaction;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\Payment\Transaction;

/**
 * Process process class
 */
class Processing extends AbstractTransaction
{
    /**
     * @var string
     */
    public $status = 'processing';

    /**
     * Execute "processing" return status
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
        return $this->processing($transaction, $order, $type);
    }
}
