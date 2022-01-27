<?php
/**
 * All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace GingerPay\Payment\Plugin\Framework\App\Request;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Request\CsrfValidator;
use Magento\Framework\App\RequestInterface;

/**
 * CsrfValidatorSkip request class
 */
class CsrfValidatorSkip
{

    /**
     * @param CsrfValidator    $subject
     * @param \Closure         $proceed
     * @param RequestInterface $request
     * @param ActionInterface  $action
     */
    public function aroundValidate(
        CsrfValidator $subject,
        \Closure $proceed,
        RequestInterface $request,
        ActionInterface $action
    ) {
        if ($request->getModuleName() == 'gingerpay') {
            return;
        }

        $proceed($request, $action);
    }
}
