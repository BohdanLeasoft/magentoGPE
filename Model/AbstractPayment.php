<?php

namespace GingerPay\Payment\Model;

use GingerPay\Payment\Model\PaymentLibrary;
use GingerPay\Payment\Model\Methods\Afterpay;
use GingerPay\Payment\Model\Methods\KlarnaPayLater;
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
        if (in_array($this->method_code, [Afterpay::METHOD_CODE, KlarnaPayLater::METHOD_CODE]))
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

    private function getTestApiKey($method, $testModus, $storeId)
    {
        switch ($method)
        {
            case Afterpay::METHOD_CODE:
                return $testModus ? $this->configRepository->getAfterpayTestApiKey($storeId, true) : null;
                break;
            case KlarnaPayLater::METHOD_CODE:
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
            case KlarnaPayLater::METHOD_CODE:
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
            case KlarnaPayLater::METHOD_CODE:
                $testApiKey = $this->configRepository->getKlarnaTestApiKey($storeId, true) ;
                break;
        }

        $client = $this->loadGingerClient($storeId, $testApiKey);

        try {
            $gingerOrder = $client->getOrder($order->getGingerpayTransactionId());

            $orderId = $gingerOrder['id'];
            $transactionId = current($gingerOrder['transactions'])['id'];
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

        $testApiKey = $this->getTestApiKey($method, $testModus, $storeId);
        $transactionId = $order->getGingerpayTransactionId();

        try {
            $addShipping = $creditmemo->getShippingAmount() > 0 ? 1 : 0;
            $client = $this->loadGingerClient($storeId, $testApiKey);

            $gingerOrder = $client->refundOrder(
                $transactionId,
                [
                    'amount' => $this->configRepository->getAmountInCents((float)$amount),
                    'currency' => $order->getOrderCurrencyCode(),
                    'order_lines' => $this->orderLines->getRefundLines($creditmemo, $addShipping)
                ]);
        } catch (\Exception $e) {
            $errorMsg = __('Error: not possible to create an online refund: %1', $e->getMessage());
            $this->configRepository->addTolog('error', $errorMsg);
            throw new LocalizedException($errorMsg);
        }

        return $this;
    }

}
