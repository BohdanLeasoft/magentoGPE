<?php

namespace GingerPay\Payment\Model\Builders;

use GingerPay\Payment\Model\OrderCollection\Orders;
use GingerPay\Payment\Model\Api\UrlProvider;
use GingerPay\Payment\Model\Builders\MailTransportBuilder;
use GingerPay\Payment\Model\Builders\LibraryConfigProvider;
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
     * @var LibraryConfigProvider
     */
    protected $libraryConfigProvider;

    /**
     * RecurringHelper constructor.
     *
     * @param Orders                $orders
     * @param UrlProvider           $urlProvider
     * @param MailTransportBuilder  $mailTransport
     * @param LibraryConfigProvider $libraryConfigProvider
     */
    public function __construct(
        Orders                  $orders,
        UrlProvider             $urlProvider,
        MailTransportBuilder    $mailTransport,
        LibraryConfigProvider   $libraryConfigProvider
    ) {
        $this->orders = $orders;
        $this->urlProvider = $urlProvider;
        $this->mailTransport = $mailTransport;
        $this->libraryConfigProvider = $libraryConfigProvider;
    }

    public function getPeriodicityLabel($periodicity)
    {
        $recurringPeriodicities = $this->libraryConfigProvider->getRecurringPeriodicity();
        $periodicityName = null;
        foreach ($recurringPeriodicities as $recurringPeriodicity)
        {
            $periodicityName = array_search($periodicity, $recurringPeriodicity) ? $recurringPeriodicity["name"] : null;
            if ($periodicityName) { return $periodicityName;}
        }
        return $periodicity;
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
        return $this->getRecurringCancelUrlByOrderId($order->getGingerpayTransactionId()).'&get_active_subscriptions=1';
    }

    public function getRecurringCancelUrlByOrderId($orderId)
    {
        return $this->urlProvider->getWebhookUrl().'?order_id='.$orderId;
    }

    public function getRecurringCancelConfirmationUrl($order)
    {
        return $this->getActiveSubscriptionsUrl($order).'&unsubscribe_confirmation=1';
    }

    public function getRecurringCancelLinkMessage($order) : string
    {
        return __('This subscription payment completed. It could be canceled by:').' <a href="'.$this->getRecurringCancelConfirmationUrl($order).'">'.__('Cancel subscription').'</a>
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
                    'cancel_recurring_url' => $this->getRecurringCancelUrlByOrderId($order->getGingerpayTransactionId()),
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
