<?php

namespace GingerPay\Payment\Model\Builders;

use Magento\Checkout\Model\Cart;
use Magento\Catalog\Model\Product;

class CartBuilder
{
    /**
     * @var Cart
     */
    public $cart;
    /**
     * @var Product
     */
    public $product;

   /**
    * CartBuilder constructor.
    *
    * @param Cart $cart
    * @param Product $product
    */
    public function __construct(
        Cart $cart,
        Product $product
    ) {
        $this->cart = $cart;
        $this->product = $product;
    }

    /**
     * Check recurring attribute value. Enabled => 1 | Disabled => 2
     *
     * @param string $attributeValue
     *
     * @return bool
     */

    private function checkRecurringAttributeValue($attributeValue)
    {
        if ($attributeValue == '1')
        {
            return true;
        }
        return false;
    }

    /**
     * Get items from cart and send it value for check
     *
     * @return bool
     */
    public function isRecurringEnabledForItemsInCart()
    {
        foreach ($this->cart->getQuote()->getItems() as $product)
        {
            $product = $this->product->load($product->getProduct()->getId());

            if (!$this->checkRecurringAttributeValue($product->getData('recurring_attribute')))
            {
                return false;
            }
        }
        return true;
    }
}
