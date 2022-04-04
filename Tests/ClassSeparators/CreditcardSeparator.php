<?php

namespace GingerPay\Payment\Tests\ClassSeparators;

//require_once __DIR__.'/../../Model/Methods/Creditcard.php';
//require_once __DIR__.'/../../Redefiners/Model/PaymentLibraryRedefiner.php';

//use GingerPay\Payment\Model\Methods\Creditcard;
use GingerPay\Payment\Redefiners\Model\PaymentLibraryRedefiner;

class CreditcardSeparator// extends Creditcard
{
    public function __construct()
    {
    }

    const METHOD_CODE = "credit-card";
}
