<?php

namespace EMSPay\Payment\Model\Builders;

use EMSPay\Payment\Api\Config\RepositoryInterface as ConfigRepository;
use EMSPay\Payment\Model\PaymentLibrary as PaymentLibraryModel;
use EMSPay\Payment\Model\PaymentLibrary as PaymentLibraryModer;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Filesystem\Driver\File as FilesystemDriver;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Payment\Helper\Data as PaymentHelper;

class ControllerCheckoutActionBuilder extends Action
{

    /**
     * @var Session
     */
    public $checkoutSession;

    /**
     * @var PaymentHelper
     */
    public $paymentHelper;

    /**
     * @var PaymentLibraryModel
     */
    public $paymentLibraryModel;

    /**
     * @var ConfigRepository
     */
    public $configRepository;
    /**
     * @return ResponseInterface|ResultInterface
     */

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var Json
     */
    private $json;

    /**
     * @var FilesystemDriver
     */
    private $filesystemDriver;


    public function execute()
    {
        // Overriding by classes in Checkout due to theare functionality
    }

    public function process()
    {
        $orderId = $this->getRequest()->getParam('order_id', null);

        if ($orderId === null) {
            $this->configRepository->addTolog('error', __('Invalid return, missing order id.'));
            $this->messageManager->addNoticeMessage(__('Invalid return from EMS.'));
            return $this->_redirect('checkout/cart');
        }

        try
        {
            $status = $this->paymentLibraryModel->processTransaction($orderId, 'success');
            var_dump($status); die();

            if (!empty($status['success']))
            {
                $this->checkoutSession->start();
                return $this->_redirect('checkout/onepage/success?utm_nooverride=1');
            }
            else
            {
                $this->checkoutSession->restoreQuote();
                if (!empty($status['cart_msg']))
                {
                    $this->messageManager->addNoticeMessage($status['cart_msg']);
                }
                else
                {
                    $this->messageManager->addNoticeMessage(__('Something went wrong.'));
                }
            }
        }
        catch (\Exception $e)
        {
            $this->configRepository->addTolog('error', $e->getMessage());
            $this->messageManager->addExceptionMessage($e, __('There was an error checking the transaction status.'));
        }

        return $this->_redirect('checkout/cart');
    }

    /**
     * EMS Redirect Controller
     *
     * @return ResponseInterface|ResultInterface
     */
    public function redirect()
    {
        $order = $this->checkoutSession->getLastRealOrder();

        try {

            $method = $order->getPayment()->getMethod();

            $methodInstance = $this->paymentHelper->getMethodInstance($method);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage('Unknown Error');
            $this->configRepository->addTolog('error', 'Unknown Error');
            $this->checkoutSession->restoreQuote();
            return $this->_redirect('checkout/cart');
        }

        if ($methodInstance instanceof \EMSPay\Payment\Model\PaymentLibrary) {
            try {
                $result = $methodInstance->startTransaction($order);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Could not start transaction, please select other payment method.')
                );
                $this->configRepository->addTolog('error', $e->getMessage());
                $this->checkoutSession->restoreQuote();
                return $this->_redirect('checkout/cart');
            }

            if (!empty($result['error'])) {
                $this->messageManager->addErrorMessage($result['error']);
                $this->configRepository->addTolog('error', $result['error']);
                $this->checkoutSession->restoreQuote();
                return $this->_redirect('checkout/cart');
            }

            return $this->getResponse()->setRedirect($result['redirect']);
        }

        $this->messageManager->addErrorMessage('Unknown Error');
        $this->configRepository->addTolog('error', 'Unknown Error');
        $this->checkoutSession->restoreQuote();
        return $this->_redirect('checkout/cart');
    }
    /**
     * Webhook Controller
     *
     * @return ResponseInterface|ResultInterface|void
     */
    public function webhook()
    {
        try {
            $input = $this->json->unserialize(
                $this->filesystemDriver->fileGetContents("php://input")
            );
            $this->configRepository->addTolog('webhook', $input);
        } catch (\Exception $e) {
            $input = null;
            $this->configRepository->addTolog('error', 'Webhook exception: ' . $e->getMessage());
        }

        if (!$input) {
            $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $result->setHttpResponseCode(503);
            return $result;
        }

        if (isset($input['order_id'])) {
            try {
                $this->paymentLibraryModel->processTransaction($input['order_id'], 'webhook');
            } catch (\Exception $e) {
                $this->configRepository->addTolog('error', $e->getMessage());
                $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
                $result->setHttpResponseCode(503);
                return $result;
            }
        }
    }

}