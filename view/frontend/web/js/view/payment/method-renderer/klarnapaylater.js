/*
 * All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'ko',
        'jquery',
        'GingerPay_Payment/js/view/payment/method-renderer/default',
        'Magento_Checkout/js/model/quote'
    ],
    function (ko, $, Component, quote) {
        var checkoutConfig = window.checkoutConfig.payment;
        'use strict';
        return Component.extend(
            {
                defaults: {
                    template: 'GingerPay_Payment/payment/klarnapaylater',
                    selectedPrefix: null
                },
                getCustomerPrefixes: function () {
                    return checkoutConfig[this.item.method].prefix;
                },
                getDob: function () {
                    var dob = window.checkoutConfig.quoteData.customer_dob;
                    if (dob == null) {
                        return ko.observable(false);
                    }
                    return ko.observable(new Date(dob));
                },
                validate: function () {
                    var form = $('#ginger_methods_klarnapaylater-form');
                    return form.validation() && form.validation('isValid');
                },
                getData: function () {
                    return {
                        'method': this.item.method,
                        'additional_data': {
                            "prefix": this.selectedPrefix,
                            "dob": $('#' + this.item.method + '_dob').val()
                        }
                    };
                }
            }
        );
    }
);
