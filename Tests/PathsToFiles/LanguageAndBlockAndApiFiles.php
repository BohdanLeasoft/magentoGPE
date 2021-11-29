<?php

class LanguageAndBlockAndApiFiles
{
    private static $apiAndBlockFiles = [
        'Api/Config/RepositoryInterface.php',
        'Block/Adminhtml/Render/Header.php',
        'Block/Adminhtml/Render/Heading.php',
        'Block/Adminhtml/System/Config/Form/Apikey/Button.php',
        'Block/Adminhtml/System/Config/Form/Apikey/Result.php',
        'Block/Info/Afterpay.php',
        'Block/Info/Banktransfer.php',
        'Block/Info/Klarna.php'
    ];

    private static $languageFiles = [
        'i18n/de_DE.csv',
        'i18n/en_US.csv',
        'i18n/fr_FR.csv',
        'i18n/nl_NL.csv',
    ];

    public static function getApiAndBlockFiles()
    {
        return self::$apiAndBlockFiles;
    }

    public static function getLanguageFiles()
    {
        return self::$languageFiles;
    }
}
