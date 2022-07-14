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

            return true;
        }
        return false;
    }


    public function getNextPaymentDate($currentDate, $recurringPeriodicity)
    {
        return strtotime($recurringPeriodicity, $currentDate);
    }

    public function isItRecurringTransaction($transaction) : bool
    {
        if (empty(current($transaction['transactions'])['payment_method_details']['recurring_type']))
        {
            return false;
        }
        return true;
    }

    public function getRecurringType($transaction)
    {
        if (empty(current($transaction['transactions'])['payment_method_details']['recurring_type'])) {
            return null;
        }
        return current($transaction['transactions'])['payment_method_details']['recurring_type'];
    }

    public function saveVaultToken($order, $transaction)
    {
        $this->orders->saveOrderVaultToken($order, current($transaction['transactions'])['payment_method_details']['vault_token']);
    }

    public function getActiveSubscriptionsUrl($order)
    {
        return $this->getRecurringCancelUrl($order).'&get_active_subscriptions=1';
    }

    public function getRecurringCancelUrl($order)
    {
        return $this->urlProvider->getWebhookUrl().'?order_id='.$order->getGingerpayTransactionId();
    }

    public function getRecurringCancelLinkMessage($order) : string
    {
        return __('This subscription payment completed. It could be canceled by:').' <a href="'.$this->getRecurringCancelUrl($order).'">'.__('Cancel subscription').'</a>
                <br><a href="'.$this->getActiveSubscriptionsUrl($order).'">'.__('Here you can see all your active subscriptions:').'</a>';
    }

    public function sendMail($order, string $type, $additionalComment = null)
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
                $templateVars = array_filter([
                    'additional_comment' => $additionalComment
                ]);
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
