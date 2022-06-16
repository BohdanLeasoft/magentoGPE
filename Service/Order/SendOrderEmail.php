<?php
/**
 * All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace GingerPay\Payment\Service\Order;

use GingerPay\Payment\Redefiners\Service\ServiceOrderRedefiner;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Sales\Model\OrderRepository;

/**
 * Send Order Email service class
 */
class SendOrderEmail extends ServiceOrderRedefiner
{
    /**
     * SendOrderEmail constructor.
     *
     * @param OrderSender $orderSender
     * @param OrderCommentHistory $orderCommentHistory
     * @parad OrderRepository $orderRepository
     */
    public function __construct(
        OrderSender $orderSender,
        OrderCommentHistory $orderCommentHistory,
        OrderRepository $orderRepository
    ) {
        $this->orderSender = $orderSender;
        $this->orderCommentHistory = $orderCommentHistory;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param OrderInterface $order
     * @throws CouldNotSaveException
     */
    public function execute(OrderInterface $order)
    {
        $this->sendOrderEmail($order);
    }
}
