<?php

class ModelFiles
{
    private static $modelFiles = [
        'Model/AbstractPayment.php',
        'Model/PaymentConfigProvider.php',
        'Model/PaymentLibrary.php',
        'Model/Methods/Afterpay.php',
        'Model/Methods/Amex.php',
        'Model/Methods/ApplePay.php',
        'Model/Methods/Bancontact.php',
        'Model/Methods/Banktransfer.php',
        'Model/Methods/Creditcard.php',
        'Model/Methods/General.php',
        'Model/Methods/Googlepay.php',
        'Model/Methods/Ideal.php',
        'Model/Methods/Klarna.php',
        'Model/Methods/KlarnaDirect.php',
        'Model/Methods/KlarnaDirectDebit.php',
        'Model/Methods/Payconiq.php',
        'Model/Methods/Paypal.php',
        'Model/Methods/Sofort.php',
        'Model/Methods/Tikkie.php',
        'Model/Config/Repository.php',
        'Model/Builders/ApiBuilder.php',
        'Model/Builders/ConfigRepositoryBuilder.php',
        'Model/Builders/ControllerCheckoutActionBuilder.php',
        'Model/Builders/LibraryConfigProvider.php',
        'Model/Builders/ServiceOrderBuilder.php',
        'Model/Builders/ServiceOrderLinesBuilder.php',
        'Model/Builders/SetupBuilder.php',
        'Model/Builders/TransactionBuilder.php',
        'Model/Api/GingerClient.php',
        'Model/Api/UrlProvider.php',
        'Model/Adminhtml/Source/ApiKey.php',
        'Model/Adminhtml/Source/Pending.php',
    ];

    public static function getModuleFiles()
    {
        return self::$modelFiles;
    }
}
