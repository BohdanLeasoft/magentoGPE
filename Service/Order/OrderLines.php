<?php
/**
 * All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace GingerPay\Payment\Service\Order;

use GingerPay\Payment\Redefiners\Service\ServiceOrderLinesRedefiner;
use GingerPay\Payment\Api\Config\RepositoryInterface as ConfigRepository;
use Magento\Framework\UrlInterface;
use Magento\Sales\Api\Data\CreditmemoItemInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Model\Order\Creditmemo;

/**
 * OrderLines order class
 */
class OrderLines extends ServiceOrderLinesRedefiner
{
    /**
     * OrderLines constructor.
     *
     * @param ConfigRepository $configRepository
     */
    public function __construct(
        ConfigRepository $configRepository
    )
    {
        $this->configRepository = $configRepository;
    }
}
