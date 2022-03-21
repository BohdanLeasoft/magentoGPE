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
        UrlProvider                 $urlProvider
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
                return 'success';
            }
            else return 'deleted';
        }
        return false;
    }

    public function getVaultToken()
    {
        $jsonContent = file_get_contents(__DIR__."/../Cron/vault_token.json");
        return (json_decode($jsonContent, true));
    }

    public function prepareRecurringPayment()
    {
       // die();// This function is not properly work for now
        $recuringData = $this->getVaultToken();
        $oldOrder = $this->getOrderByTransaction->execute($recuringData["transactionId"]);        //  get order with all customer information by transaction id

        $order = [
            'shipping_method' =>  $oldOrder->getShippingMethod(),
            'currency_id' => 'USD',
            'email' => 'hello@example.com',
            'shipping_address' => ['firstname' => 'John',
                'lastname' => 'Green',
                'street' => 'xxxxxx',
                'city' => 'xxxxxxx',
                'country_id' => 'US',
                'region' => '1',
                'postcode' => '85001',
                'telephone' => '52556542',
                'fax' => '3242322556',
                'save_in_address_book' => 1],
            'items' => [['product_id' => current($oldOrder->getItems())->getProductId(), 'qty' => current($oldOrder->getItems())->getQtyOrdered(), 'price' => current($oldOrder->getItems())->getPrice()]]
        ];
        $this->writeToFile('beforeReturn', 'd');
        return $this->helperDataBuilder->createOrder($order);
    }

    public function prepareGingerOrder(OrderInterface $order)
    {
        $vaultToken = $order->getGingerpayVaultToken();
        if (!$vaultToken)
        {
            return false;
            $this->writeToFile('Bed news', 's');
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

        $this->writeToFile('transaction', $vaultToken.json_encode($transaction));

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
                $result = $this->processUpdate->execute($transaction, $order, 'recurring');
                $this->writeToFile('result', json_encode($result)); //TODO: Remove it before any release
                if ($result['success'])
                {
                    // TODO: Create magento order with next payment date and new vault_token (But for now: Change next payment date and vault_token in original(first) order)
                    $this->writeToFile('payment_method_details', json_encode( current($transaction['transactions'])['payment_method_details']));
                    $recurringData = [
                        'vault_token' => current($transaction['transactions'])['payment_method_details']['vault_token'],
                        'next_payment_date' => $this->recurringHelper->getNextPaymentDate(strtotime(date('Y-m-d H:i')), $order->getGingerpayRecurringPeriodicity())
                    ];

                    $this->orders->saveOrderRecurringData($order, $recurringData);

                    $this->recurringHelper->sendMail($order, 'recurring');
                }
                else
                {
                    // TODO: Log the result and leave the order. It might get success next time
                    $this->writeToFile('NotSuccess'.$order->getGingerpayTransactionId(), json_encode($transaction));
                }
            }
            else
            {
                // TODO: Add normal logging
                $this->writeToFile('RecurringOrderReturnedFalse'.$order->getGingerpayTransactionId(), json_encode($transaction));
            }
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
