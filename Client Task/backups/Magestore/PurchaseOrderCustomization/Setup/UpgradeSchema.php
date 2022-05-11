<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderCustomization\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class UpgradeSchema
 *
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD)
 * @package Magestore\PurchaseOrderCustomization\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * @param SchemaSetupInterface $setup
     *
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {

            $setup->getConnection()->dropTable($setup->getTable('os_supplier_transactions'));

            /**
             * create os_supplier_product table
             */
            $table = $installer->getConnection()
                ->newTable($installer->getTable('os_supplier_transactions'))
                ->addColumn(
                    'supplier_transaction_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    11,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Supplier Transaction Id'
                )->addColumn(
                    'supplier_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    11,
                    ['default' => null, 'unsigned' => true],
                    'Supplier Id'
                )->addColumn(
                    'transaction_created_date',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                    null,
                    ['nullable' => true],
                    'Transaction Created Date'
                )->addColumn(
                    'transaction_date',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                    null,
                    ['nullable' => true],
                    'Transaction Date'
                )->addColumn(
                    'type',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    10,
                    ['default' => null],
                    'Transaction Type'
                )->addColumn(
                    'doc_no',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['default' => null],
                    'Doc No.'
                )->addColumn(
                    'chq_no',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['default' => null],
                    'Chq No.'
                )->addColumn(
                    'amount',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '12,4',
                    ['default' => null],
                    'Amount'
                )->addColumn(
                    'currency',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    45,
                    [],
                    'Currency'
                )->addColumn(
                    'description_option',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    50,
                    [],
                    'Description Option'
                )
                ->addIndex(
                    $installer->getIdxName('os_supplier_transactions', ['supplier_id']),
                    ['supplier_id']
                )->addForeignKey(
                    $installer->getFkName(
                        'os_supplier_transactions',
                        'supplier_id',
                        'os_supplier',
                        'supplier_id'
                    ),
                    'supplier_id',
                    $installer->getTable('os_supplier'),
                    'supplier_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                );
            $installer->getConnection()->createTable($table);
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            if (!$setup->getConnection()->tableColumnExists(
                $setup->getTable('os_return_order_item'),
                'cost')
            ) {
                $setup->getConnection()->addColumn(
                    $setup->getTable('os_return_order_item'),
                    'cost',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'nullable' => true,
                        'default' => 0,
                        'length' => '12,4',
                        'comment' => 'Cost'
                    ]
                );
            }
        }
        $installer->endSetup();
    }
}
