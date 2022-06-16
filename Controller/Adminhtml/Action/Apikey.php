<?php
/**
 * All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace GingerPay\Payment\Controller\Adminhtml\Action;

use GingerPay\Payment\Api\Config\RepositoryInterface as ConfigRepository;
use GingerPay\Payment\Model\Api\GingerClient;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use GingerPay\Payment\Model\Cache\MulticurrencyCacheRepository;

/**
 * Apikey controller class
 */
class Apikey extends Action
{

    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'GingerPay_Payment::config';

    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;
    /**
     * @var GingerClient
     */
    private $client;
    /***
     * @var ConfigRepository
     */
    private $configRepository;
    /**
     * @var MulticurrencyCacheRepository
     */
    public $multicurrencyCacheRepository;

    /**
     * Apikey constructor.
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param ConfigRepository $configRepository
     * @param GingerClient $client
     * @param MulticurrencyCacheRepository $multicurrencyCacheRepository
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        ConfigRepository $configRepository,
        GingerClient $client,
        MulticurrencyCacheRepository $multicurrencyCacheRepository
    ) {
        $this->request = $context->getRequest();
        $this->resultJsonFactory = $resultJsonFactory;
        $this->configRepository = $configRepository;
        $this->client = $client;
        $this->multicurrencyCacheRepository = $multicurrencyCacheRepository;
        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        $results = [];
        $success = true;
        $apiKey = $this->request->getParam('apikey');
        $storeId = $this->request->getParam('store', 0);
        $result = $this->resultJsonFactory->create();

        if (!class_exists('Ginger\ApiClient')) {
            $apiErrorMsg = ['<span class="ginger-error">' . __('Could not load Ginger client!') . '</span>'];
            $result->setData(['success' => false, 'msg' => $apiErrorMsg]);
            return $result;
        }

        try {
            $client = $this->client->get((int)$storeId, $apiKey);
            if (!$client) {
                $results[] = '<span class="ginger-error">' . __('Error! '.$apiKey.'Invalid API Key.') . '</span>';
                $success = false;
            } else {
                $client->getIdealIssuers();
                $this->multicurrencyCacheRepository->set($client);
                $results[] = '<span class="ginger-success">' . __('Success!') . '</span>';
            }
        } catch (\Exception $e) {
            $results[] = '<span class="ginger-error">' . $e->getMessage() . '</span>';
            $this->configRepository->addTolog('error', $e->getMessage());
            $success = false;
        }

        return $result->setData(['success' => $success, 'msg' => implode('<br/>', $results)]);
    }
}
