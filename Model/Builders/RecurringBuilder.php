<?php

namespace GingerPay\Payment\Model\Builders;

use GingerPay\Payment\Model\Methods\Creditcard;
use GingerPay\Payment\Service\Order\GetOrderByTransaction;
use GingerPay\Payment\Model\Api\GingerClient;
use GingerPay\Payment\Model\Builders\ServiceOrderBuilder;
use GingerPay\Payment\Model\Builders\HelperDataBuilder;
use GingerPay\Payment\Service\Order\OrderDataCollector;
use GingerPay\Payment\Service\Order\OrderLines;
use GingerPay\Payment\Service\Order\CustomerData;
use GingerPay\Payment\Model\OrderCollection\Orders;
use GingerPay\Payment\Service\Transaction\ProcessUpdate as ProcessTransactionUpdate;
use GingerPay\Payment\Service\Transaction\ProcessRequest as ProcessTransactionRequest;
use GingerPay\Payment\Model\Builders\RecurringHelper;
use GingerPay\Payment\Model\Api\UrlProvider;
use GingerPay\Payment\Api\Config\RepositoryInterface as ConfigRepository;

use Magento\Sales\Api\Data\OrderInterface;

class RecurringBuilder
{
    /**
     * @var GetOrderByTransaction
     */
    protected $getOrderByTransaction;
    /**
     * @var GingerClient
     */
    protected $gingerClient;
    /**
     * @var ServiceOrderBuilder
     */
    protected $serviceOrderBuilder;
    /**
     * @var HelperDataBuilder
     */
    protected $helperDataBuilder;
    /**
     * @var OrderDataCollector
     */
    protected $orderDataCollector;
    /**
     * @var OrderLines
     */
    public $orderLines;
    /**
     * @var CustomerData
     */
    public $customerData;
    /**
     * @var Orders
     */
    public $orders;
    /**
     * @var ProcessTransactionUpdate
     */
    public $processUpdate;
    /**
     * @var ProcessTransactionRequest
     */
    public $processRequest;
    /**
     * @var RecurringHelper
     */
    public $recurringHelper;
    /**
     * @var UrlProvider
     */
    protected $urlProvider;
    /**
     * @var UrlProvider
     */
    private $configRepository;

    /**
     * RecurringBuilder constructor.
     *
     * @param GetOrderByTransaction         $getOrderByTransaction
     * @param GingerClient                  $gingerClient
     * @param ServiceOrderBuilder           $serviceOrderBuilder
     * @param OrderDataCollector            $orderDataCollector
     * @param OrderLines                    $orderLines
     * @param CustomerData                  $customerData
     * @param Orders                        $orders
     * @param MailTransportBuilder          $mailTransport
     * @param ProcessTransactionUpdate      $processUpdate
     * @param ProcessTransactionRequest     $processRequest
     * @param RecurringHelper               $recurringHelper
     * @param UrlProvider                   $urlProvider
     * @param ConfigRepository              $configRepository
     */
    public function __construct(
        GetOrderByTransaction       $getOrderByTransaction,
        GingerClient                $gingerClient,
        ServiceOrderBuilder         $serviceOrderBuilder,
        HelperDataBuilder           $helperDataBuilder,
        OrderDataCollector          $orderDataCollector,
        OrderLines                  $orderLines,
        CustomerData                $customerData,
        Orders                      $orders,
        MailTransportBuilder        $mailTransport,
        ProcessTransactionUpdate    $processUpdate,
        ProcessTransactionRequest   $processRequest,
        RecurringHelper             $recurringHelper,
        UrlProvider                 $urlProvider,
        ConfigRepository            $configRepository
    ) {
        $this->getOrderByTransaction =      $getOrderByTransaction;
        $this->gingerClient =               $gingerClient;
        $this->serviceOrderBuilder =        $serviceOrderBuilder;
        $this->helperDataBuilder =          $helperDataBuilder;
        $this->orderDataCollector =         $orderDataCollector;
        $this->orderLines =                 $orderLines;
        $this->customerData =               $customerData;
        $this->orders =                     $orders;
        $this->mailTransport =              $mailTransport;
        $this->processUpdate =              $processUpdate;
        $this->processRequest =             $processRequest;
        $this->recurringHelper =            $recurringHelper;
        $this->urlProvider =                $urlProvider;
        $this->configRepository =           $configRepository;
    }

    public function isOrderForRecurring($order)
    {
        return $order->getGingerpayNextPaymentDate() ? true : false;
    }

    public function getActiveSubscriptionsInfo($transactionId)
    {
        $order = $this->getOrderByTransaction->execute($transactionId);

        if (!$order) {
            return false;
        }

        $activeSubscriptionOrders = $this->orders->getOrderRecurringCustomerCollection($order->getCustomerEmail());

        if (!$activeSubscriptionOrders) {
            return false;
        }

        $orderSubscriptionsInfo = [];

        foreach ($activeSubscriptionOrders as $activeSubscriptionOrder) {
            $orderSubscriptionsInfo[] = [
                'total_amount' => $activeSubscriptionOrder->getBaseGrandTotal(),
                'total_qty_ordered' => $activeSubscriptionOrder->getData('total_qty_ordered'),
                'next_payment_date' =>  date('d-m-Y H:i:s', $activeSubscriptionOrder->getGingerpayNextPaymentDate()),
                'order_periodicity' => $this->recurringHelper->getPeriodicityLabel($activeSubscriptionOrder->getGingerpayRecurringPeriodicity()),
                'cancel_url' => $this->recurringHelper->getRecurringCancelConfirmationUrl($activeSubscriptionOrder),
                'cancel_massage' => __('Cancel subscription')
            ];
        }
        return $orderSubscriptionsInfo;
    }

    public function cancelRecurringOrder($transactionId, $additionalComment = null)
    {
        $order = $this->getOrderByTransaction->execute($transactionId);

        if (!$order)
        {
            return false;
        }
        if (!$this->isOrderForRecurring($order))
        {
            return 'deleted';
        }
        $this->orders->deleteRecurringOrderData($order);
        $this->recurringHelper->sendMail($order, 'cancel', $additionalComment);
        $this->orders->addComment($order, $additionalComment.__('Subscription canceled'));
        return 'success';
    }

    public function prepareGingerOrder($order, $vaultToken)
    {
        $paymentMethodDetails["vault_token"] = $vaultToken;
        if (!$paymentMethodDetails["vault_token"])
        {
            $this->configRepository->addTolog('error', __('Vault token is missing while preparing recurring order'));
            return false;
        }

        $paymentMethodDetails["recurring_type"] = 'recurring';

        $custumerData = $this->customerData->get($order, Creditcard::METHOD_CODE);

        $orderData = $this->orderDataCollector->collectDataForOrder(
            $order,
            Creditcard::PLATFORM_CODE,
            Creditcard::METHOD_CODE,
            $this->urlProvider,
            $this->orderLines,
            $custumerData,
            $paymentMethodDetails
        );
        $storeId = (int)$order->getStoreId();
        $client = $this->gingerClient->get($storeId);
        $transaction = $client->createOrder($orderData);

        return $transaction ?? false;
    }

    public function createOrder($oldOrder)
    {
        $newOrder = null;

        try {
            $result = $this->helperDataBuilder->createOrder($oldOrder);

            if (isset($result['success']))
            {
                $newOrder = $result['order'];
            }
        }
        catch (\Exception $e)
        {
            print $e;
            $this->configRepository->addTolog('error', $e);
            $this->configRepository->addTolog('message', 'The new order could not be created. The old one will be used.');
            $newOrder = $oldOrder;
        }
        return $newOrder;
    }

    public function mainRecurring()
    {
        $this->configRepository->addTolog('running', 'Cron job started');
        $recurringOrders = $this->orders->getOrderRecurringCollection();

        foreach ($recurringOrders as $order)
        {
            $newOrder = $this->createOrder($order);

            if (!$newOrder)
            {
                $this->cancelRecurringOrder($order->getGingerpayTransactionId(),  __('Some of the products are not in the store. '));
                continue;
            }

            $transaction = $this->prepareGingerOrder($newOrder, $order->getGingerpayVaultToken());

            if (!$transaction)
            {
                $this->configRepository->addTolog('recurring_transaction', $transaction);
                continue;
            }


            $this->orders->saveGingerTransactionId($newOrder, $transaction['id']);
            $this->processRequest->execute($newOrder, $transaction);
            $result = $this->processUpdate->execute($transaction, $newOrder, 'success');

            if ($result['success'])
            {
                $recurringData = [
                    'vault_token' => current($transaction['transactions'])['payment_method_details']['vault_token'],
                    'next_payment_date' => $this->recurringHelper->getNextPaymentDate(strtotime(date('Y-m-d H:i')), $order->getGingerpayRecurringPeriodicity()),
                    'recurring_periodicity' => $order->getGingerpayRecurringPeriodicity()
                ];

                $this->orders->deleteRecurringOrderData($order);
                $this->orders->saveOrderRecurringData($newOrder, $recurringData);
                // If transaction was not successful then while next cron job running such order will make another attempt
            }
            $this->configRepository->addTolog('recurring', $result);
            $this->configRepository->addTolog('recurring_transaction', $transaction);
        }
    }



    // TODO: Remove functions below. They were for test

    public function saySomething()
    {
        return "DON'T PANIC";
    }

    public function writeToFile($fileName, $text)
    {
        $file = fopen(__DIR__."/".$fileName.".json", "w+");
        fwrite( $file, $text);
        fclose($file);
    }
}
