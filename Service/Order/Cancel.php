<?php
/**
 * All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace GingerPay\Payment\Service\Order;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use GingerPay\Payment\Api\Config\RepositoryInterface as ConfigRepository;
use GingerPay\Payment\Redefiners\Service\ServiceOrderRedefiner;


/**
 * Cancel order service class
 */
class Cancel extends ServiceOrderRedefiner
{
    /**
     * Cancel constructor.
     *
     * @param ConfigRepository $configRepository
     */
    public function __construct(
        ConfigRepository $configRepository
    )
    {
        $this->configRepository = $configRepository;
    }

    /**
     * @param OrderInterface $order
     *
     * @return bool
     */
    public function execute(OrderInterface $order): bool
    {
       return $this->cancel($order);
    }
}
