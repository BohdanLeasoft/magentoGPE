<?php

namespace GingerPay\Payment\Model\Builders;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Sales\Setup\SalesSetup;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\UpgradeDataInterface;


class SetupBuilder
{
    /** Transaction id */
    const TRANSACTION_ID = 'gingerpay_transaction_id';

    /**
     * @var SalesSetupFactory
     */
    public $salesSetupFactory;

    /**
     * @var ResourceConnection
     */
    public $resourceConnection;

    /**
     * @param ModuleDataSetupInterface $setup
     */
    public function addTansactionId(ModuleDataSetupInterface $setup)
    {
        /** @var SalesSetup $salesSetup */
        $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);
        $options = ['type' => 'varchar', 'visible' => false, 'required' => false];
        $salesSetup->addAttribute('order', self::TRANSACTION_ID, $options);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    public function addIndex(ModuleDataSetupInterface $setup)
    {
        $setup->getConnection()->addIndex(
            $setup->getTable('sales_order'),
            $this->resourceConnection->getIdxName('sales_order', [self::TRANSACTION_ID]),
            [self::TRANSACTION_ID]
        );
    }
}

