<?php
/**
 * @category Mageants StoreViewPricing
 * @package Mageants_StoreViewPricing
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\StoreViewPricing\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/*
 * UpgradeSchema for update databse
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
   
    /**
     * Upgrade DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $tableName = $setup->getTable('store_view_pricing');
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $columns = [
                    'special_price' => [
                      'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                      'length' => '12',
                      'nullable' => true,
                      'afters' => 'price',
                      'comment' => 'special_price',
                     ],
                    
                     'cost' => [
                       'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                       'length' => '12',
                       'nullable' => true,
                       'afters' => 'special_price',
                       'comment' => 'cost',
                     ],

                     'special_from_date' => [
                       'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                       'length' => '15',
                       'nullable' => true,
                       'afters' => 'cost',
                       'comment' => 'special_from_date',
                     ],
                     'special_to_date' => [
                       'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                       'length' => '15',
                       'nullable' => true,
                       'afters' => 'special_from_date',
                       'comment' => 'special_to_date',
                     ],

                     'msrp_display_actual_price_type' => [
                       'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                       'length' => '1',
                       'nullable' => false,
                       'default'=>0,
                       'afters' => 'special_to_date',
                       'comment' => 'msrp_display_actual_price_type',
                     ],
                     'msrp' => [
                       'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                       'length' => '12',
                       'nullable' => true,
                       'afters' => 'msrp_display_actual_price_type',
                       'comment' => 'msrp',
                     ],
                     'tier_price' => [
                       'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                       'length' => '2M',
                       'nullable' => true,
                       'afters' => 'msrp',
                       'comment' => 'tier_price',
                     ],
                ];
                
                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }
        $setup->endSetup();
    }
}
