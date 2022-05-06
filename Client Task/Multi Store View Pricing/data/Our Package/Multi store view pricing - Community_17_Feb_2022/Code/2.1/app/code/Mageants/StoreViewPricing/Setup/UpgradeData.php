<?php
/**
 * @category Mageants StoreViewConfig
 * @package Mageants_StoreViewConfig
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\StoreViewPricing\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * UpgradeData for install Database for StoreViewPricing
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * eav Setup Factory
     *
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context;
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $entityType = $eavSetup->getEntityTypeId('catalog_product');
            $eavSetup->updateAttribute($entityType, 'special_price', 'is_global', 0);
            $eavSetup->updateAttribute($entityType, 'special_from_date', 'is_global', 0);
            $eavSetup->updateAttribute($entityType, 'special_to_date', 'is_global', 0);
            $eavSetup->updateAttribute($entityType, 'cost', 'is_global', 0);
            $eavSetup->updateAttribute($entityType, 'msrp_display_actual_price_type', 'is_global', 0);
            $eavSetup->updateAttribute($entityType, 'tier_price', 'is_global', 0);
            $eavSetup->updateAttribute($entityType, 'price_type', 'is_global', 0);
            $eavSetup->updateAttribute($entityType, 'price_view', 'is_global', 0);
            $eavSetup->updateAttribute($entityType, 'msrp', 'is_global', 0);
        }
    }
}
