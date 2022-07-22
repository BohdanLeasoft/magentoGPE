<?php

class ControllerAndViewModuleFiles
{
    private static $viewModelFile = [
        'ViewModel/Checkout/Success.php'
    ];

    private static $controllerFiles = [
        'Controller/Adminhtml/Action/Apikey.php',
        'Controller/Checkout/Process.php',
        'Controller/Checkout/Redirect.php',
        'Controller/Checkout/Webhook.php',
    ];

    public static function getViewModuleFile()
    {
        return self::$viewModelFile;
    }

    public static function getControllerFiles()
    {
        return self::$controllerFiles;
    }
}