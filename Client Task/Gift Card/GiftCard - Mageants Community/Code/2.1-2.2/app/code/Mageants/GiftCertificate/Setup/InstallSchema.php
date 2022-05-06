<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCertificate\Setup;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * InstallSchema for Update Database for GiftCertificate
 */
class InstallSchema implements InstallSchemaInterface
{
    
    /** 
     *
     * store manager
     * 
     * @var Magento\Store\Model\StoreManagerInterface
     */
     protected $StoreManager;     

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
        $installer = $setup;
        $installer->startSetup();
        $table = $installer->getConnection()->newTable(
        $installer->getTable('gift_code_set'))
         ->addColumn(
            'code_set_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'code_set_id'
        )->addColumn(
            'code_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'code_title'
        )->addColumn(
            'code_pattern',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'code_pattern'
        )->addColumn(
            'code_qty',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'status'
        )->addColumn(
            'unused_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'code_pattern'
        )->addIndex(  
                      $setup->getIdxName(  
                           $setup->getTable('code_id'),  
                           ['code_title', 'code_pattern'],  
                           \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT  
                      ),  
                      ['code_title', 'code_pattern'],
                      ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT]
        ) ->setComment(
            'Mageants_GiftCertificate'
        );
        $installer->getConnection()->createTable($table);
        
        $table = $installer->getConnection()->newTable(
        $installer->getTable('gift_code_list'))
         ->addColumn(
            'code_list_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'code_list_id'
        )->addColumn(
            'code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'code'
        )->addColumn(
            'allocate',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default'=>'0'],
            'allocate'
        )->addColumn(
            'code_set_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false,'default'=>'0'],
            'code_set_id'
        )->addIndex(  
                      $setup->getIdxName(  
                           $setup->getTable('code_list_id'),  
                           ['code'],  
                           \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT  
                      ),  
                      ['code'],
                      ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT]
        ) ->setComment(
            'Mageants_GiftCertificate'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
        $installer->getTable('gift_code_account'))
         ->addColumn(
            'account_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'account_id'
        )->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'order_id'
        )->addColumn(
            'gift_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'gift_code'
        )->addColumn(
            'categories',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'categories'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default'=>'0'],
            'status'
        )->addColumn(
            'website',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false,'default'=>'0'],
            'website'
        )->addColumn(
            'initial_code_value',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'initial_code_value'
        )->addColumn(
            'current_balance',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'current_balance'
        )->addColumn(
            'expire_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
            null,
            ['nullable' => false],
            'expire_at'
        )->addColumn(
            'comment',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'comment'
        )->addColumn(
            'template',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'template'
        )->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'customer_id'
        )->addColumn(
            'discount_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Discount Type'
            /*->addColumn(
            'avail_bal',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Available Bal'*/
        )->addColumn(
            'temp_customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'temp_customer_id'
        )->addIndex(  
                      $setup->getIdxName(  
                           $setup->getTable('account_id'),  
                           ['order_id', 'initial_code_value','current_balance','comment','gift_code'],  
                           \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT  
                      ),  
                      ['order_id', 'initial_code_value','current_balance','comment','gift_code'],
                      ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT]
        ) ->setComment(
            'Mageants_GiftCertificate'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
        $installer->getTable('gift_code_customer'))
         ->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'customer_id'
        )->addColumn(
            'temp_customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'temp_customer_id'
        )->addColumn(
            'code_value',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'code_value'
        )->addColumn(
            'card_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default'=>'0'],
            'card_type'
        )->addColumn(
            'sender_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false,'default'=>'0'],
            'sender_name'
        )->addColumn(
            'sender_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'sender_email'
        )->addColumn(
            'recipient_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'recipient_name'
        )->addColumn(
            'recipient_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'recipient_email'
        )->addColumn(
            'date_of_delivery',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
            null,
            ['nullable' => false],
            'date_of_delivery'
        )->addColumn(
            'time_zone',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'time_zone'
        )->addColumn(
            'message',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'message'
        )->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'order_id'
        )->addIndex(  
                      $setup->getIdxName(  
                           $setup->getTable('customer_id'),  
                           ['code_value', 'sender_name','sender_email','recipient_name','recipient_email','time_zone','message'],  
                           \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT  
                      ),  
                      ['code_value', 'sender_name','sender_email','recipient_name','recipient_email','time_zone','message'],
                      ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT]
        ) ->setComment(
            'Mageants_GiftCertificate'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
        $installer->getTable('gift_quote'))
         ->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'id'
        )->addColumn(
            'gift_card_value',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'gift_card_value'
        )->addColumn(
            'card_types',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'gift_types'
        )->addColumn(
            'categories',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'categories'
        )->addColumn(
            'template_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'template_id'
        )->addColumn(
            'sender_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'sender_name'
        )->addColumn(
            'sender_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'sender_email'
        )->addColumn(
            'recipient_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'recipient_name'
        )->addColumn(
            'recipient_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'recipient_email'
        )->addColumn(
            'date_of_delivery',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
            null,
            ['nullable' => false],
            'date_of_delivery'
        )->addColumn(
            'message',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'message'
        )->addColumn(
            'quote_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'quote_id'
        )->addColumn(
            'product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'product_id'
        )->addColumn(
            'codesetid',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'codesetid'
        )->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'customer_id'
        ) ->addColumn(
            'expiry_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
            null,
            ['nullable' => false],
            'expiry_date'
        )->addColumn(
            'temp_customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'temp_customer_id'
        )->setComment(
            'Mageants_GiftCertificate'
        );
        $installer->getConnection()->createTable($table);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager =$objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $websiteId = $storeManager->getWebsite()->getWebsiteId();
        $store = $storeManager->getStore();
        $storeId = $store->getStoreId();
        $rootNodeId = $store->getRootCategoryId();
        $rootCat = $objectManager->get('Magento\Catalog\Model\Category');
        $table = $installer->getConnection()->newTable(
        $installer->getTable('gift_templates'))
         ->addColumn(
            'image_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'image_id'
        )->addColumn(
            'image',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'image'
        )->addColumn(
            'image_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default'=>'0'],
            'image_title'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false,'default'=>'0'],
            'sender_name'
        )->addColumn(
            'code_position',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'code_position'
        )->addIndex(  
                      $setup->getIdxName(  
                           $setup->getTable('image_id'),  
                           ['image', 'image_title','status'],  
                           \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT  
                      ),  
                      ['image', 'image_title','status'],
                      ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT]
        ) ->setComment(
            'Mageants_GiftCertificate'
        );
        $installer->getConnection()->createTable($table);
        
     try
        {
            $cat_info = $rootCat->load($rootNodeId); 
            $categoryfactory=$objectManager->get('\Magento\Catalog\Model\CategoryFactory');
            $category = $objectManager->create('Magento\Catalog\Model\Category');
            $cate=$category->getCollection()->addAttributeToFilter('url_key','giftcard')->getFirstItem();
            
            if(!$cate->getId())
            {
                $categoryTmp = $categoryfactory->create();
                $categoryTmp->setName('Gift Card');
                $categoryTmp->setIsActive(false);
                $categoryTmp->setUrlKey('giftcard');
                $categoryTmp->setData('description', 'description');
                $categoryTmp->setParentId($rootNodeId);
                $categoryTmp->setStoreId($storeId);
                $categoryTmp->setPath($rootCat->getPath());
                $categoryTmp->save();
            }     
       }
       catch(Exception $e){
           echo "Category exist already";
       }
       $service_url = 'https://www.mageants.com/index.php/rock/register/live?ext_name=Mageants_AjaxLogin&dom_name='.$this->StoreManager->getStore()->getBaseUrl();
        $curl = curl_init($service_url);     

        curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_FOLLOWLOCATION =>true,
            CURLOPT_ENCODING=>'',
            CURLOPT_USERAGENT => 'Mozilla/5.0'
        ));
        
        $curl_response = curl_exec($curl);
        curl_close($curl);
        $installer->endSetup();
    }
}
