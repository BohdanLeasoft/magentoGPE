<?php
/**
 * All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace GingerPay\Payment\Service\Transaction\Process;

use Magento\Sales\Api\Data\OrderInterface;
use GingerPay\Payment\Service\Transaction\AbstractTransaction;

/**
 * Cancelled process class
 */
class Cancelled extends AbstractTransaction
{

    /**
     * @var string
     */
    public $status = 'cancelled';

    /**
     * Execute "cancelled" return status
     *
     * @param OrderInterface $order
     * @param string $type
     *
     * @return array
     */
    public function execute(OrderInterface $order, string $type, $customerMessage = ''): array
    {
        return $this->cancelled($order, $type, $customerMessage);
    }
}
