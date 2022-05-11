<?php
/**
 * @category Mageants CustomStockStatus
 * @package Mageants_CustomStockStatus
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <info@mageants.com>
 */
 
namespace Mageants\CustomStockStatus\Setup;
 
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
 
    /**
     * @var StoreManagerInterface
     */
    private $StoreManager;
 
    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     * @param StoreManagerInterface $StoreManager
     */
    public function __construct(EavSetupFactory $eavSetupFactory, StoreManagerInterface $StoreManager)
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->StoreManager=$StoreManager;
    }
 
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $context=$context;
        
        /**
         * @var EavSetup $eavSetup
         */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        if (version_compare($context->getVersion(), '2.0.1') < 0) {
            $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'mageants_custom_stock_rule');
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'mageants_custom_stock_rule',
                [
                'group' => 'General',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Custom Stock Status Rule',
                'input' => 'select',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'option',''
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.0.2') < 0) {
            $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'mageants_');
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'mageants_qty_base_rule_status',
                [
                'group' => 'General',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Use Quantity Ranges Based Stock Status',
                'input' => 'boolean',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => true,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'option',''
                ]
            );
        }
    }
}
