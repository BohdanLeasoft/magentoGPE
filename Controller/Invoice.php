<?php
/**
 * All rights reserved.
 * See COPYING.txt for license details.
 */
namespace GingerPay\Payment\Controller;

use Magento\Framework\App\Action\Context;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Framework\DB\Transaction;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Invoice as OriginalInvoice;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment\Transaction as TransactionStatus;

/**
 * Class for handling invoice creating
 */
class Invoice
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;
    /**
     * @var InvoiceService
     */
    protected $invoiceService;
    /**
     * @var Transaction
     */
    protected $transaction;
    /**
     * @var InvoiceSender
     */
    protected $invoiceSender;
    /**
     * @var InvoiceRepositoryInterface
     */
    protected $invoiceRepository;

    /**
     * Invoice constructor
     *
     * @param Context $context
     * @param OrderRepositoryInterface $orderRepository
     * @param InvoiceService $invoiceService
     * @param InvoiceSender $invoiceSender
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param Transaction $transaction
     */
    public function __construct(
        Context $context,
        OrderRepositoryInterface $orderRepository,
        InvoiceService $invoiceService,
        InvoiceSender $invoiceSender,
        InvoiceRepositoryInterface $invoiceRepository,
        Transaction $transaction
    ) {
        $this->orderRepository = $orderRepository;
        $this->invoiceService = $invoiceService;
        $this->transaction = $transaction;
        $this->invoiceSender = $invoiceSender;
        $this->invoiceRepository  = $invoiceRepository;
    }

    /**
     * Create invoice function
     *
     * @param Order $order
     * @param Transaction $transaction
     */
    public function createInvoice($order, $transaction)
    {
        //$order = $this->orderRepository->get($order->getIncrementId());
        $invoice = $this->invoiceService->prepareInvoice($order);
        $invoice->setRequestedCaptureCase(OriginalInvoice::CAPTURE_ONLINE);
        $invoice->setState(OriginalInvoice::STATE_PAID);
        $invoice->setBaseGrandTotal($order->getGrandTotal());
        $invoice->setTransactionId($transaction['id']);
        $invoice->register();
        $invoice->getOrder()->setIsInProcess(true);
        $invoice->pay();

        $transactionSave = $this->transaction
            ->addObject($invoice)
            ->addObject($order);
        $transactionSave->save();

        $order->setTotalPaid($order->getTotalPaid());
        $order->setBaseTotalPaid($order->getBaseTotalPaid());
        $order->save();

        $this->invoiceRepository->save($invoice);
    }
}
