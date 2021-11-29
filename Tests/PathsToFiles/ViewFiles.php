<?php

class ViewFiles
{
    private static $viewFiles = [
        'view/adminhtml/layout/default.xml',
        'view/adminhtml/templates/info/pdf/banktransfer.phtml',
        'view/adminhtml/templates/info/afterpay.phtml',
        'view/adminhtml/templates/info/banktransfer.phtml',
        'view/adminhtml/templates/info/klarna.phtml',
        'view/adminhtml/templates/system/config/button/apikey.phtml',
        'view/adminhtml/templates/system/config/fieldset/apikey_result.phtml',
        'view/adminhtml/templates/system/config/fieldset/header.phtml',
        'view/adminhtml/web/css/styles.css',
        'view/adminhtml/web/images/icons.png',
        'view/adminhtml/web/images/icons@x2.png',
        'view/frontend/layout/checkout_index_index.xml',
        'view/frontend/layout/checkout_onepage_success.xml',
        'view/frontend/templates/checkout/success.phtml',
        'view/frontend/templates/info/banktransfer.phtml',
        'view/frontend/templates/info/afterpay.phtml',
        'view/frontend/templates/info/klarna.phtml',
        'view/frontend/web/css/checkout.css',
        'view/frontend/web/css/success.css',
        'view/frontend/web/images/afterpay.png',
        'view/frontend/web/images/amex.png',
        'view/frontend/web/images/applepay.png',
        'view/frontend/web/images/bancontact.png',
        'view/frontend/web/images/banktransfer.png',
        'view/frontend/web/images/cod.png',
        'view/frontend/web/images/creditcard.png',
        'view/frontend/web/images/googlepay.png',
        'view/frontend/web/images/ideal.png',
        'view/frontend/web/images/klarna.png',
        'view/frontend/web/images/klarnadirect.png',
        'view/frontend/web/images/klarnadirectdebit.png',
        'view/frontend/web/images/payconiq.png',
        'view/frontend/web/images/paypal.png',
        'view/frontend/web/images/sofort.png',
        'view/frontend/web/images/tikkie.png',
        'view/frontend/web/js/view/payment/method-renderer/afterpay.js',
        'view/frontend/web/js/view/payment/method-renderer/default.js',
        'view/frontend/web/js/view/payment/method-renderer/ideal.js',
        'view/frontend/web/js/view/payment/method-renderer/klarna.js',
        'view/frontend/web/js/view/payment/ideal-validation-mixin.js',
        'view/frontend/web/js/view/payment/method-renderer.js',
        'view/frontend/web/template/payment/afterpay.html',
        'view/frontend/web/template/payment/default.html',
        'view/frontend/web/template/payment/ideal.html',
        'view/frontend/web/template/payment/klarna.html',
        'view/frontend/requirejs-config.js',
    ];

    public static function getViewFiles()
    {
        return self::$viewFiles;
    }
}
