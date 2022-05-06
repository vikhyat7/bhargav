<?php
/**
 * @category Mageants CustomStockStatus
 * @package Mageants_CustomStockStatus
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <info@mageants.com>
 */

namespace Mageants\CustomStockStatus\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * InstallData for Update Database for CustomStockStatus
 */

class InstallSchema implements InstallSchemaInterface
{

    /**
     * install Database for CustomStockStatus
     */

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        // Get manage_stock_status_icons table
        $tableName = $installer->getTable('manage_stock_status_icons');
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
                    'option_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    'Option Id'
                )
                ->addColumn(
                    'icon',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'Icon'
                );
            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
