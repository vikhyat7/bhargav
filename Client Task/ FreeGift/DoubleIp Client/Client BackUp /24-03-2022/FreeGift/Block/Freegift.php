<?php
/**
 * @category Mageants FreeGift
 * @package Mageants_FreeGift
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\FreeGift\Block;

class Freegift extends \Magento\Framework\View\Element\Template
{    
	/**
     * @var \Mageants\FreeGift\Helper\Data
     */
    protected $_freeGiftHelper;

	/**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
	protected $_storeManager;
	
	/**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
	protected $_priceFormat;
    
    /** 
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory  
     */
    protected $productCollectionFactory;
    
    /** 
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface 
     */
    protected $_stockItemRepository;
    
    /** 
     * @var \Magento\Catalog\Helper\Image 
     */
    protected $helperImage;
    
    /** 
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $productVisibility;
	
	/**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Mageants\FreeGift\Helper\Data $freeGiftHelper
     * @param \Magento\Framework\Pricing\Helper\Data $priceFormat
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository
     * @param \Magento\Catalog\Helper\Image $helperImage
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     */
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
        \Mageants\FreeGift\Helper\Data $freeGiftHelper,
        \Magento\Framework\Pricing\Helper\Data $priceFormat,
        \Magento\Checkout\Model\Session $_checkoutSession,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,
        \Magento\Catalog\Helper\Image $helperImage,
        \Magento\Catalog\Helper\Product\Configuration $configurableHelper,
        \Magento\Catalog\Model\Product\Visibility $productVisibility
	) {
		$this->_freeGiftHelper = $freeGiftHelper;
		$this->_storeManager = $context->getStoreManager();
		$this->_priceFormat = $priceFormat;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->_stockItemRepository = $stockItemRepository;
        $this->_checkoutSession = $_checkoutSession;
        $this->helperImage = $helperImage;
        $this->configurableHelper = $configurableHelper;
        $this->productVisibility = $productVisibility;
		parent::__construct($context);
	}
	
	/**
     * @return $this|array
     */	
	public function getValidRules()	{
		return $this->_freeGiftHelper->getValidRules();
	}
	
	/**
     * @return $this|array
     */	
	public function getIsValidRuleForFreeGifts()	{
		$ruleArray = array();
		foreach($this->getValidRules() as $_rule) {
			$freeGiftSku = $_rule->getFreeGiftSku();
			if($freeGiftSku != '') {
				$ruleArray[] = $_rule->getFreeGiftSku();
			}
		}
		return $ruleArray;
	}
	
	/**
     * @param \Magento\SalesRule\Model\Rule $validRule
     * @return $this|array
     */
    public function getProducts(\Magento\SalesRule\Model\Rule $validRule) {
        $products = array();
        $promoSku = $validRule->getFreeGiftSku();
        if (!empty($promoSku)){
            $products = $this->productCollectionFactory->create()
                ->addFieldToFilter('sku', array('in' => explode(",", $promoSku)))
                ->addUrlRewrite()
                ->addAttributeToSelect(array('id', 'name', 'thumbnail', 'price'));
        }		
        return $products;
    }
	
    /**
     * @return \Mageants\FreeGift\Helper\Data
     */
	public function getIsActive() {
        return $this->_freeGiftHelper->getFreeGiftConfig('mageants_freegift/general/active');
    }
  
    /**
     * return freegift banner for product view page
     */  
	public function getFreeGiftBanner() {
		$mediaUrl = $this->_storeManager-> getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		$configBannerUrl = $this->_freeGiftHelper->getFreeGiftConfig('mageants_freegift/general/freegift_banner');
		if($configBannerUrl == '')
		{
			return;
		}
		return $mediaUrl.'freegift/'.$configBannerUrl;
    }
    
    /**
     * check product is InStock or not
     */
    public function isInStockProduct($product) {
        $stockItem = $this->_stockItemRepository->get($product->getId());
		$manageStock = $stockItem->getManageStock();
		$qty = $stockItem->getQty();
		$isInStock = $stockItem->getIsInStock();
		$stockStatus = 0;
		if($manageStock == 1)
		{
			if($qty > 0 && $isInStock == 1)
			{
				$stockStatus = 1;
			}
		}
		else
		{
			if($isInStock == 1)
			{
				$stockStatus = 1;
			}
		}
        return $stockStatus;
    }
    
    /**
     * @param \Magento\SalesRule\Model\Rule $validRule
     * @return mixed
     */
    function getLabelImage(\Magento\SalesRule\Model\Rule $validRule)
    {
		$mediaUrl = $this->_storeManager-> getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $url = null;
        $image = $validRule->getData('fglabel_upload');
        $imageUrl = '';
        if ($image != ''){
            $imageUrl = $mediaUrl.'freegift/product/tmp/'.$image;
        }
        return $imageUrl;
    }
    
    /**
     * @return \Mageants\FreeGift\Helper\Data
     */
    public function isShowFreeGiftText() {
        return $this->_freeGiftHelper->getFreeGiftConfig('mageants_freegift/general/show_freegift_text');
    }
    
    /**
     * @return \Mageants\FreeGift\Helper\Data
     */
    public function getAllSkusText() {
        return $this->_freeGiftHelper->getFreeGiftConfig('mageants_freegift/general/all_skus_text');
    }
    
    /**
     * @return \Mageants\FreeGift\Helper\Data
     */
    public function getSelectedSkusText() {
        return $this->_freeGiftHelper->getFreeGiftConfig('mageants_freegift/general/selected_skus_text');
    }
    
    /**
     * @return \Mageants\FreeGift\Helper\Data
     */
    public function isShowFreeGiftBanner() {
        return $this->_freeGiftHelper->getFreeGiftConfig('mageants_freegift/general/show_freegift_banner');
    }
    
    /**
     * @return \Mageants\FreeGift\Helper\Data
     */
    public function getBannerHeight() {
        return $this->_freeGiftHelper->getFreeGiftConfig('mageants_freegift/general/banner_image_height');
    }
    
    /**
     * @return \Mageants\FreeGift\Helper\Data
     */
    public function getBannerWidth() {
        return $this->_freeGiftHelper->getFreeGiftConfig('mageants_freegift/general/banner_image_width');
    }

   
    
    /**
     * @return \Magento\Catalog\Helper\Image
     */
    public function getImageHelper() {
        return $this->helperImage;
    }
    
    /**
     * @return \Mageants\FreeGift\Helper\Data
     */
    public function getIsShowFreeProduct() {
        return $this->_freeGiftHelper->getFreeGiftConfig('mageants_freegift/general/show_freegift_view_page');
    }
    
    public function getIsShowPrice() {
        return $this->_freeGiftHelper->getFreeGiftConfig('mageants_freegift/general/is_display_freegift_price');
    }
   
    /**
     * return price format
     */
    public function getFormatCurrency($price) {
        return $this->_priceFormat->currency($price, true, false);
    }
    public function getAllItems() {
        return $this->_checkoutSession->getQuote()->getAllItems();
    }

    public function getCustomOptions($item) {
        return $this->configurableHelper->getCustomOptions($item);
    }


    
    /**
     * return array()
     */
    public function getFreeQuoteItemIds() {		
        $freeProductIds = $this->_freeGiftHelper->getFreeQuoteItems();
		$products = $this->productCollectionFactory->create()
		->addFieldToFilter('entity_id', array('in' => $freeProductIds))
		->addAttributeToSelect(array('id', 'name', 'sku', 'price'))
		->setVisibility($this->productVisibility->getVisibleInSiteIds());

		$freeProductSkus = array();
		foreach($products as $product)
		{
			$freeProductSkus[] = $product->getSku();
		}		
		return $freeProductSkus;
    }
}

