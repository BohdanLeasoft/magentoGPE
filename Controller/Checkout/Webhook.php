<?php
/**
 * All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace GingerPay\Payment\Controller\Checkout;

use GingerPay\Payment\Api\Config\RepositoryInterface as ConfigRepository;
use GingerPay\Payment\Model\PaymentLibrary as PaymentLibraryModer;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Filesystem\Driver\File as FilesystemDriver;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Payment\Helper\Data as PaymentHelper;
use GingerPay\Payment\Redefiners\Controller\ControllerCheckoutActionRedefiner as ActionRedefiner;

/**
 * Webhook controller class
 */
class Webhook extends ActionRedefiner
{
    /**
     * Webhook constructor.
     *
     * @param Context $context
     * @param Session $checkoutSession
     * @param PaymentHelper $paymentHelper
     * @param PaymentLibraryModer $paymentLibraryModel
     * @param ConfigRepository $configRepository
     * @param Json $json
     * @param FilesystemDriver $filesystemDriver
     */
    public function __construct(
        Context             $context,
        Session             $checkoutSession,
        PaymentHelper       $paymentHelper,
        PaymentLibraryModer $paymentLibraryModel,
        ConfigRepository    $configRepository,
        Json                $json,
        FilesystemDriver    $filesystemDriver
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->paymentHelper = $paymentHelper;
        $this->resultFactory = $context->getResultFactory();
        $this->paymentLibraryModel = $paymentLibraryModel;
        $this->configRepository = $configRepository;
        $this->resultFactory = $context->getResultFactory();
        $this->json = $json;
        $this->filesystemDriver = $filesystemDriver;
        parent::__construct($context);
    }

    /**
     * Webhook Controller
     *
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
       return $this->webhook();
    }
}
