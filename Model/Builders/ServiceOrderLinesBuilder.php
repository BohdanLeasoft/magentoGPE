<?php

namespace GingerPay\Payment\Model\Builders;

use Magento\Framework\UrlInterface;
use Magento\Sales\Api\Data\CreditmemoItemInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;

class ServiceOrderLinesBuilder
{
    /**
     * @var ConfigRepository
     */
    protected $configRepository;

    /**
     * @param OrderInterface $order
     *
     * @return array
     */
    public function get(OrderInterface $order)
    {
        $orderLines = [];
        foreach ($order->getItems() as $item)
        {
            $orderLines[] = [
                'type' => 'physical',
                'url' => $this->getItemUrl($item),
                'name' => $this->getItemName($item),
                'amount' => $this->getItemAmount($item),
                'currency' => $order->getOrderCurrencyCode(),
                'quantity' => $item->getQtyOrdered() ? round($item->getQtyOrdered()) : 1,
                'vat_percentage' => $this->configRepository->getAmountInCents((float)$item->getTaxPercent()),
                'merchant_order_line_id' => $item->getItemId()
            ];
        }

        if ($order->getShippingAmount() > 0) {
            $orderLines[] = $this->getShippingOrderLine($order);
        }

        $this->configRepository->addTolog('orderLines', $orderLines);

        return $orderLines;
    }

    /**
     * @param OrderItemInterface $item
     *
     * @return mixed
     */
    protected function getItemUrl(OrderItemInterface $item)
    {
        return $item->getProduct()->getProductUrl();
    }

    /**
     * @param OrderItemInterface $item
     *
     * @return null|string
     */
    protected function getItemName(OrderItemInterface $item)
    {
        return preg_replace("/[^A-Za-z0-9 ]/", "", $item->getName());
    }

    /**
     * @param OrderItemInterface $item
     *
     * @return int
     */
    protected function getItemAmount(OrderItemInterface $item)
    {
        return $this->configRepository->getAmountInCents((
                $item->getRowTotal()
                - $item->getDiscountAmount()
                + $item->getTaxAmount()
                + $item->getDiscountTaxCompensationAmount()
            )
            / $item->getQtyOrdered());
    }

    /**
     * @param OrderItemInterface $item
     *
     * @return string
     */
    protected function getImageUrl(OrderItemInterface $item)
    {
        $imagePath = $this->configRepository->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        return $imagePath . 'catalog/product' . $item->getProduct()->getImage();
    }

    /**
     * @param OrderInterface $order
     *
     * @return array
     */
    protected function getShippingOrderLine(OrderInterface $order)
    {
        return [
            'type' => 'shipping_fee',
            'name' => $this->getShippingName($order),
            'amount' => $this->getShippingAmount($order),
            'currency' => $order->getOrderCurrencyCode(),
            'vat_percentage' => $this->getShippingVatPercentage($order),
            'quantity' => 1,
            'merchant_order_line_id' => 'shipping'
        ];
    }

    /**
     * @param OrderInterface $order
     *
     * @return null|string
     */
    protected function getShippingName(OrderInterface $order)
    {
        return preg_replace("/[^A-Za-z0-9 ]/", "", $order->getShippingDescription());
    }

    /**
     * @param OrderInterface $order
     *
     * @return int
     */
    protected function getShippingAmount(OrderInterface $order)
    {
        return $this->configRepository->getAmountInCents((float)(
            $order->getShippingAmount()
            + $order->getShippingTaxAmount()
            + $order->getShippingDiscountTaxCompensationAmount()
        ));
    }

    /**
     * @param OrderInterface $order
     *
     * @return float|int
     */
    public function getShippingVatPercentage(OrderInterface $order)
    {
        $vatPercentage = 0;
        if ($order->getShippingAmount() > 0) {
            $vatPercentage = ($order->getShippingTaxAmount() / $order->getShippingAmount()) * 100;
        }

        return $this->configRepository->getAmountInCents((float)$vatPercentage);
    }

    /**
     * @param Creditmemo $creditmemo
     * @param bool $addShipping
     *
     * @return array
     */
    public function getRefundLines($creditmemo, $addShipping = false)
    {
        $orderLines = [];
        $refundItems = $creditmemo->getAllItems();

        /** @var CreditmemoItemInterface $item */
        foreach ($refundItems as $item) {
            $orderLines[] = [
                'merchant_order_line_id' => $item->getOrderItemId(),
                'quantity' => $item->getQty()
            ];
        }

        if ($addShipping) {
            $orderLines[] = [
                'merchant_order_line_id' => 'shipping',
                'quantity' => 1
            ];
        }
        return $orderLines;
    }
}

