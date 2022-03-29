<?php
namespace GingerPay\Payment\Model\Cron;

use GingerPay\Payment\Model\Builders\RecurringBuilder;
use \Psr\Log\LoggerInterface;

class CronModel
{
//    /**
//     * @var RecurringBuilder
//     */
//    protected $recurringBuilder;
//
//    /**
//     * CronModel constructor.
//     *
//     * @param RecurringBuilder $recurringBuilder
//     */
//    public function __construct(
//        RecurringBuilder $recurringBuilder,
//    ) {
//        $this->recurringBuilder = $recurringBuilder;
//    }

    protected $logger;
    protected $recurringBuilder;

    public function __construct(LoggerInterface $logger, RecurringBuilder $recurringBuilder) {

        $this->logger = $logger;
        $this->recurringBuilder = $recurringBuilder;
    }

    public function execute()
    {
      //  $this->recurringBuilder->mainRecurring();
        $file = fopen(__DIR__."/cronfile2.json", "w+");
        fwrite( $file,  $this->recurringBuilder->saySomething()." The time is ". date("h:i:sa"));

        fclose($file);

        print_r('I am ginger cron.<br>');//*/1 * * * * root /var/www/html/app/code/Vendor/Module/etc/Som.php
    }
}
