<?php
/**
 * All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace GingerPay\Payment\Service\Transaction;

use GingerPay\Payment\Redefiners\Service\TransactionRedefiner;
use GingerPay\Payment\Service\Transaction\Process\Cancelled;
use GingerPay\Payment\Service\Transaction\Process\Complete;
use GingerPay\Payment\Service\Transaction\Process\Error;
use GingerPay\Payment\Service\Transaction\Process\Expired;
use GingerPay\Payment\Service\Transaction\Process\Processing;
use GingerPay\Payment\Service\Transaction\Process\Unknown;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Process Update service class
 */
class ProcessUpdate extends TransactionRedefiner
{
    /**
     * Process constructor.
     *
     * @param Processing $processing
     * @param Cancelled $cancelled
     * @param Error $error
     * @param Expired $expired
     * @param Complete $complete
     * @param Unknown $unknown
     */
    public function __construct(
        Processing $processing,
        Cancelled $cancelled,
        Error $error,
        Expired $expired,
        Complete $complete,
        Unknown $unknown
    ) {
        $this->processing = $processing;
        $this->cancelled = $cancelled;
        $this->error = $error;
        $this->expired = $expired;
        $this->complete = $complete;
        $this->unknown = $unknown;
    }

    /**
     * @param array $transaction
     * @param OrderInterface $order
     * @param string $type
     *
     * @return array
     * @throws LocalizedException
     */
    public function execute(array $transaction, OrderInterface $order, string $type): array
    {
        return $this->processUpdate($transaction, $order, $type);
    }
}
