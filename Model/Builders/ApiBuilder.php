<?php

namespace GingerPay\Payment\Model\Builders;

use Braintree\Exception;

class ApiBuilder
{
    /**
     * @var ConfigRepository
     */
    protected $configRepository;

    /**
     * @var UrlProvider
     */
    protected $urlProvider;

    /**
     * @var \Ginger\ApiClient
     */
    protected $client = null;

    /**
     * @var string
     */
    protected $apiKey = null;

    /**
     * @var string
     */
    protected $endpoint = null;

    /**
     * Endpoint
     */
    public const ENDPOINT = 'https://api.online.emspay.eu/';

    /**
     * Ginger
     *
     * @var object
     */
    protected $ginger_lib;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Get function
     *
     * @param int $storeId
     * @param string $testApiKey
     *
     * @return bool|\Ginger\ApiClient
     * @throws \Exception
     */
    public function get(int $storeId = null, string $testApiKey = null)
    {

        if ($this->client !== null && $testApiKey === null) {
            return $this->client;
        }

        if (empty($storeId)) {
            $storeId = $this->configRepository->getCurrentStoreId();
        }

        if ($testApiKey !== null) {
            $this->apiKey = $testApiKey;
        }

        if ($this->apiKey === null) {
            $this->apiKey = $this->configRepository->getApiKey((int)$storeId);
        }

        if ($this->endpoint === null) {
            $this->endpoint = $this->urlProvider->getEndPoint();
        }

        if (!$this->apiKey || !$this->endpoint) {
            $this->configRepository->addTolog('error', 'Missing Api Key / Api Endpoint');
            return false;
        }

        $gingerClient = new \Ginger\Ginger;

        try {
            $this->client = $gingerClient->createClient($this->endpoint, $this->apiKey);
        } catch(Exception $e) {
            if ($e instanceof HttpException && $e->getStatusCode()== 401) {
                dd('you are not authorized');
            }
        }

        return $this->client;
    }

    /**
     * Return Url Builder
     *
     * @return mixed
     */
    public function getReturnUrl()
    {
        return $this->urlBuilder->getUrl('ginger/checkout/process');
    }

    /**
     * Webhook Url Builder
     *
     * @return string
     */
    public function getWebhookUrl()
    {
        return $this->urlBuilder->getUrl('ginger/checkout/webhook/');
    }

    /**
     * Process Url Builder
     *
     * @param string $transactionId
     *
     * @return string
     */
    public function getSuccessProcessUrl(string $transactionId) : string
    {
        return $this->urlBuilder->getUrl('ginger/checkout/process', ['order_id' => $transactionId]);
    }

    /**
     * Checkout Webhook Url Builder
     *
     * @return string
     */
    public function getSuccessUrl()
    {
        return $this->urlBuilder->getUrl('checkout/onepage/success?utm_nooverride=1');
    }

    /**
     * Get end point
     *
     * @return string
     */
    public function getEndPoint()
    {
        return self::ENDPOINT;
    }
}
