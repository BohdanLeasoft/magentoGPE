<?php
/**
 * All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace GingerPay\Payment\Service\Order;

use GingerPay\Payment\Redefiners\Service\ServiceOrderRedefiner;
use GingerPay\Payment\Api\Config\RepositoryInterface as ConfigRepository;
use Magento\Framework\App\ProductMetadataInterface ;

/**
 * ExtraLines order class
 */
class OrderDataCollector extends ServiceOrderRedefiner
{
    /**
     * ExtraLines constructor.
     *
     * @param ConfigRepository $configRepository
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        ConfigRepository $configRepository,
        ProductMetadataInterface $productMetadata
    )
    {
        $this->configRepository = $configRepository;
        $this->productMetadata = $productMetadata;
    }
}


