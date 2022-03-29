<?php

namespace GingerPay\Payment\Model\Builders;

use GingerPay\Payment\Model\OrderCollection\Orders;
use GingerPay\Payment\Model\Api\UrlProvider;
use GingerPay\Payment\Model\Builders\MailTransportBuilder;
use Magento\Sales\Api\Data\OrderInterface;

class RecurringHelper
{
    /**
     * @var Orders
     */
    public $orders;
    /**
     * @var UrlProvider
     */
    public $urlProvider;
    /**
     * @var MailTransportBuilder
     */
    protected $mailTransport;

    /**
     * RecurringHelper constructor.
     *
     * @param Orders                $orders
     * @param UrlProvider           $urlProvider
     * @param MailTransportBuilder  $mailTransport
     */
    public function __construct(
        Orders                  $orders,
        UrlProvider             $urlProvider,
        MailTransportBuilder    $mailTransport
    ) {
        $this->orders = $orders;
        $this->urlProvider = $urlProvider;
        $this->mailTransport = $mailTransport;
    }

    public function initializeRecurringOrder($order, $isRecurringEnable)
    {
        $additionalData = $order->getPayment()->getAdditionalInformation();

        if (!empty($additionalData['periodicity']) && $additionalData['periodicity'] != 'once' && $isRecurringEnable)
        {
            $recurringType = 'first';
            $time = strtotime(date('Y-m-d H:i'));
            $recurringPeriodicity = $additionalData['periodicity'];
            $this->orders->saveInitializeData(
                $order,
                [
                    'next_payment_date' => $this->getNextPaymentDate($time, $recurringPeriodicity),
                    'recurring_periodicity' => $recurringPeriodicity
                ]
            );
        }
    }


    public function getNextPaymentDate($currentDate, $recurringPeriodicity)
    {
        return strtotime($recurringPeriodicity, $currentDate);
    }

    public function isItRecurringTransaction($transaction) : bool
    {
        if (empty(current($transaction['transactions'])["payment_method_details"]["recurring_type"]))
        {
            return false;
        }
        return true;
    }

    public function saveVaultToken($order, $transaction)
    {
        $this->orders->saveOrderVaultToken($order, current($transaction['transactions'])['payment_method_details']['vault_token']);
    }

    public function getRecurringCancelUrl($order)
    {
        return $this->urlProvider->getWebhookUrl().'?order_id='.$order->getGingerpayTransactionId();
    }

    public function getRecurringCancelLinkMessage($order) : string
    {
        return 'This is subscription payment. It could be canceled by: <a href="'.$this->getRecurringCancelUrl($order).'">Cancel subscription</a>';
    }

    public function sendMail(OrderInterface $order, string $type)
    {
        $customer = $order->getBillingAddress();

        $templateVars = [ ];
        $templateMail = 'gingermail_template';

        switch ($type)
        {
            case 'recurring':
                $templateVars = [
                    'cancel_recurring_url' => $this->getRecurringCancelUrl($order),
                    'total_price' =>  $order->getGrandTotal()
                ];
                $templateMail = 'gingermail_template';
                break;
            case 'cancel':
                $templateMail = 'gingermail_subscription_canceled';
                break;
        }

        $this->mailTransport->SendEmail(
            $order->getIncrementId(),
            $customer->getEmail(),
            $customer->getFirstname(),
            $templateMail,
            $templateVars
        );
    }
}
