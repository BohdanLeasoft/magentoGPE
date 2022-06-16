<?php
/**
 * All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace GingerPay\Payment\Service\Order;

use GingerPay\Payment\Redefiners\Service\ServiceOrderRedefiner;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\OrderRepository;

/**
 * Update status service class
 */
class UpdateStatus extends ServiceOrderRedefiner
{
    /**
     * UpdateStatus constructor.
     *
     * @param OrderRepository $orderRepository
     */
    public function __construct(
        OrderRepository $orderRepository
    )
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param OrderInterface $order
     * @param string $status
     * @return OrderInterface
     * @throws AlreadyExistsException
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function execute(OrderInterface $order, string $status) : OrderInterface
    {
       return $this->updateStatus($order, $status);
    }
}
