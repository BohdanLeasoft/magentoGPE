<?php

namespace GingerPay\Payment\Model\OrderCollection;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Order;

class Orders
{
    protected $orderCollectionFactory;
    protected $orderRepository;
    protected $orderResourceModel;

    public function __construct(
        CollectionFactory $orderCollectionFactory,
        OrderRepositoryInterface $orderRepository,
        Order $orderResourceModel
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderRepository = $orderRepository;
        $this->orderResourceModel = $orderResourceModel;
    }

    public function getOrderRecurringCollection()
    {
        $collection = $this->orderCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter(
                'gingerpay_transaction_id',
                ['neq' => 'NULL']
            )
            ->addFieldToFilter(
                'gingerpay_next_payment_date',
                ['lteq' => strtotime(date('Y-m-d H:i'))]
            )
            ->addFieldToFilter(
                'gingerpay_recurring_periodicity',
                ['neq' => 'NULL']
            );

        return $collection->getItems();
    }

    public function saveOrderVaultToken($order, $vaultToken)
    {
        $order->setGingerpayVaultToken($vaultToken);
        $this->orderResourceModel->save($order);
    }

    public function saveOrderRecurringData($order, array $recurringData)
    {
        // TODO: This feature will be deprecated when the issue with Magento orders creating is resolved. Then remove it
        $order->setGingerpayVaultToken($recurringData['vault_token']);
        $order->setGingerpayNextPaymentDate($recurringData['next_payment_date']);
        $this->orderResourceModel->save($order);
    }

    public function saveInitializeData($order, array $recurringData)
    {
        $order->setGingerpayNextPaymentDate($recurringData['next_payment_date']);
        $order->setGingerpayRecurringPeriodicity($recurringData['recurring_periodicity']);
        $this->orderResourceModel->save($order);
    }

    public function deleteRecurringOrderData($order)
    {
        $order->setGingerpayVaultToken(null);
        $order->setGingerpayNextPaymentDate(null);
        $order->setGingerpayRecurringPeriodicity(null);
        $this->orderResourceModel->save($order);
    }
}
