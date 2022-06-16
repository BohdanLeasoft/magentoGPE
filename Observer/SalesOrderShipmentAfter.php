<?php
/**
 * All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace GingerPay\Payment\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use GingerPay\Payment\Model\Methods\KlarnaPayLater;
use GingerPay\Payment\Model\Methods\Afterpay;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * SalesOrderShipmentAfter observer class
 */
class SalesOrderShipmentAfter implements ObserverInterface
{

    /**
     * @var KlarnaPayLater
     */
    private $klarnaModel;

    /**
     * @var Afterpay
     */
    private $afterpayModel;

    /**
     * SalesOrderShipmentAfter constructor.
     *
     * @param KlarnaPayLater $klarnaModel
     * @param Afterpay $afterpayModel
     */
    public function __construct(
        KlarnaPayLater $klarnaModel,
        Afterpay $afterpayModel
    ) {
        $this->klarnaModel = $klarnaModel;
        $this->afterpayModel = $afterpayModel;
    }

    /**
     * @param Observer $observer
     *
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        /** @var OrderInterface $order */
        $order = $observer->getEvent()->getShipment()->getOrder();

        switch ($order->getPayment()->getMethod()) {
            case KlarnaPayLater::METHOD_CODE:
                $this->klarnaModel->captureOrder($order);
                break;
            case Afterpay::METHOD_CODE:
                $this->afterpayModel->captureOrder($order);
                break;
        }
    }
}
