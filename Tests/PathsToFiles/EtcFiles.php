<?php

class EtcFiles
{
    private static $etcFiles = [
        'etc/acl.xml',
        'etc/config.xml',
        'etc/di.xml',
        'etc/module.xml',
        'etc/payment.xml',
        'etc/frontend/di.xml',
        'etc/frontend/routes.xml',
        'etc/adminhtml/events.xml',
        'etc/adminhtml/methods.xml',
        'etc/adminhtml/routes.xml',
        'etc/adminhtml/system.xml',
        'etc/adminhtml/methods/afterpay.xml',
        'etc/adminhtml/methods/amex.xml',
        'etc/adminhtml/methods/applepay.xml',
        'etc/adminhtml/methods/bancontact.xml',
        'etc/adminhtml/methods/banktransfer.xml',
        'etc/adminhtml/methods/creditcard.xml',
        'etc/adminhtml/methods/googlepay.xml',
        'etc/adminhtml/methods/ideal.xml',
        'etc/adminhtml/methods/klarna.xml',
        'etc/adminhtml/methods/klarnadirect.xml',
        'etc/adminhtml/methods/klarnadirectdebit.xml',
        'etc/adminhtml/methods/payconiq.xml',
        'etc/adminhtml/methods/paypal.xml',
        'etc/adminhtml/methods/sofort.xml',
        'etc/adminhtml/methods/tikkie.xml'
    ];

    public static function getEtcFiles()
    {
        return self::$etcFiles;
    }

}
