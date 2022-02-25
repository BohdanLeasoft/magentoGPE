<?php
/**
 * All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace GingerPay\Payment\Service\Transaction;

use GingerPay\Payment\Redefiners\Service\TransactionRedefiner;
use GingerPay\Payment\Api\Config\RepositoryInterface as ConfigRepository;
use GingerPay\Payment\Model\Api\UrlProvider;
use GingerPay\Payment\Controller\Invoice;
use GingerPay\Payment\Model\Methods\Banktransfer;
use GingerPay\Payment\Service\Order\Cancel as CancelOrder;
use GingerPay\Payment\Service\Order\SendInvoiceEmail;
use GingerPay\Payment\Service\Order\SendOrderEmail;
use GingerPay\Payment\Service\Order\UpdateStatus;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\OrderRepository;

/**
 * Transaction Abstract class
 */
class AbstractTransaction extends TransactionRedefiner
{
    /**
     * AbstractTransaction constructor.
     * @param ConfigRepository $configRepository
     * @param OrderRepository $orderRepository
     * @param CancelOrder $cancelOrder
     * @param SendOrderEmail $sendOrderEmail
     * @param SendInvoiceEmail $sendInvoiceEmail
     * @param UpdateStatus $updateStatus
     * @param UrlProvider $urlProvider
     * @param CheckoutSession $checkoutSession
     * @param Invoice $invoice
     */
    public function __construct(
        ConfigRepository $configRepository,
        OrderRepository $orderRepository,
        CancelOrder $cancelOrder,
        SendOrderEmail $sendOrderEmail,
        SendInvoiceEmail $sendInvoiceEmail,
        UpdateStatus $updateStatus,
        UrlProvider $urlProvider,
        CheckoutSession $checkoutSession,
        Invoice $invoice
    ) {
        $this->configRepository = $configRepository;
        $this->orderRepository = $orderRepository;
        $this->cancelOrder = $cancelOrder;
        $this->sendOrderEmail = $sendOrderEmail;
        $this->sendInvoiceEmail = $sendInvoiceEmail;
        $this->updateStatus = $updateStatus;
        $this->urlProvider = $urlProvider;
        $this->checkoutSession = $checkoutSession;
        $this->invoice = $invoice;
    }
}
