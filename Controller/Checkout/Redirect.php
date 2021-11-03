<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace EMSPay\Payment\Controller\Checkout;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use EMSPay\Payment\Api\Config\RepositoryInterface as ConfigRepository;
use EMSPay\Payment\Redefiners\Controller\ControllerCheckoutActionRedefiner as ActionRedefiner;

/**
 * Checkout redirect class
 */
class Redirect extends ActionRedefiner
{
    /**
     * Redirect constructor.
     *
     * @param Context $context
     * @param Session $checkoutSession
     * @param PaymentHelper $paymentHelper
     * @param ConfigRepository $configRepository
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        PaymentHelper $paymentHelper,
        ConfigRepository $configRepository
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->paymentHelper = $paymentHelper;
        $this->configRepository = $configRepository;
        parent::__construct($context);
    }

    /**
     * EMS Redirect Controller
     *
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        return $this->redirect();
    }
}
