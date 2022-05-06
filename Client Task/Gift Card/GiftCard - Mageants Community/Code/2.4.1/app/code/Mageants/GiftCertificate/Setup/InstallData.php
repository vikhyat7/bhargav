<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCertificate\Setup;
use Magento\Customer\Model\Customer;
use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
/**
 * InstallData for install Database for GiftCertificate
 */
class InstallData implements InstallDataInterface
{
    /**
     * eav Setup Factory
     *
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $eavSetupFactory;
    
    /**
     * sales Setup Factory
     *
     * @var \Magento\Sales\Setup\SalesSetupFactory
     */
    protected $salesSetupFactory;
    
    /**
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     * @param \Magento\Sales\Setup\SalesSetupFactory $salesSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory,SalesSetupFactory $salesSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
    }

    /**
     * install Database for GiftCertificate
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'gifttype',
            [
                'group' => 'Gift Card Informtion',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'sort_order' => 50,
                'label' => 'Gift Type',
                'input' => 'select',
                'class' => 'required',
                'source' => 'Mageants\GiftCertificate\Model\Config\Source\Gifttype',
                'global' =>  \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'visible' => true,
                'required' => true,
                'user_defined' => false,
                'default' => '',
                'searchable' => true,
                'filterable' => true,
                'comparable' => true,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to'=>'giftcertificate'
            ]
        );
        $eavSetup->addAttribute(
			\Magento\Catalog\Model\Product::ENTITY,
            'giftcerticodeset',
            [
                'group' => 'Gift Card Informtion',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'sort_order' => 60,
                'label' => 'Select Gift Card Code Set',
                'input' => 'select',
                'class' => 'required',
                'source' => 'Mageants\GiftCertificate\Model\Config\Source\Codeset',
                'global' =>  \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'visible' => true,
                'required' => true,
                'user_defined' => false,
                'apply_to' => 'giftcertificate',
                'used_in_product_listing' => true,
                'visible_on_front' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'searchable' => true,
                'filterable' => true,
                'required' => true,
                'comparable' => true,
                'default' => '',
                'apply_to'=>'giftcertificate',
            ]
        ); 
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'minprice',
            [
                'group' => 'Gift Card Informtion',
                'type' => 'int',
                'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Boolean',
                'frontend' => '',
                'frontend_class' => 'validate-number',
                'label' => 'Set Minimum Price of GiftCard',
                'input' => 'text',
                'class' => '',
                'source' => 'Magento\Catalog\Model\Product\Attribute\Source\Boolean',
                'global' => true,
                'visible' => true,
                'required' => true,
                'user_defined' => false,
                'default' => '',
                'apply_to' => '',
                'input_renderer' => 'Magento\GiftMessage\Block\Adminhtml\Product\Helper\Form\Config',
                'visible_on_front' => false,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'apply_to'=>'giftcertificate',
            ]
        ); 
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'maxprice',
            [
                'group' => 'Gift Card Informtion',
                'type' => 'int',
                'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Boolean',
                'frontend' => '',
                'frontend_class' => 'validate-number',
                'label' => 'Set Maximum Price of GiftCard',
                'input' => 'text',
                'class' => '',
                'source' => 'Magento\Catalog\Model\Product\Attribute\Source\Boolean',
                'global' => true,
                'visible' => true,
                'required' => true,
                'user_defined' => false,
                'default' => '',
                'apply_to' => '',
                'input_renderer' => 'Magento\GiftMessage\Block\Adminhtml\Product\Helper\Form\Config',
                'visible_on_front' => false,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'apply_to'=>'giftcertificate',
            ]
        ); 
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'validity',
            [
                'group' => 'Gift Card Informtion',
                'type' => 'int',
                'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Boolean',
                'frontend' => '',
                'frontend_class' => 'validate-number',
                'label' => 'Gift Card Validity',
                'input' => 'text',
                'class' => '',
                'source' => 'Magento\Catalog\Model\Product\Attribute\Source\Boolean',
                'global' => true,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'apply_to' => '',
                'input_renderer' => 'Magento\GiftMessage\Block\Adminhtml\Product\Helper\Form\Config',
                'visible_on_front' => false,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'apply_to'=>'giftcertificate',
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'allowmessage',
            [
                'group' => 'Gift Card Informtion',
                'type' => 'int',
                'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Boolean',
                'frontend' => '',
                'label' => 'Allow Message',
                'input' => 'select',
                'class' => '',
                'source' => 'Mageants\GiftCertificate\Model\Config\Source\Message',
                'global' => true,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'apply_to' => '',
                'input_renderer' => 'Magento\GiftMessage\Block\Adminhtml\Product\Helper\Form\Config',
                'visible_on_front' => false,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'apply_to'=>'giftcertificate',
            ]
        );        
        
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'giftimages',
            [
                'group' => 'Gift Card Informtion',
                'type' => 'text',
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'frontend' => '',
                'label' => 'Choose gift certificate images ',
                'input' => 'multiselect',
                'class' => '',
                'source' => 'Mageants\GiftCertificate\Model\Config\Source\Giftimages',
                'global' => true,
                'visible' => true,
                'required' => true,
                'user_defined' => false,
                'default' => '',
                'apply_to' => '',
                'input_renderer' => 'Magento\GiftMessage\Block\Adminhtml\Product\Helper\Form\Config',
                'visible_on_front' => false,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'apply_to'=>'giftcertificate',
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'category',
            [
                'group' => 'Gift Card Informtion',
                'type' => 'text',
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'frontend' => '',
                'label' => 'Choose Categories (To apply this certificate)',
                'input' => 'multiselect',
                'class' => '',
                'source' => 'Mageants\GiftCertificate\Model\Config\Source\Categories',
                'global' => true,
                'visible' => true,
                'required' => true,
                'user_defined' => false,
                'default' => '',
                'apply_to' => '',
                'input_renderer' => 'Magento\GiftMessage\Block\Adminhtml\Product\Helper\Form\Config',
                'visible_on_front' => false,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'apply_to'=>'giftcertificate'
            ]
        );

        $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);
        /**
         * Remove previous attributes
         */
        $attributes =['order_gift'];
        foreach ($attributes as $attr_to_remove){
            $salesSetup->removeAttribute(\Magento\Sales\Model\Order::ENTITY,$attr_to_remove);
        }

        /**
         * Add 'NEW_ATTRIBUTE' attributes for order
         */
        $options = ['type' => 'decimal', 'length'=> '10,4', 'visible' => false, 'required' => false];
        $salesSetup->addAttribute('order', 'order_gift', $options);
    	
        $fieldList = [
           'price',
          'tier_price',
          'cost',
        ];
        foreach ($fieldList as $field) {
	        $applyTo = explode(
	        ',',
	            $eavSetup->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $field, 'apply_to')
	        );
	        if (!in_array('giftcertificate', $applyTo)) {
	        $applyTo[] = 'giftcertificate';
	        $eavSetup->updateAttribute(
	            \Magento\Catalog\Model\Product::ENTITY,
	            $field,
	            'apply_to',
	            implode(',', $applyTo) );
	        }
   		}
    }
}
