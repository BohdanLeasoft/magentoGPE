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
 * Error process class
 */
class Error extends AbstractTransaction
{
    /**
     * @var string
     */
    public $status = 'error';

    /**
     * Execute "error" return status
     *
     * @param OrderInterface $order
     * @param string $type
     *
     * @return array
     */
    public function execute(OrderInterface $order, string $type): array
    {
        return $this->error($order, $type);
    }
}
