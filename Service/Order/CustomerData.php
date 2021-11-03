<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace EMSPay\Payment\Service\Order;

use EMSPay\Payment\Redefiners\Service\ServiceOrderRedefiner;
use EMSPay\Payment\Api\Config\RepositoryInterface as ConfigRepository;
use EMSPay\Payment\Model\Methods\Afterpay;
use EMSPay\Payment\Model\Methods\Klarna;
use EMSPay\Payment\Model\Methods\KlarnaDirect;
use Magento\Framework\HTTP\Header;
use Magento\Framework\Locale\Resolver;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Order Customer Data class
 */
class CustomerData extends ServiceOrderRedefiner
{
    /**
     * CustomerData constructor.
     *
     * @param Resolver $resolver
     * @param Header $httpHeader
     * @param ConfigRepository $configRepository
     */
    public function __construct(
        Resolver $resolver,
        Header $httpHeader,
        ConfigRepository $configRepository
    )
    {
        $this->resolver = $resolver;
        $this->httpHeader = $httpHeader;
        $this->configRepository = $configRepository;
    }
}
