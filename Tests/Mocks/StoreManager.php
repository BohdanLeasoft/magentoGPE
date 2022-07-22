<?php

namespace GingerPay\Payment\Tests\Mocks;

class StoreManager
{
    public function getStore()
    {
        return $this;
    }

    public function getStoreId()
    {
        return 1;
    }

    public function startEnvironmentEmulation($storeId, $area = null, $force = null)
    {

    }

    public function stopEnvironmentEmulation() {}

    public function getWebsiteId()
    {
        return 'WebsiteId';
    }

}
