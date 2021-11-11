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
    protected $salesSetupFactory;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

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

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     */
    public function upgradeData(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $moduleVersion = $context->getVersion();

        if (version_compare($moduleVersion, '0.9.0', '<'))
        {
            $this->installer->addTansactionId($setup);
            $this->installer->addIndex($setup);
        }
    }
}

