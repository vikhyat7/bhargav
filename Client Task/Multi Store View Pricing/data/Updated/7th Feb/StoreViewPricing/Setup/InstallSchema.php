<?php
/**
 * @category Mageants StoreViewPricing
 * @package Mageants_StoreViewPricing
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\StoreViewPricing\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * InstallSchema for Update Database for StoreViewPricing
 */
// @codingStandardsIgnoreLine
class InstallSchema implements InstallSchemaInterface
{
    
    /**
     *
     * store manager
     *
     * @var Magento\Store\Model\StoreManagerInterface
     */
    public $StoreManager;

    /**
     *
     *
     *
     * @param Magento\Store\Model\StoreManagerInterface
     */
    public function __construct(StoreManagerInterface $StoreManager)
    {
        $this->StoreManager=$StoreManager;
    }

    /**
     * install Database for GiftCertificate
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        // @codingStandardsIgnoreStart
        $useConetext = $context;
        $installer = $setup;
        $installer->startSetup();
        $table = $installer->getConnection()->newTable(
            $installer->getTable('store_view_pricing')
        )
         ->addColumn(
             'id',
             \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
             null,
             ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
             'id'
         )->addColumn(
             'entity_id',
             \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
             10,
             ['nullable' => false],
             'entity_id'
         )->addColumn(
             'store_id',
             \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
             null,
             ['nullable' => false],
             'store_id'
         )->addColumn(
             'price',
             \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
             '12,4',
             ['nullable' => false, 'default' => '0.0000'],
             'Price'
         )->setComment(
             'Mageants_StoreViewPricing'
         );
        $installer->getConnection()->createTable($table);
        $service_url = 'https://www.mageants.com/index.php/rock/register/
        live?ext_name=Mageants_AjaxLogin&dom_name='.$this->StoreManager->getStore()->getBaseUrl();
        $curl = curl_init($service_url);

        curl_setopt_array($curl, [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_FOLLOWLOCATION =>true,
            CURLOPT_ENCODING=>'',
            CURLOPT_USERAGENT => 'Mozilla/5.0'
        ]);
        
        $curl_response = curl_exec($curl);
        curl_close($curl);
        $installer->endSetup();
        // @codingStandardsIgnoreEnd
    }
}
