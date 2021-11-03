<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace EMSPay\Payment\Service\Transaction\Process;

use Magento\Sales\Api\Data\OrderInterface;
use EMSPay\Payment\Service\Transaction\AbstractTransaction;

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
    public function execute(OrderInterface $order, string $type): array
    {
        return $this->cancelled($order, $type);
    }
}
