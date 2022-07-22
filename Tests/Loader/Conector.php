<?php

function __($str)
{
    return $str;
}

require_once __DIR__.'/../Mocks/Order.php';
require_once __DIR__.'/../Mocks/UrlProvider.php';
require_once __DIR__.'/../../Model/Builders/RecurringHelper.php';
require_once __DIR__.'/../../Model/Builders/RecurringBuilder.php';
require_once __DIR__.'/../../Model/Builders/ServiceOrderBuilder.php';
require_once __DIR__.'/../../Api/Config/RepositoryInterface.php';
require_once __DIR__.'/../../Model/Builders/ApiBuilder.php';
require_once __DIR__.'/../../Model/Builders/ConfigRepositoryBuilder.php';
require_once __DIR__.'/../../Model/Builders/LibraryConfigProvider.php';



