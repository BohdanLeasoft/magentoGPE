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
     * @var RecurringHelper
     */
    protected $recurringHelper;
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
        RecurringHelper             $recurringHelper,
        UrlProvider                 $urlProvider,
        ConfigRepository            $configRepository
    ) {
        $this->getOrderByTransaction = $getOrderByTransaction;
        $this->gingerClient = $gingerClient;
        $this->serviceOrderBuilder = $serviceOrderBuilder;
        $this->helperDataBuilder = $helperDataBuilder;
        $this->orderDataCollector = $orderDataCollector;
        $this->orderLines = $orderLines;
        $this->customerData = $customerData;
        $this->orders = $orders;
        $this->mailTransport = $mailTransport;
        $this->processUpdate = $processUpdate;
        $this->recurringHelper = $recurringHelper;
        $this->urlProvider = $urlProvider;
        $this->configRepository = $configRepository;
    }

    public function isOrderForRecurring($order)
    {
        if ($order->getGingerpayNextPaymentDate())
        {
            return true;
        }
        return false;
    }

    public function cancelRecurringOrder($transactionId)
    {
        $order = $this->getOrderByTransaction->execute($transactionId);

        if ($order)
        {
            if ($this->isOrderForRecurring($order))
            {
                $this->orders->deleteRecurringOrderData($order);
                $this->recurringHelper->sendMail($order, 'cancel');
                $this->orders->addComment($order, 'Subscription canceled');
                return 'success';
            }
            else return 'deleted';
        }
        return false;
    }



    public function prepareRecurringPayment()
    {
        $oldOrder = $this->getOrderByTransaction->execute('29ae45c8-65af-4023-b0f3-7c9d10b4e18e');        //  get order with all customer information by transaction id

        return $this->helperDataBuilder->createOrder($oldOrder);
    }

    public function prepareGingerOrder(OrderInterface $order)
    {
        $vaultToken = $order->getGingerpayVaultToken();
        if (!$vaultToken)
        {
            $this->configRepository->addTolog('error', 'Vault token is missing while preparing recurring order');
            return false;
        }
        $issuer = null;
        $recurringType = 'recurring';

        $custumerData = $this->customerData->get($order, Creditcard::METHOD_CODE);

        $orderData = $this->orderDataCollector->collectDataForOrder(
            $order,
            Creditcard::PLATFORM_CODE,
            Creditcard::METHOD_CODE,
            $this->urlProvider,
            $this->orderLines,
            $custumerData,
            $issuer,
            $recurringType,
            $vaultToken
        );
        $storeId = (int)$order->getStoreId();
        $client = $this->gingerClient->get($storeId);
        $transaction = $client->createOrder($orderData);

        return $transaction ?? false;
    }

    public function mainRecurring()
    {
        $recurringOrders = $this->orders->getOrderRecurringCollection();

        foreach ($recurringOrders as $order)
        {
            $transaction = $this->prepareGingerOrder($order);

            if ($transaction)
            {
                $newOrder = $this->helperDataBuilder->createOrder($order);
                $this->orders->saveGingerTransactionId($newOrder, $transaction['id']);

                $result = $this->processUpdate->execute($transaction, $newOrder, 'success');

                if ($result['success'])
                {
                    $recurringData = [
                        'vault_token' => current($transaction['transactions'])['payment_method_details']['vault_token'],
                        'next_payment_date' => $this->recurringHelper->getNextPaymentDate(strtotime(date('Y-m-d H:i')), $order->getGingerpayRecurringPeriodicity()),
                        'recurring_periodicity' => $order->getGingerpayRecurringPeriodicity()
                    ];

                    $this->orders->saveOrderRecurringData($newOrder, $recurringData);
                    $this->orders->deleteRecurringOrderData($order);

                }
                $this->configRepository->addTolog('recurring', $result);
            }
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
