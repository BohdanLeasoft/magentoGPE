<?php

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

class Invoice
{
    protected $orderRepository;
    protected $invoiceService;
    protected $transaction;
    protected $invoiceSender;
    protected $invoiceRepository;

    public function __construct(
        Context $context,
        OrderRepositoryInterface $orderRepository,
        InvoiceService $invoiceService,
        InvoiceSender $invoiceSender,
        InvoiceRepositoryInterface $invoiceRepository,
        Transaction $transaction
    )
    {
        $this->orderRepository = $orderRepository;
        $this->invoiceService = $invoiceService;
        $this->transaction = $transaction;
        $this->invoiceSender = $invoiceSender;
        $this->invoiceRepository  = $invoiceRepository;
    }


    public function createInvoice($order, $transaction)
    {
        $invoice = $this->invoiceService->prepareInvoice($order);
        $invoice->setRequestedCaptureCase(OriginalInvoice::CAPTURE_ONLINE);
        $invoice->setState(OriginalInvoice::STATE_PAID);
        $invoice->setBaseGrandTotal($order->getGrandTotal());
        $invoice->setTransactionId($transaction['id']);
        $invoice->register();
        $invoice->getOrder()->setIsInProcess(true);
        $invoice->pay();


        // Create the transaction
        $transactionSave = $this->transaction
            ->addObject($invoice)
            ->addObject($order);
        $transactionSave->save();

        // Update the order
        $order->setTotalPaid($order->getTotalPaid());
        $order->setBaseTotalPaid($order->getBaseTotalPaid());
        $order->save();

        // Save the invoice
        $this->invoiceRepository->save($invoice);
    }
}
