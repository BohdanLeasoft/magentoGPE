<?php

namespace GingerPay\Payment\Model\Builders;

use GingerPay\Payment\Api\Config\RepositoryInterface as ConfigRepository;
use GingerPay\Payment\Model\PaymentLibrary as PaymentLibraryModel;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Filesystem\Driver\File as FilesystemDriver;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Framework\Webapi\Rest\Request;

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
        // Overriding by classes in Checkout due to their functionality
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

            if (!empty($status['success']))
            {
                $this->checkoutSession->start();
                return $this->_redirect('checkout/onepage/success?utm_nooverride=1');
            }

            $this->checkoutSession->restoreQuote();

            $message = $status['cart_msg'] ?? __('Something went wrong.');
            $this->messageManager->addNoticeMessage($message);

        }
        catch (\Exception $e)
        {
            $this->configRepository->addTolog('error', $e->getMessage());
            $this->messageManager->addExceptionMessage($e, __('There was an error checking the transaction status.'));
        }

        return $this->_redirect('checkout/cart');
    }

    /**
     * Redirect Controller
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

        if ($methodInstance instanceof \GingerPay\Payment\Model\PaymentLibrary) {
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

            if (isset($result['error'])) {
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
            $input =  json_decode(file_get_contents("php://input"), true);
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
