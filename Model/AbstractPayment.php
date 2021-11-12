<?php

namespace GingerPay\Payment\Model;

use GingerPay\Payment\Model\PaymentLibrary;
use GingerPay\Payment\Model\Methods\Afterpay;
use GingerPay\Payment\Model\Methods\Klarna;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Model\InfoInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Framework\DataObject;


class AbstractPayment extends PaymentLibrary
{
    /**
     * @var string
     */
    private $paymentName;

    /**
     * @var string
     */
    private $testApiKey;

    /**
     * @param OrderInterface $order
     *
     * @return array
     * @throws \Exception
     * @throws LocalizedException
     */
    public function startTransaction(OrderInterface $order): array
    {
        return parent::prepareTransaction(
            $order,
            $this->platform_code,
            $this->method_code
        );
    }

    /**
     * @param CartInterface|null $quote
     *
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function isAvailable(CartInterface $quote = null): bool
    {
        if ($this->method_code == Afterpay::METHOD_CODE || $this->method_code == Klarna::METHOD_CODE)
        {
            if ($quote == null)
            {
                $quote = $this->checkoutSession->getQuote();
            }

            if (!$this->configRepository->isAfterpayOrKlarnaAllowed($this->method_code, (int)$quote->getStoreId()))
            {
                return false;
            }
        }

        return parent::isAvailable($quote);
    }

    /**
     * @param string $method
     *
     * @return $this->testApiKey
     */

    private function getTestApiKey($method, $testModus)
    {
        switch ($method)
        {
            case Afterpay::METHOD_CODE:
                return $testModus ? $this->configRepository->getAfterpayTestApiKey($storeId, true) : null;
                break;
            case Klarna::METHOD_CODE:
                return $this->configRepository->getKlarnaTestApiKey($storeId, true) ;
                break;
        }

        return $this->testApiKey;
    }

    /**
     * @param string $method
     * @param OrderInterface $order
     *
     * @return $this
     * @throws \Exception
     */
    protected function capturing($method, $order)
    {
        switch ($method)
        {
            case Afterpay::METHOD_CODE:
                $this->paymentName = 'Afterpay';
                break;
            case Klarna::METHOD_CODE:
                $this->paymentName = 'Klarna';
                break;
        }

        $storeId = (int)$order->getStoreId();
        $testModus = $order->getPayment()->getAdditionalInformation();

        if (array_key_exists('test_modus', $testModus)) {
            $testModus = $testModus['test_modus'];
        }

        switch ($method)
        {
            case Afterpay::METHOD_CODE:
                $testApiKey = $testModus ? $this->configRepository->getAfterpayTestApiKey($storeId, true) : null;
                break;
            case Klarna::METHOD_CODE:
                $testApiKey = $this->configRepository->getKlarnaTestApiKey($storeId, true) ;
                break;
        }

        $client = $this->loadGingerClient($storeId, $testApiKey);

        try {
            $ingOrder = $client->getOrder($order->getGingerpayTransactionId());
            $orderId = $ingOrder['id'];
            $transactionId = current($ingOrder['transactions'])['id'];
            $client->captureOrderTransaction($orderId, $transactionId);
            $this->configRepository->addTolog(
                'success',
                $this->paymentName.' payment captured for order: ' . $order->getIncrementId()
            );
        } catch (\Exception $e) {
            $msg = __('Warning: Unable to capture '.$this->paymentName.' Payment for this order, full detail: var/log/ginger-payment.log');
            $this->messageManager->addErrorMessage($msg);
            $this->configRepository->addTolog('error', 'Function: captureOrder: ' . $e->getMessage());
        }

        return $this;
    }

    /**
     * @param string $method
     * @param InfoInterface $payment
     * @param float $amount
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function refundFunctionality($method, InfoInterface $payment, $amount)
    {
        /** @var Creditmemo $creditmemo */
        $creditmemo = $payment->getCreditmemo();

        /** @var Order $order */
        $order = $payment->getOrder();

        if ($creditmemo->getAdjustmentPositive() != 0 || $creditmemo->getAdjustmentNegative() != 0) {
            throw new LocalizedException(__('Api does not accept adjustment fees for refunds using order lines'));
        }

        if ($creditmemo->getShippingAmount() > 0
            && ($creditmemo->getShippingAmount() != $creditmemo->getBaseShippingInclTax())) {
            throw new LocalizedException(__('Api does not accept adjustment fees for shipments using order lines'));
        }

        $storeId = (int)$order->getStoreId();
        $testModus = $order->getPayment()->getAdditionalInformation();
        if (array_key_exists('test_modus', $testModus)) {
            $testModus = $testModus['test_modus'];
        }
        $testApiKey = $this->getTestApiKey($method);
        $transactionId = $order->getGingerpayTransactionId();

        try {
            $addShipping = $creditmemo->getShippingAmount() > 0 ? 1 : 0;
            $client = $this->loadGingerClient($storeId, $testApiKey);
            $client->refundOrder(
                $transactionId,
                [
                    'order_lines' => $this->orderLines->getRefundLines($creditmemo, $addShipping)
                ]
            );
        } catch (\Exception $e) {
            $this->configRepository->addTolog('error', $e->getMessage());
            throw new LocalizedException(__('Error: not possible to create an online refund: %1', $e->getMessage()));
        }

        return $this;
    }

}
