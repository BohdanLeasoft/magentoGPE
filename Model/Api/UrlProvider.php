<?php
/**
 * All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace GingerPay\Payment\Model\Api;

use Magento\Framework\UrlInterface;
use GingerPay\Payment\Redefiners\Model\ModelBuilderRedefiner;

/**
 * UrlProvider API class
 */
class UrlProvider extends ModelBuilderRedefiner
{
    /**
     * UrlProvider constructor.
     *
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        UrlInterface $urlBuilder
    )
    {
        $this->urlBuilder = $urlBuilder;
    }
}
