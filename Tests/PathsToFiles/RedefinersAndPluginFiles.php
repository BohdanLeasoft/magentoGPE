<?php

class RedefinersAndPluginFiles
{
    private static $redefinerFiles = [
        'Redefiners/Controller/ControllerCheckoutActionRedefiner.php',
        'Redefiners/Model/ModelBuilderRedefiner.php',
        'Redefiners/Model/PaymentLibraryRedefiner.php',
        'Redefiners/Service/ServiceOrderLinesRedefiner.php',
        'Redefiners/Service/ServiceOrderRedefiner.php',
        'Redefiners/Service/TransactionRedefiner.php',
        'Redefiners/Setup/SetupRedefiner.php'
    ];

    private static $pluginFiles = [
        'Plugin/Framework/App/Request/CsrfValidatorSkip.php'
    ];

    public static function getRedefinersFiles()
    {
        return self::$redefinerFiles;
    }

    public static function getPluginFiles()
    {
        return self::$pluginFiles;
    }
}
