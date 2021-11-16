<?php
/**
 * Copyright © 2018 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace GingerPay\Payment\Setup;

use GingerPay\Payment\Redefiners\Setup\SetupRedefiner as SetupData;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * UpgradeData setup class
 */
class UpgradeData extends SetupData implements UpgradeDataInterface
{
    /**
     * @var SetupData
     */
    public $installer;

    /**
     * UpgradeData constructor.
     *
     * @param SetupData $installer
     */
    public function __construct(SetupData $installer)
    {
        $this->installer = $installer;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $this->upgradeData($setup, $context);
    }
}
