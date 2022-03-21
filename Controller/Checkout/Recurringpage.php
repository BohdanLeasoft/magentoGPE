<?php

declare(strict_types=1);

namespace GingerPay\Payment\Controller\Checkout;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page;

class Recurringpage extends Action
{
    /**
     * @var string
     */
    protected $_block = 'ginger.checkout.recurringpage';

    public function setMessage($result)
    {
        switch ($result)
        {
            case 'success': return "Subscription successfully canceled!"; break;
            case 'deleted': return "Subscription already canceled!"; break;
            case 'error': return "Not found such subscription!"; break;
        }
    }

    public function execute()
    {
        $result = $this->getRequest()->getParam('result');
        /** @var Page $resultPage */

        $page = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        /** @var Template $block */
        $block = $page->getLayout()->getBlock($this->_block);

        $block->setData('recurring_message', $this->setMessage($result));

        return $page;
    }
}
