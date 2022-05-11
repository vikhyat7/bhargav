<?php
/**
 * @category Mageants CustomStockStatus
 * @package Mageants_CustomStockStatus
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <info@mageants.com>
 */
 
namespace Mageants\CustomStockStatus\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '2.0.1', '<')) {
             // Get manage_stock_status_icons table
            $tableName = $installer->getTable('manage_stock_status_range');
        // Check if the table already exists
            if ($installer->getConnection()->isTableExists($tableName) != true) {
                // Create manage_stock_status_icons table
                $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'ID'
                )
                    ->addColumn(
                        'from',
                        Table::TYPE_TEXT,
                        null,
                        ['nullable' => false, 'default' => ''],
                        'From'
                    )
                    ->addColumn(
                        'to',
                        Table::TYPE_TEXT,
                        null,
                        ['nullable' => false, 'default' => ''],
                        'To'
                    )
                    ->addColumn(
                        'option_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false],
                        'Option Id'
                    )
                    ->addColumn(
                        'rule_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false],
                        'Rule Id'
                    );
                $installer->getConnection()->createTable($table);
            }
        }

        $installer->endSetup();
    }
}
