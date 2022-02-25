<?php

namespace GingerPay\Payment\Redefiners\Model;

use GingerPay\Payment\Model\Builders\LibraryConfigProvider;

class ModelBuilderRedefiner extends LibraryConfigProvider
{
    /**
     * Endpoint variable
     *
     * @var string
     */
    public $ENDPOINT = 'https://api.online.emspay.eu/';
}
