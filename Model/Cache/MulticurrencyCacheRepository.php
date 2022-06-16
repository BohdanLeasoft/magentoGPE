<?php

namespace GingerPay\Payment\Model\Cache;

class MulticurrencyCacheRepository
{
    public function isCacheExist()
    {
        return file_exists(__DIR__."/currencyList.json");
    }

    public function isTermOfActionExpired($termOfAction)
    {
        if (date("Y-m-d H:i:s") < $termOfAction)
        {
            return false;
        }
        return true;
    }

    public function set($client)
    {
        $currencyList = fopen(__DIR__."/currencyList.json", "w+");
        $currencyData = json_encode([
                'term_of_action' => date("Y-m-d H:i:s", strtotime('+2 hours')),
                'currency_list' => $client->getCurrencyList()
            ]
        );

        fwrite($currencyList, $currencyData);
        fclose($currencyList);
    }

    public function get()
    {
        $jsonContent = file_get_contents(__DIR__."/currencyList.json");
        return (json_decode($jsonContent, true));
    }

    public function getAvailablePayments($client)
    {
        if ($this->isCacheExist())
        {
            $currencyData = $this->get();
            if ($this->isTermOfActionExpired($currencyData['term_of_action']))
            {
                $this->set($client);
                $currencyData = $this->get();
            }

            return $currencyData['currency_list'];
        }

        $this->set($client);
        $currencyData = $this->get();
        return $currencyData['currency_list'];
    }
}

