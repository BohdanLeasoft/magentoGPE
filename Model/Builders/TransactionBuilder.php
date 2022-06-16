<?php

namespace GingerPay\Payment\Model\Builders;

use GingerPay\Payment\Controller\Invoice;
use GingerPay\Payment\Model\Api\UrlProvider;
use GingerPay\Payment\Model\Methods\Afterpay;
use GingerPay\Payment\Model\Methods\KlarnaPayLater;
use GingerPay\Payment\Model\Methods\Banktransfer;
use GingerPay\Payment\Service\Order\Cancel as CancelOrder;
use GingerPay\Payment\Service\Order\SendInvoiceEmail;
use GingerPay\Payment\Service\Order\SendOrderEmail;
use GingerPay\Payment\Service\Order\UpdateStatus;
use GingerPay\Payment\Service\Transaction\Process\Cancelled;
use GingerPay\Payment\Service\Transaction\Process\Complete;
use GingerPay\Payment\Service\Transaction\Process\Error;
use GingerPay\Payment\Service\Transaction\Process\Expired;
use GingerPay\Payment\Service\Transaction\Process\Processing;
use GingerPay\Payment\Service\Transaction\Process\Unknown;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order\Payment\Transaction;


class TransactionBuilder
{
    /**
     * @var ConfigRepository
     */
    public $configRepository;

    /**
     * @var SendOrderEmail
     */
    public $sendOrderEmail;

    /**
     * @var SendInvoiceEmail
     */
    public $sendInvoiceEmail;

    /**
     * @var OrderRepository
     */
    public $orderRepository;

    /**
     * @var CancelOrder
     */
    public $cancelOrder;

    /**
     * @var UpdateStatus
     */
    public $updateStatus;

    /**
     * @var UrlProvider
     */
    public $urlProvider;

    /**
     * @var CheckoutSession
     */
    public $checkoutSession;
    /**
     * @var Processing
     */
    protected $processing;

    /**
     * @var Cancelled
     */
    protected $cancelled;

    /**
     * @var Error
     */
    protected $error;

    /**
     * @var Expired
     */
    protected $expired;

    /**
     * @var Complete
     */
    protected $complete;

    /**
     * @var Unknown
     */
    protected $unknown;

    /**
     * @var Invoice
     */
    protected $invoice;

    /**
     * @param OrderInterface $order
     * @param array $transaction
     * @param string $type
     *
     * @return OrderInterface
     *
     * @throws AlreadyExistsException
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function updateOrderTransaction(
        OrderInterface $order,
        array $transaction,
        string $type
    ): OrderInterface {
        /** @var Payment $payment */
        $payment = $order->getPayment();

        $payment->setTransactionId($transaction['id']);
        $payment->isSameCurrency();
        $payment->setIsTransactionClosed(false);
        $payment->addTransaction($type);

        $this->orderRepository->save($order);

        return $order;
    }

    /**
     * @param OrderInterface $order
     *
     * @return mixed
     * @throws LocalizedException
     */
    public function getMethodFromOrder(OrderInterface $order)
    {
        /** @var Payment $payment */
        $payment = $order->getPayment();

        return $payment->getMethodInstance()->getCode();
    }

    /**
     * @param OrderInterface $order
     * @param string $method
     * @param array $transaction
     *
     * @return void
     * @throws LocalizedException
     */
    public function updateMailingAddress(OrderInterface $order, $method, $transaction)
    {
        if ($method !== Banktransfer::METHOD_CODE) {
            return;
        }

        /** @var Payment $payment */
        $payment = $order->getPayment();

        /** @var Banktransfer $methodInstance */
        $methodInstance = $payment->getMethodInstance();
        $mailingAddress = $methodInstance->getMailingAddress();
        $grandTotal = $this->configRepository->formatPrice($transaction['amount'] / 100);
        $reference = current($transaction['transactions'])['payment_method_details']['reference'];
        $mailingAddress = str_replace('%AMOUNT%', $grandTotal, $mailingAddress);
        $mailingAddress = str_replace('%REFERENCE%', $reference, $mailingAddress);
        $mailingAddress = str_replace('\n', PHP_EOL, $mailingAddress);
        $payment->setAdditionalInformation('mailing_address', $mailingAddress);
    }

    /**
     * @param OrderInterface $order
     * @param array $transaction
     *
     * @return OrderInterface
     *
     * @throws AlreadyExistsException
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function captureOrderTransaction(
        OrderInterface $order,
        array $transaction
    ): OrderInterface {

        /** @var Payment $payment */
        $payment = $order->getPayment();
        if ($order->hasInvoices() || $payment->getAmountPaid()) {
            $errorMsg = __('Order %1 already invoiced/paid, no need for capture', $order->getIncrementId());
            $this->configRepository->addTolog('error', $errorMsg);
            return $order;
        }

        $payment->setTransactionId($transaction['id']);
        $payment->isSameCurrency();
        $payment->setIsTransactionClosed(true);

        $amount = $transaction['amount'] / 100;
        $payment->registerCaptureNotification($amount, true);

        if ($order->getIsVirtual()) {
            $order->setState(Order::STATE_COMPLETE);
        } else {
            $order->setState(Order::STATE_PROCESSING);
        }

        $this->orderRepository->save($order);

        return $order;
    }

    /**
     * @param OrderInterface $order
     * @param null|array $transaction
     * @param null|string $testModus
     *
     * @return array
     * @throws LocalizedException
     */
    public function processRequest(OrderInterface $order, $transaction = null, $testModus = null): array
    {
        $method = $order->getPayment()->getMethod();
        $this->updateMailingAddress($order, $method, $transaction);
        $this->configRepository->addTolog('transaction', $transaction);
        $transactionId = !empty($transaction['id']) ? $transaction['id'] : null;

        if ($transactionId && !$this->configRepository->getError($transaction)) {
            $method = $this->getMethodFromOrder($order);
            $message = __('EMS Order ID: %1', $transactionId);
            $status = $this->configRepository->getStatusPending($method, (int)$order->getStoreId());
            $order->addStatusToHistory($status, $message, false);
            $order->setGingerpayTransactionId($transactionId);

            if ($testModus !== null) {
                /** @var Payment $payment */
                $payment = $order->getPayment();
                $payment->setAdditionalInformation('test_modus', $testModus);
            }

            $this->orderRepository->save($order);
        }

        if ($error = $this->configRepository->getError($transaction)) {
            return ['error' => $error];
        }

        if ($method == Banktransfer::METHOD_CODE) {
            return ['redirect' => $this->urlProvider->getSuccessProcessUrl((string)$transactionId)];
        }

        if ($transaction !== null && !empty(current($transaction['transactions'])['payment_url'])) {
            return ['redirect' => current($transaction['transactions'])['payment_url']];
        }

        return ['error' => __('Error, could not fetch redirect url')];
    }


    /**
     * @param array $transaction
     * @param OrderInterface $order
     * @param string $type
     *
     * @return array
     * @throws LocalizedException
     */
    public function processUpdate(array $transaction, OrderInterface $order, string $type): array
    {
        $status = !empty($transaction['status']) ? $transaction['status'] : '';
        $customerMessage = !empty(current($transaction['transactions'])['customer_message']) ? current($transaction['transactions'])['customer_message'] : null;

        switch ($status) {
            case 'error':
                return $this->error->execute($order, $type, $customerMessage);
            case 'expired':
                return $this->expired->execute($order, $type, $customerMessage);
            case 'cancelled':
                return $this->cancelled->execute($order, $type, $customerMessage);
            case 'completed':
                return $this->complete->execute($transaction, $order, $type);
            case 'processing':
                return $this->processing->execute($transaction, $order, $type);
            default:
                return $this->unknown->execute($order, $type, $status);
        }
    }

    /**
     * Execute "cancelled" return status
     *
     * @param OrderInterface $order
     * @param string $type
     *
     * @return array
     */
    public function cancelled(OrderInterface $order, string $type, $customerMessage = ''): array
    {
        if ($type == 'webhook') {
            $this->cancelOrder->execute($order);
        }

        $result = [
            'success' => false,
            'status' => $this->status,
            'order_id' => $order->getEntityId(),
            'type' => $type,
        ];

        if ($customerMessage)        {
            $result += [ 'cart_msg' => __($customerMessage), ];
        } else {
            $result += [ 'cart_msg' => __('There was a problem processing your payment because it has been cancelled. Please try again.'), ];
        }

        $this->configRepository->addTolog('success', $result);
        return $result;
    }

    /**
     * Execute "complete" return status
     *
     * @param array $transaction
     * @param OrderInterface $order
     * @param string $type
     *
     * @return array
     * @throws LocalizedException
     */
    public function complete(array $transaction, OrderInterface $order, string $type): array
    {
        /** @var Payment $payment */
        $payment = $order->getPayment();
        if (!$payment->getIsTransactionClosed() && $type == 'webhook') {
            $order = $this->captureOrderTransaction($order, $transaction);
            $this->sendInvoiceEmail->execute($order);

            $method = $this->getMethodFromOrder($order);
            $status = $this->configRepository->getStatusProcessing($method, (int)$order->getStoreId());

            $this->updateStatus->execute($order, $status);
        }

        if ($type != 'webhook') {
            $this->sendOrderEmail->execute($order);
            $this->sendInvoiceEmail->execute($order);
        }

        if ($type == 'success') {
            $this->checkoutSession->setLastQuoteId($order->getQuoteId())
                ->setLastSuccessQuoteId($order->getQuoteId())
                ->setLastRealOrderId($order->getIncrementId())
                ->setLastOrderId($order->getEntityId());
            if (!$order->hasInvoices()) {
                $this->invoice->createInvoice($order, $transaction);
            }
        }

        $result = [
            'success' => true,
            'status' => $this->status,
            'order_id' => $order->getEntityId(),
            'type' => $type
        ];

        $this->configRepository->addTolog('success', $result);
        return $result;
    }

    /**
     * Execute "error" return status
     *
     * @param OrderInterface $order
     * @param string $type
     * @param string $customerMessage
     *
     * @return array
     */
    public function error(OrderInterface $order, string $type, $customerMessage = ''): array
    {
        if ($type == 'webhook') {
            $this->cancelOrder->execute($order);
        }

        $result = [
            'success' => false,
            'status' => $this->status,
            'order_id' => $order->getEntityId(),
            'type' => $type,

        ];

        if ($customerMessage)
        {
            $result += [ 'cart_msg' => __($customerMessage), ];
        }
        else
        {
            $result += [ 'cart_msg' => __('There was a problem processing your payment because it failed. Please try again.'), ];
        }
        $this->configRepository->addTolog('success', $result);

        return $result;
    }

    /**
     * Execute "expired" return status
     *
     * @param OrderInterface $order
     * @param string $type
     *
     * @return array
     */
    public function expired(OrderInterface $order, string $type, $customerMessage = ''): array
    {
        if ($type == 'webhook') {
            $this->cancelOrder->execute($order);
        }

        $result = [
            'success' => false,
            'status' => $this->status,
            'order_id' => $order->getEntityId(),
            'type' => $type,
        ];
        if ($customerMessage)
        {
            $result += [ 'cart_msg' => __($customerMessage), ];
        }
        else
        {
            $result += [ 'cart_msg' => __('There was a problem processing your payment because it expired. Please try again.'), ];
        }

        $this->configRepository->addTolog('success', $result);
        return $result;
    }

    /**
     * Execute "processing" return status
     *
     * @param array $transaction
     * @param OrderInterface $order
     * @param string $type
     *
     * @return array
     * @throws LocalizedException
     */
    public function processing(array $transaction, OrderInterface $order, string $type): array
    {
        $method = $order->getPayment()->getMethodInstance()->getCode();

        if ($type == 'webhook') {
            $order = $this->updateOrderTransaction($order, $transaction, Transaction::TYPE_AUTH);
        }

        if ($type != 'webhook') {
            if ($method == Banktransfer::METHOD_CODE) {
                $this->sendOrderEmail->execute($order);
            }
        }

        $result = [
            'success' => true,
            'status' => $this->status,
            'order_id' => $order->getEntityId(),
            'type' => $type
        ];

        $this->configRepository->addTolog('success', $result);
        return $result;
    }

    /**
     * Execute unkown return status
     *
     * @param OrderInterface $order
     * @param string $type
     * @param string $status
     *
     * @return array
     */
    public function unknown(OrderInterface $order, string $type, string $status): array
    {
        $result = [
            'success' => false,
            'status' => $status,
            'order_id' => $order->getEntityId(),
            'type' => $type
        ];
        $this->configRepository->addTolog('success', $result);
        return $result;
    }


}

