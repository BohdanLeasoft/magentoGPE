<?php
/**
 * All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace GingerPay\Payment\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Setup\SalesSetup;
use Magento\Sales\Setup\SalesSetupFactory;

/**
 * UpgradeData setup class
 */
class UpgradeData implements UpgradeDataInterface
{
    const GINGER_DB_ATTRIBUTE = [
        'gingerpay_transaction_id',
        'gingerpay_vault_token',
        'gingerpay_next_payment_date',
        'gingerpay_recurring_periodicity'
        ];

    /**
     * @var SalesSetupFactory
     */
    private $salesSetupFactory;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * UpgradeData constructor.
     *
     * @param SalesSetupFactory $salesSetupFactory
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        SalesSetupFactory $salesSetupFactory,
        ResourceConnection $resourceConnection
    ) {
        $this->salesSetupFactory = $salesSetupFactory;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $moduleVersion = $context->getVersion();
        foreach (self::GINGER_DB_ATTRIBUTE as $gingerAttribute)
        {
            $this->addTansactionId($setup, $gingerAttribute);
            $this->addIndex($setup, $gingerAttribute);
        }

    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    public function addTansactionId(ModuleDataSetupInterface $setup, $attribute)
    {
        /** @var SalesSetup $salesSetup */
        $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);
        $options = ['type' => 'varchar', 'visible' => false, 'required' => false];
        $salesSetup->addAttribute('order', $attribute, $options);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    public function addIndex(ModuleDataSetupInterface $setup, $attribute)
    {
        $setup->getConnection()->addIndex(
            $setup->getTable('sales_order'),
            $this->resourceConnection->getIdxName('sales_order', [$attribute]),
            [$attribute]
        );
    }
}