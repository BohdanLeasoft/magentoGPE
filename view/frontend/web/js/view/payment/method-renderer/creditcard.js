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
                template: 'GingerPay_Payment/payment/creditcard',
                selectedPeriodicity: null
            },
            getDisplay: function () {
                return checkoutConfig[this.item.method].displayRecurringSelect;
            },
            getRecurringPeriodicity: function () {
                return checkoutConfig[this.item.method].periodicity;
            },
            getData: function () {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        "periodicity": this.selectedPeriodicity
                    }
                };
            },
            validate: function () {
                var form = $('#ginger_methods_creditcard-form');
                document.getElementsByName("periodicitySelect")[0].style.display = "none";
                return form.validation() && form.validation('isValid');
            }
        });
    }
);
