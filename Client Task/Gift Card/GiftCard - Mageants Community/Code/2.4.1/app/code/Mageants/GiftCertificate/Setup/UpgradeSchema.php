<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCertificate\Setup;
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
            $tableName = $setup->getTable('gift_quote');
              if ($setup->getConnection()->isTableExists($tableName) == true) {
                 $columns = [
                    'timezone' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => false,
                        'afters' => 'expiry_date',
                        'comment' => 'timezone',
                    ],
                    'custom_upload' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => false,
                         'default'=>'0',
                        'afters' => 'timezone',
                        'comment' => 'custom_upload',
                    ],
                    'emailtime' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                        'nullable' => false,
                        'afters' => 'timezone',
                        'comment' => 'emailtime',
                    ],
                    'sendtemplate_id' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => false,
                         'default'=>'0',
                        'afters' => 'emailtime',
                        'comment' => 'sendtemplate_id',
                    ],
                    'order_increment_id' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'afters' => 'sendtemplate_id',
                        'comment' => 'order_increment_id',
                    ],
                    'code_validity' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'afters' => 'order_increment_id',
                        'comment' => 'code_validity',
                    ],
                    
                ];
                $connection = $setup->getConnection();
                 foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
            $tableName = $setup->getTable('gift_code_customer');
            if ($setup->getConnection()->isTableExists($tableName) == true) {
              $columns = [
                    'timezone' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => false,
                        'afters' => 'expiry_date',
                        'comment' => 'timezone',
                    ],
                    'emailtime' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                        'nullable' => false,
                        'afters' => 'timezone',
                        'comment' => 'emailtime',
                    ],
                    'sentgiftcard' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => false,
                        'default' => 0,
                        'afters' => 'emailtime',
                        'comment' => 'sentgiftcard',
                    ],
                 
                ];
                $connection = $setup->getConnection();
                 foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }

            }


            $tableName = $setup->getTable('gift_templates');
            if ($setup->getConnection()->isTableExists($tableName) == true) {
              $columns = [
                    'message' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => false,
                        'afters' => 'status',
                        'comment' => 'timezone',
                    ],
                    'color' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => false,
                        'afters' => 'message',
                        'comment' => 'color',
                    ],
                    'forecolor' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => false,
                        'afters' => 'color',
                        'comment' => 'forecolor',
                    ],

                    'positionleft' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => false,
                        'default' => 0,
                        'afters' => 'forecolor',
                        'comment' => 'positionleft',
                    ],
                    'positiontop' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => false,
                        'default' => 0,
                        'afters' => 'positionleft',
                        'comment' => 'positiontop',
                    ],
                 
                ];
                $connection = $setup->getConnection();
                 foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }

            }

            $tableName = $setup->getTable('gift_code_account');
            if ($setup->getConnection()->isTableExists($tableName) == true) {
              $columns = [
                'discount_type' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => false,
                        'default'=>'',
                        'afters' => 'sendtemplate_id',
                        'comment' => 'Discount Type',
                 ],
                 'custom_upload' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => false,
                         'default'=>'0',
                        'afters' => 'temp_customer_id',
                        'comment' => 'custom_upload',
                    ],
                    'notified' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => false,
                         'default'=>'0',
                        'afters' => 'custom_upload',
                        'comment' => 'notified',
                    ],
                    'sendtemplate_id' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => false,
                         'default'=>'0',
                        'afters' => 'custom_upload',
                        'comment' => 'sendtemplate_id',
                    ],
                    'order_increment_id' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,                        
                        'afters' => 'sendtemplate_id',
                        'comment' => 'order_increment_id',
                    ],
                ];
                $connection = $setup->getConnection();
                 foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }

                if (version_compare($context->getVersion(), "2.0.11", "<")) {
                    $setup->getConnection()->addColumn(
                        $setup->getTable('gift_code_account'),
                        'percentage',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                            'length' => 10,
                            'nullable' => true,
                            'comment' => 'Discount Percentage'
                        ]
                    );
                    $setup->getConnection()->addColumn(
                        $setup->getTable('gift_code_account'),
                        'allow_balance',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                            'length' => 10,
                            'nullable' => true,
                            'comment' => 'Allow Balance'
                        ]
                    );
                }
            }
        $setup->endSetup(); 
    
    }
}
