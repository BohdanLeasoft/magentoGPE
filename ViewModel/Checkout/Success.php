<?php
/**
 * All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace GingerPay\Payment\ViewModel\Checkout;

use GingerPay\Payment\Api\Config\RepositoryInterface as ConfigRepository;
use GingerPay\Payment\Model\PaymentLibrary as PaymentLibraryModel;
use GingerPay\Payment\Model\Methods\Banktransfer;
use GingerPay\Payment\Model\Methods\Ideal;
use GingerPay\Payment\Model\Methods\KlarnaPayNow;
use Magento\Checkout\Model\Session;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Model\Order\Payment;

/**
 * Success view model class
 */
class Success implements ArgumentInterface
{

    const IDEAL_PROCESSING_MESSAGE = "Your order has been received. Thank you for your purchase!
The payment with iDeal is still processing.
You will receive the order email once the payment is successful.";
    const SOFORT_PENDING_MESSAGE = "Your order has been received. Thank you for your purchase!
The payment with iDeal is still processing.
You will receive the order email once the payment is successful.";

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var ConfigRepository
     */
    private $configRepository;

    /**
     * @var PaymentLibraryModel
     */
    private $paymentLibraryModel;

    /**
     * Success constructor.
     *
     * @param Session $checkoutSession
     * @param ConfigRepository $configRepository
     * @param PaymentLibraryModel $paymentLibraryModel
     */
    public function __construct(
        Session $checkoutSession,
        ConfigRepository $configRepository,
        PaymentLibraryModel $paymentLibraryModel
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->configRepository = $configRepository;
        $this->paymentLibraryModel = $paymentLibraryModel;
    }

    /**
     * @return string
     */
    public function getMailingAddress(): string
    {
        $order = $this->checkoutSession->getLastRealOrder();

        /** @var Payment $payment */
        $payment = $order->getPayment();

        if ($payment->getMethod() == Banktransfer::METHOD_CODE) {
            return $payment->getAdditionalInformation('mailing_address');
        }

        return '';
    }

    /**
     * @return string
     */
    public function getThankYouMessage(): string
    {
        $transaction = null;
        $order = $this->checkoutSession->getLastRealOrder();

        /** @var Payment $payment */
        $payment = $order->getPayment();

        $paymentMethod = $payment->getMethod();

        $transactionId = $order->getGingerpayTransactionId();

        if (!$transactionId || $paymentMethod == Banktransfer::METHOD_CODE) {
            return '';
        }

        try {
            $method = $order->getPayment()->getMethodInstance()->getCode();
            $testApiKey = $this->configRepository->getTestKey((string)$method, (int)$order->getStoreId());
            $client = $this->paymentLibraryModel->loadGingerClient((int)$order->getStoreId(), $testApiKey);
            $transaction = $client->getOrder($transactionId);
        } catch (\Exception $e) {
            $this->configRepository->addTolog('error', $e->getMessage());
        }

        if (!$transaction) {
            return '';
        }

        $paymentStatus = $transaction['status'] ?? null;
        if (($paymentStatus == 'processing') && ($paymentMethod == Ideal::METHOD_CODE)) {
            $message = self::IDEAL_PROCESSING_MESSAGE;
            return __($message)->render();
        }
        if (($paymentStatus == 'pending') && ($paymentMethod == KlarnaPayNow::METHOD_CODE)) {
            $message = self::SOFORT_PENDING_MESSAGE;
            return __($message)->render();
        }

        return '';
    }

    /**
     * @return string
     */
    public function getCompanyName(): string
    {
        $storeId = $this->configRepository->getCurrentStoreId();
        return $this->configRepository->getCompanyName((int)$storeId);
    }
}
