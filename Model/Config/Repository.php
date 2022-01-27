<?php
/**
 * All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace GingerPay\Payment\Model\Config;

use GingerPay\Payment\Redefiners\Model\ModelBuilderRedefiner;
use GingerPay\Payment\Logger\DebugLogger;
use GingerPay\Payment\Logger\ErrorLogger;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface as StoreManager;

/**
 * Config repository class
 */
class Repository extends ModelBuilderRedefiner
{
    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param RemoteAddress $remoteAddress
     * @param StoreManager $storeManager
     * @param PricingHelper $pricingHelper
     * @param AssetRepository $assetRepository
     * @param ModuleListInterface $moduleList
     * @param ErrorLogger $errorLogger
     * @param DebugLogger $debugLogger
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        RemoteAddress $remoteAddress,
        StoreManager $storeManager,
        PricingHelper $pricingHelper,
        AssetRepository $assetRepository,
        ModuleListInterface $moduleList,
        ErrorLogger $errorLogger,
        DebugLogger $debugLogger
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->remoteAddress = $remoteAddress;
        $this->storeManager = $storeManager;
        $this->pricingHelper = $pricingHelper;
        $this->assetRepository = $assetRepository;
        $this->moduleList = $moduleList;
        $this->errorLogger = $errorLogger;
        $this->debugLogger = $debugLogger;
    }
}
