<?php

declare(strict_types=1);

namespace GingerPay\Payment\Controller\Checkout;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page;
use GingerPay\Payment\Model\Builders\RecurringBuilder;
use GingerPay\Payment\Model\Helper\CacheControl;

class Recurringpage extends Action
{
    /**
     * @var string
     */
    protected $_block = 'ginger.checkout.recurringpage';
    /**
     * @var RecurringBuilder
     */
    private $recurringBuilder;
    /**
     * @var CacheControl
     */
    private $cacheControl;

    public function __construct(
        Context $context,
        CacheControl $cacheControl,
        RecurringBuilder $recurringBuilder
    ) {
        parent::__construct($context);
        $this->cacheControl = $cacheControl;
        $this->recurringBuilder = $recurringBuilder;
    }

    public function setMessage($result)
    {
        switch ($result) {
            case 'success': return __("Subscription successfully canceled!"); break;
            case 'deleted': return __("Subscription already canceled or the link to cancel has changed!"); break;
            case 'error': return __("Not found such subscription!"); break;
            case 'subscriptions': return __("Active subscriptions!"); break;
        }
    }

    public function execute()
    {
        $activeSubscriptions = $this->getRequest()->getParam('active_subscriptions');
        $unsubscribeConfirmation = $this->getRequest()->getParam('unsubscribe_confirmation');
        $result = $this->getRequest()->getParam('result');
        /** @var Page $resultPage */

        $page = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        /** @var Template $block */
        $block = $page->getLayout()->getBlock($this->_block);

        if ($activeSubscriptions) {
            $subscriptionInfo = $this->recurringBuilder->getActiveSubscriptionsInfo($activeSubscriptions);
            if (!$subscriptionInfo) {
                $result = 'error';
            }
            $block->setData('active_subscriptions_info', json_encode($subscriptionInfo));
        }

        if ($result == 'error' || $result == 'deleted') {
            $unsubscribeConfirmation = null;
        }

        if ($unsubscribeConfirmation) {
            $block->setData('cancel_url', $this->recurringBuilder->recurringHelper->getRecurringCancelUrlByOrderId($activeSubscriptions));
            $block->setData('unsubscribe_confirmation', $unsubscribeConfirmation);
        }

        $block->setData('recurring_message', $this->setMessage($result));
        //$this->cacheControl->flushCache();
        return $page;
    }
}
