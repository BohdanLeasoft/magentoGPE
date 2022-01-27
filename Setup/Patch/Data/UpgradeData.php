<?php
/**
 * Copyright © 2018 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace GingerPay\Payment\Setup\Patch\Data;

use GingerPay\Payment\Setup\SetupData;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * UpgradeData setup class
 */
class UpgradeData implements DataPatchInterface
{
    /**
     * @var Installer
     */
    private $installer;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var ModuleContextInterface
     */
    private $moduleContext;

    /**
     * UpgradeData constructor.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     * @param SetupData $installer
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        ModuleContextInterface   $context,
        SetupData $installer
    ) {
        echo '_______________________________________Hello_______________________________________________';
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->installer = $installer;
        $this->moduleContext = $context;
    }
    /** Transaction id */
    const TRANSACTION_ID = 'AAAAAAAAgingerpay_transaction_id';

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        echo '_________________________________________________________________apply';
        $this->upgrade($this->moduleDataSetup, $this->moduleContext);
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $options = ['type' => 'varchar', 'visible' => false, 'required' => false];
        $eavSetup->addAttribute('order', self::TRANSACTION_ID, $options);
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * Upgrade function.
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $moduleVersion = $context->getVersion();

        if (version_compare($moduleVersion, '0.9.0', '<')) {
            $this->installer->addTansactionId($setup);
            $this->installer->addIndex($setup);
        }
    }
}
