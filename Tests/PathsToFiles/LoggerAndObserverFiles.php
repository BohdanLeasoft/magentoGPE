<?php

class LoggerAndObserverFiles
{
    private static $observerFile = [
        'Observer/SalesOrderShipmentAfter.php'
    ];

    private static $loggerFiles = [
        'Logger/DebugLogger.php',
        'Logger/ErrorLogger.php',
        'Logger/Handler/Debug.php',
        'Logger/Handler/Error.php'
    ];

    public static function getObserverFile()
    {
        return self::$observerFile;
    }

    public static function getLoggerFiles()
    {
        return self::$loggerFiles;
    }
}

