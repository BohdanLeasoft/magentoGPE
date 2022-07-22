<?php
namespace GingerPay\Payment\Model\Cron;

use GingerPay\Payment\Model\Builders\RecurringBuilder;
use Magento\Store\Model\Store as StoreModel;
use Magento\Store\Model\App\Emulation;
use \Psr\Log\LoggerInterface;

class CronModel
{
    /**
     * @var StoreModel
     */
    private $storeModel;
    /**
     * @var Emulation
     */
    private $emulation;
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var RecurringBuilder
     */
    protected $recurringBuilder;

    public function __construct(
        LoggerInterface $logger,
        RecurringBuilder $recurringBuilder,
        StoreModel $storeModel,
        Emulation $emulation
    ) {
        $this->logger = $logger;
        $this->recurringBuilder = $recurringBuilder;
        $this->storeModel = $storeModel;
        $this->emulation = $emulation;
    }

    /**
     * Get store Id
     *
     */
    public function getStoreId()
    {
        //default is store code
        $store = $this->storeModel->load('default');
        $storeId =  $store->getId();
        return $storeId;
    }

    public function execute()
    {
        $this->emulation->startEnvironmentEmulation($this->getStoreId(), \Magento\Framework\App\Area::AREA_FRONTEND, true);

        $this->recurringBuilder->mainRecurring();
        $file = fopen(__DIR__."/cronfile2.json", "w+");
        fwrite( $file,  $this->recurringBuilder->saySomething()." The time is ". date("h:i:sa").'  '.$this->getStoreId());

        fclose($file);

        $this->emulation->stopEnvironmentEmulation();
    }
}
