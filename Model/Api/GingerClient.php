<?php
/**
 * All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace GingerPay\Payment\Model\Api;

use GingerPay\Payment\Api\Config\RepositoryInterface as ConfigRepository;
use GingerPay\Payment\Redefiners\Model\ModelBuilderRedefiner;
use Magento\Framework\Message\ManagerInterface;
/**
 * GingerClient API class
 */
class GingerClient extends ModelBuilderRedefiner
{
    /**
     * GingerClient constructor.
     *
     * @param ConfigRepository $configRepository
     * @param UrlProvider $urlProvider
     */
    public function __construct(
        ConfigRepository $configRepository,
        UrlProvider $urlProvider,
        ManagerInterface $messageManager
    ) {
        $this->configRepository = $configRepository;
        $this->urlProvider = $urlProvider;
        $this->messageManager = $messageManager;
    }
}
