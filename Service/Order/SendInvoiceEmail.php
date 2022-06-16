<?php
/**
 * All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace GingerPay\Payment\Service\Order;

use GingerPay\Payment\Redefiners\Service\ServiceOrderRedefiner;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use GingerPay\Payment\Api\Config\RepositoryInterface as ConfigRepository;
use Magento\Sales\Model\Order\Payment;

/**
 * Send invoice email service class
 */
class SendInvoiceEmail extends ServiceOrderRedefiner
{
    /**
     * SendInvoiceEmail constructor.
     *
     * @param InvoiceSender $invoiceSender
     * @param OrderCommentHistory $orderCommentHistory
     * @param ConfigRepository $configRepository
     */
    public function __construct(
        InvoiceSender $invoiceSender,
        OrderCommentHistory $orderCommentHistory,
        ConfigRepository $configRepository
    ) {
        $this->invoiceSender = $invoiceSender;
        $this->orderCommentHistory = $orderCommentHistory;
        $this->configRepository = $configRepository;
    }

    /**
     * @param OrderInterface $order
     *
     * @throws LocalizedException
     */
    public function execute(OrderInterface $order)
    {
       $this->sendInvoiceEmail($order);
    }
}
