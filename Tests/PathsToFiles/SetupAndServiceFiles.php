<?php

class SetupAndServiceFiles
{
    private static $serviceFiles = [
        'Service/Order/Cancel.php',
        'Service/Order/CustomerData.php',
        'Service/Order/GetOrderByTransaction.php',
        'Service/Order/OrderCommentHistory.php',
        'Service/Order/OrderLines.php',
        'Service/Order/SendInvoiceEmail.php',
        'Service/Order/SendOrderEmail.php',
        'Service/Order/UpdateStatus.php',
        'Service/Transaction/AbstractTransaction.php',
        'Service/Transaction/ProcessRequest.php',
        'Service/Transaction/ProcessUpdate.php',
        'Service/Transaction/Process/Cancelled.php',
        'Service/Transaction/Process/Complete.php',
        'Service/Transaction/Process/Error.php',
        'Service/Transaction/Process/Expired.php',
        'Service/Transaction/Process/Processing.php',
        'Service/Transaction/Process/Unknown.php',
    ];

    public static function getServiceFiles()
    {
        return self::$serviceFiles;
    }
}
