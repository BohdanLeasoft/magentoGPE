<?php

namespace GingerPay\Payment\Redefiners\Model;

use GingerPay\Payment\Model\Builders\LibraryConfigProvider;

class ModelBuilderRedefiner extends LibraryConfigProvider
{
    /**
     * Endpoint
     *
     * @var string
     */
    public $ENDPOINT = 'https://api.online.emspay.eu/';
}
