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
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Filesystem\Driver\File as FilesystemDriver;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Store\Model\App\Emulation;
use Magento\Framework\App\Action\Action;
use Magento\Store\Model\Store as StoreModel;
use GingerPay\Payment\Redefiners\Controller\ControllerCheckoutActionRedefiner as ActionRedefiner;

/**
 * Webhook controller class
 */
class Webhook extends Action
{
    /**
     * @var ActionRedefiner
     */
    private $action;

    /**
     * Webhook constructor.
     *
     * @param Context $context
     * @param Session $checkoutSession
     * @param PaymentLibraryModer $paymentLibraryModel
     * @param FilesystemDriver $filesystemDriver
     * @param ConfigRepository $configRepository
     * @param ActionRedefiner $action
     */
    public function __construct(
        Context             $context,
        Session             $checkoutSession,
        PaymentLibraryModer $paymentLibraryModel,
        FilesystemDriver    $filesystemDriver,
        ConfigRepository $configRepository,
        ActionRedefiner $action
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->resultFactory = $context->getResultFactory();
        $this->paymentLibraryModel = $paymentLibraryModel;
        $this->filesystemDriver = $filesystemDriver;
        $this->configRepository = $configRepository;
        $this->action = $action;
        $this->action->configRepository = $configRepository;
        $this->action->paymentLibraryModel = $paymentLibraryModel;
        $this->action->webhook();

        parent::__construct($context);
    }

    /**
     * Webhook Controller
     *
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
    }
}

