<?php

namespace GingerPay\Payment\Model\Builders;

use GingerPay\Payment\Api\Config\RepositoryInterface as ConfigRepositoryInterface;
use GingerPay\Payment\Model\Methods\Afterpay;
use GingerPay\Payment\Model\Methods\KlarnaPayLater;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;

class ConfigRepositoryBuilder extends ApiBuilder implements ConfigRepositoryInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var RemoteAddress
     */
    protected $remoteAddress;

    /**
     * @var StoreManager
     */
    protected $storeManager;

    /**
     * @var AssetRepository
     */
    protected $assetRepository;

    /**
     * @var PricingHelper
     */
    protected $pricingHelper;

    /**
     * @var ModuleListInterface
     */
    protected $moduleList;

    /**
     * @var ErrorLogger
     */
    protected $errorLogger;

    /**
     * @var DebugLogger
     */
    protected $debugLogger;

    /**
     * Checke is payment available
     *
     * @param int $storeId
     *
     * @return bool
     */
    public function isAvailable(int $storeId): bool
    {
        $active = $this->getStoreConfig(self::XML_PATH_MODULE_ACTIVE);
        if (!$active) {
            return false;
        }

        $apiKey = $this->getApiKey($storeId);
        if (!$apiKey) {
            return false;
        }

        return true;
    }

    /**
     * Get config value
     *
     * @param string $path
     * @param int $storeId
     *
     * @return string|array
     */
    protected function getStoreConfig(string $path, int $storeId = 0)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Get API key
     *
     * @param int $storeId
     *
     * @return string|null
     */
    public function getApiKey(int $storeId): string
    {
        return $this->getStoreConfig(self::XML_PATH_APIKEY, $storeId);
    }

    /**
     * {@inheritDoc}
     */
    public function useMethodCheck(): bool
    {
        return (bool)$this->getFlag(self::XML_PATH_OBSERVER);
    }

    /**
     * Get config flag
     *
     * @param string $path
     * @param int $storeId
     *
     * @return bool
     */
    protected function getFlag(string $path, int $storeId = 0): bool
    {
        return $this->scopeConfig->isSetFlag($path, ScopeInterface::SCOPE_STORE, (int)$storeId);
    }

    /**
     * Get method code from order
     *
     * @param OrderInterface $order
     *
     * @return string
     */
    public function getMethodCodeFromOrder(OrderInterface $order): string
    {
        $method = $order->getPayment()->getMethodInstance()->getCode();
        return str_replace('ginger_methods_', '', $method);
    }

    /**
     * Get status processing
     *
     * @param string $method
     * @param int $storeId
     *
     * @return string|array
     */
    public function getStatusProcessing(string $method, int $storeId = 0): string
    {
        $path = 'payment/' . $method . '/order_status_processing';
        return $this->getStoreConfig($path, $storeId);
    }

    /**
     * Get status pending
     *
     * @param string $method
     * @param int $storeId
     *
     * @return string|array
     */
    public function getStatusPending(string $method, int $storeId = 0): string
    {
        $path = 'payment/' . $method . '/order_status_pending';
        return $this->getStoreConfig($path, $storeId);
    }

    /**
     * Send invoice
     *
     * @param string $method
     * @param int $storeId
     *
     * @return bool
     */
    public function sendInvoice(string $method, int $storeId = 0): bool
    {
        $path = 'payment/' . $method . '/invoice_notify';
        return (bool)$this->getFlag($path, $storeId);
    }

    /**
     * Get description
     *
     * @param string $method
     * @param int $storeId
     *
     * @return string
     */
    public function getDescription($order, $method): string
    {
        $storeId = (int)$order->getStoreId();

        $description = ($this->getStoreConfig($path = 'payment/' . $method . '/description', $storeId));
        $description = str_replace('%id%', $order->getIncrementId(), $description);
        $storeName = $this->getStoreConfig(self::XML_PATH_STORE_NAME, $storeId);
        $description = str_replace('%name%', $storeName, $description);

        return $description;
    }

    /**
     * Get account details
     *
     * @return string|array
     */
    public function getAccountDetails(): array
    {
        return $this->getStoreConfig(self::XML_PATH_ACCOUNT_DETAILS);
    }

    /**
     * Get company name
     *
     * @param int $storeId
     *
     * @return string|array
     */
    public function getCompanyName(int $storeId): string
    {
        return (string)$this->getStoreConfig(self::XML_PATH_COMPANY_NAME, $storeId);
    }

    /**
     * Check is Afterpay or Klarna allowed
     *
     * @param string $method
     * @param int $storeId
     *
     * @return bool
     */
    public function isAfterpayOrKlarnaAllowed(string $method, int $storeId = 0): bool
    {
        switch ($method)
        {
            case Afterpay::METHOD_CODE:
                $paymentTestModus = self::XML_PATH_AFTERPAY_TEST_MODUS;
                $paymentIpFilterList = self::XML_PATH_AFTERPAY_IP_FILTER;
                break;
            case KlarnaPayLater::METHOD_CODE:
                $paymentTestModus = self::XML_PATH_KLARNA_TEST_MODUS;
                $paymentIpFilterList = self::XML_PATH_KLARNA_IP_FILTER;
                break;
        }

        $testModus = $this->getStoreConfig(self::XML_PATH_AFTERPAY_TEST_MODUS, $storeId);

        if (!$testModus) {
            return true;
        }

        $ipFilterList = $this->getStoreConfig(self::XML_PATH_AFTERPAY_IP_FILTER, $storeId);
        if (strlen($ipFilterList) > 0) {
            $ipWhitelist = array_map('trim', explode(",", $ipFilterList));
            $remoteAddress = $this->remoteAddress->getRemoteAddress();
            if (!in_array($remoteAddress, $ipWhitelist)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get test key
     *
     * @param string $method
     * @param int $storeId
     * @param string|null $testFlag
     *
     * @return string|null
     */
    public function getTestKey(string $method, int $storeId, string $testFlag = ''): string
    {
        if ($method == KlarnaPayLater::METHOD_CODE && $testFlag == 'klarna')
        {
            return $this->getKlarnaTestApiKey($storeId, true);
        }
        elseif ($method == Afterpay::METHOD_CODE && $testFlag == 'afterpay')
        {
            return $this->getAfterpayTestApiKey($storeId, true);
        }

        return $this->getApiKey($storeId);
    }

    /**
     * Get Klarna test api key
     *
     * @param int $storeId
     * @param bool $force
     *
     * @return string|null
     */
    public function getKlarnaTestApiKey(int $storeId, bool $force = false)
    {
        $testApiKey = $this->getStoreConfig(self::XML_PATH_KLARNA_TEST_API_KEY, $storeId);
        $testModus = $this->getStoreConfig(self::XML_PATH_KLARNA_TEST_MODUS, $storeId);

        if ((!$testModus && !$force) || empty($testApiKey)) {
            return null;
        }
        return $testApiKey;
    }

    /**
     * Get Afterpay test api key
     *
     * @param int $storeId
     * @param bool $force
     *
     * @return string|null
     */
    public function getAfterpayTestApiKey(int $storeId, bool $force = false)
    {
        $testApiKey = $this->getStoreConfig(self::XML_PATH_AFTERPAY_TEST_API_KEY, $storeId);
        $testModus = $this->getStoreConfig(self::XML_PATH_AFTERPAY_TEST_MODUS, $storeId);

        if ((!$testModus && !$force) || empty($testApiKey)) {
            return null;
        }
        return $testApiKey;
    }

    /**
     * Add to log
     *
     * @param string $type
     * @param string $force
     */
    public function addTolog(string $type, $data)
    {
        if ($this->isDebugEnabled()) {
            if ($type == 'error') {
                $this->errorLogger->addLog($type, $data);
            } elseif ($this->isDebugEnabled()) {
                $this->debugLogger->addLog($type, $data);
            }
        }
    }

    /**
     * Check is debug enabled
     *
     * @return bool
     */
    public function isDebugEnabled(): bool
    {
        return (bool)$this->getFlag(self::XML_PATH_DEBUG);
    }

    /**
     * Get plugin version
     *
     * @return string
     */
    public function getPluginVersion(): string
    {
        return $this->getExtensionVersion();
    }

    /**
     * Get plugin name
     *
     * @return string
     */
    public function getPluginName(): string
    {
        return self::PLUGIN_NAME;
    }

    /**
     * Get extension version
     *
     * @return string
     */
    public function getExtensionVersion(): string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_VERSION);
    }


    /**
     * Get payment name by method code
     *
     * @param string $methodCode
     *
     * @return string
     */
    public function getPaymentNameByMethodCode($methodCode): string
    {
        return $this->scopeConfig->getValue('payment/'.$methodCode.'/title');
    }

    /**
     * Get error
     *
     * @param array $transaction
     *
     * @return string
     */
    public function getError(array $transaction)
    {
        if ($transaction['status'] == 'error' && (current($transaction['transactions'])['customer_message'])) {
            return current($transaction['transactions'])['customer_message'];
        }

        if ($transaction['status'] == 'cancelled') {
            $method = current($transaction['transactions'])['payment_method'];
            if ($method == $this->getShortMethodCode(
                Afterpay::METHOD_CODE) || $method == $this->getShortMethodCode(KlarnaPayLater::METHOD_CODE)
            ) {
                $methodName = 'payment';
                switch ($method)
                {
                    case $this->getShortMethodCode(Afterpay::METHOD_CODE):
                        $methodName = 'Afterpay';
                        break;
                    case $this->getShortMethodCode(KlarnaPayLater::METHOD_CODE):
                        $methodName = 'Klarna';
                        break;
                }
                return (string)__('Unfortunately, we can not currently accept
                your purchase with '.$methodName.'. Please choose another payment
                option to complete your order. We apologize for the inconvenience.');
            }
        }
        return false;
    }

    /**
     * Returns method code without prefix
     *
     * @param string $method
     *
     * @return string
     */
    protected function getShortMethodCode($method): string
    {
        return str_replace(self::METHOD_PREFIX, '', $method);
    }

    /**
     * Get amount in cents
     *
     * @param float $amount
     *
     * @return int
     */
    public function getAmountInCents(float $amount): int
    {
        return (int)round($amount * 100);
    }

    /**
     * Return format price
     *
     * @param float $amount
     *
     * @return float
     */
    public function formatPrice(float $price)
    {
        return $this->pricingHelper->currency((float)$price, true, false);
    }

    /**
     * Get currency store id
     *
     * @return int
     */
    public function getCurrentStoreId(): int
    {
        return (int)$this->getStore()->getId();
    }

    /**
     * Get store
     *
     * @return StoreInterface
     */
    public function getStore(): StoreInterface
    {
        try {
            return $this->storeManager->getStore();
        } catch (\Exception $e) {
            if ($store = $this->storeManager->getDefaultStoreView()) {
                return $store;
            }
        }

        $stores = $this->storeManager->getStores();
        return reset($stores);
    }

    /**
     * Get base url
     *
     * @param string $type
     *
     * @return string
     */
    public function getBaseUrl(string $type): string
    {
        return (string)$this->getStore()->getBaseUrl($type);
    }

    /**
     * Get payment logo
     *
     * @param string $code
     *
     * @return string|bool
     */
    public function getPaymentLogo(string $code)
    {
        if (!$this->displayPaymentImages()) {
            return false;
        }

        $logo = sprintf('%s::images/%s.png', self::MODULE_CODE, $this->getShortMethodCode($code));
        return $this->assetRepository->getUrl($logo);
    }

    /**
     * Display payment images
     *
     * @return bool
     */
    public function displayPaymentImages(): bool
    {
        return (bool)$this->getFlag(self::XML_PATH_IMAGES);
    }
}
