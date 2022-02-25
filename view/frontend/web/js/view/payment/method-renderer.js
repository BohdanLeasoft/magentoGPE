/*
 * All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        $,
        Component,
        rendererList
    ) {
        'use strict';
        var defaultComponent = 'GingerPay_Payment/js/view/payment/method-renderer/default';
        var idealComponent = 'GingerPay_Payment/js/view/payment/method-renderer/ideal';
        var klarnaComponent = 'GingerPay_Payment/js/view/payment/method-renderer/klarnapaylater';
        var afterpayComponent = 'GingerPay_Payment/js/view/payment/method-renderer/afterpay';
        var methods = [
            {type: 'ginger_methods_bancontact', component: defaultComponent},
            {type: 'ginger_methods_banktransfer', component: defaultComponent},
            {type: 'ginger_methods_creditcard', component: defaultComponent},
            {type: 'ginger_methods_applepay', component: defaultComponent},
            {type: 'ginger_methods_klarnapaynow', component: defaultComponent},
            {type: 'ginger_methods_paypal', component: defaultComponent},
            {type: 'ginger_methods_amex', component: defaultComponent},
            {type: 'ginger_methods_tikkie', component: defaultComponent},
            {type: 'ginger_methods_payconiq', component: defaultComponent},
            {type: 'ginger_methods_klarnapaylater', component: klarnaComponent},
            {type: 'ginger_methods_afterpay', component: afterpayComponent},
            {type: 'ginger_methods_ideal', component: idealComponent},
            {type: 'ginger_methods_googlepay', component: defaultComponent},
            {type: 'ginger_methods_sofort', component: defaultComponent},
            {type: 'ginger_methods_klarnadirectdebit', component: defaultComponent}
        ];
        $.each(methods, function (k, method) {
            var paymentMethod = window.checkoutConfig.payment[method['type']];

            if (paymentMethod.isActive)
            {
                if (method.type == 'ginger_methods_applepay')
                {
                    if (window.ApplePaySession && paymentMethod.isActive)
                    {
                        rendererList.push(method);
                    }
                }
                else
                {
                    rendererList.push(method);
                }
            }

        });
        return Component.extend({});
    }
);
