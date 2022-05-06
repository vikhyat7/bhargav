<?php
/**
 * @category Mageants FreeGift
 * @package Mageants_FreeGift
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\FreeGift\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $installer->getConnection()->addColumn(
            $installer->getTable('salesrule'),
            'free_gift_type',
            [
                'type' => 'text',
                'nullable' => false,
                'comment' => 'Type',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('salesrule'),
            'free_gift_sku',
            [
                'type' => 'text',
                'nullable' => false,
                'comment' => 'Free Products',
            ]
        );
        
        $installer->getConnection()->addColumn(
            $installer->getTable('salesrule'),
            'fglabel_upload',
            [
                'type' => 'text',
                'nullable' => true,
                'comment' => 'FreeGift Label Upload',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('salesrule'),
            'select_no_of_freegift',
            [
                'type' => 'text',
                'nullable' => false,
                'comment' => 'Select No of FreeGift',
            ]
        );
        
        $installer->getConnection()->addColumn(
            $installer->getTable('salesrule'),
            'default_selected_freegift',
            [
                'type' => 'text',
                'nullable' => false,
                'comment' => 'Default Selected FreeGift',
            ]
        );
        
        $installer->getConnection()->addColumn(
            $installer->getTable('quote_item'),
            'parent_product_id',
            [
                'type' => 'text',
                'nullable' => false,
                'comment' => 'Parent Product Id',
            ]
        );
        
        $installer->getConnection()->addColumn(
            $installer->getTable('quote_item'),
            'is_free_item',
            [
                'type' => 'text',
                'nullable' => false,
                'comment' => 'Is Free Item',
            ]
        );

        $setup->endSetup();
    }
}
