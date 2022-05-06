<?php
/**
 * @category Mageants FreeGift
 * @package Mageants_FreeGift
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\FreeGift\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PublicCookieMetadata;
use Magento\Framework\Stdlib\CookieManagerInterface;

class CartPageLoadObserver implements ObserverInterface
{ 
    protected $_cart;
    
    /**
     * @var \Mageants\FreeGift\Helper\Data
     */
    protected $_freeGiftHelper; 
    protected $_checkoutSession; 
    protected $_ruleCollectionFactory;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    
   	/**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $_productRepository;
    
    protected $_responseFactory;


    protected $_url;
    protected $freeQty;
    /**
     * Plugin constructor.
     *
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Checkout\Model\Cart $cart,
        \Mageants\FreeGift\Helper\Data $freeGiftHelper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository,
         CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        SessionManagerInterface $sessionManager
    ) {
        $this->_cart = $cart;   
  		$this->_freeGiftHelper = $freeGiftHelper;
  		$this->_checkoutSession = $checkoutSession;
  		$this->_ruleCollectionFactory = $ruleCollectionFactory;
  		$this->_storeManager = $storeManager;  
        $this->_url = $url;
        $this->_responseFactory = $responseFactory;
  		$this->_productRepository = $productRepository;
  		$this->freeQty = 0;
  		$this->_cookieManager = $cookieManager;
        $this->_cookieMetadataFactory = $cookieMetadataFactory;
        $this->_sessionManager = $sessionManager;
	}
	
	public function execute(\Magento\Framework\Event\Observer $observer) 
	{
		$isAllowMultiples = $this->_freeGiftHelper->getAllowMultiples();
		$validation = $this->_freeGiftHelper->getCartBasedValidRuleOnAddtoCart();
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$helper = $objectManager->get('\Magento\Catalog\Helper\Product\Configuration');
		$subTotals = $this->_cart->getQuote()->getTotals()['subtotal']['value'];
		$totalQty = $this->_cart->getQuote()->getItemsQty();
		$appliedRuleId = explode(',',$this->_checkoutSession->getQuote()->getAppliedRuleIds());
		$freeGiftItem = $this->_cart->getQuote()->getAllItems();
		$super_attribute = array();
		$validRules = $this->_ruleCollectionFactory->create()
			->addFieldToFilter('rule_id', ['in' => $appliedRuleId]);

			$allRules = $this->_ruleCollectionFactory->create();
			
			foreach ($allRules as $value) 
			{
				if ($value->getSimpleAction() == 'add_free_item') 
				{
					$getConditios = unserialize($value->getConditionsSerialized());

					if(isset($getConditios['conditions']))
					{
						foreach($getConditios['conditions'] as $cond){
							if (in_array(false, $validation['valid_qty_subtotal'])) {
								
								foreach($freeGiftItem as $freeItem)
					            {
					            	$options=$helper->getCustomOptions($freeItem);
					            	if ($options) 
					            	{
						            	foreach ($options as $option) {
						            		if ($option['value'] == "Free Product") 
						            		{
						            			$this->_cart->removeItem($freeItem->getItemId());
						            		}
						            		$this->_cart->save();
						            	}
					            	}
					            }
							}
							else{
								if ($cond['value'] && $isAllowMultiples == 1) 
								{
									$this->freeQty = $subTotals/$cond['value'];
								}
							}
						}
					}
				}
			}

		if (count($this->_cart->getQuote()->getAllItems()) < 1 && $this->_cart->getItemsCount() > 0) 
		{
			$this->_cart->truncate();
			$this->_cart->save();
			$RedirectUrl = $this->_url->getUrl('checkout/cart/index');
	        $this->_responseFactory->create()->setRedirect($RedirectUrl)->sendResponse();
	         die();
		}
		if(!count($this->_cart->getQuote()->getAllItems())){
			return;
		}
		foreach($validRules as $_rule):
			if($_rule->getSimpleAction() == 'add_free_item' && (int)$_rule->getCouponType()!== 2):
				$getConditiosSerialize = unserialize($_rule->getConditionsSerialized());
				if(isset($getConditiosSerialize['conditions']))
				{
					foreach($getConditiosSerialize['conditions'] as $conditions):
						
						if($conditions['attribute'] == 'base_subtotal')
						{
							$this->addProductToCart($_rule->getFreeGiftSku(),$_rule->getDiscountAmount());
						}
						if($conditions['attribute'] == 'total_qty')
						{
							$this->addProductToCart($_rule->getFreeGiftSku(),$_rule->getDiscountAmount());
						}	
					endforeach;
				}
			endif;
		endforeach;
	}
	public function addProductToCart($skus,$qty)
	{
		
		$isAllowMultiples = $this->_freeGiftHelper->getAllowMultiples();
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$addedSkus = array();
		$helper = $objectManager->get('\Magento\Catalog\Helper\Product\Configuration');
		$isActive = $this->_freeGiftHelper->getFreeGiftConfig('mageants_freegift/general/active');
		$super_attribute = array();
		$freeGiftQty = 0;
		if($isActive) {
			$allitems = $this->_cart->getQuote()->getAllItems();
			foreach ($allitems as $items) {
	        	$options=$helper->getCustomOptions($items);
	        	if ($options) 
	        	{
	            	foreach ($options as $option) {
	            		if ($option['value'] == "Free Product") 
	            		{
							if(strpos($skus, $items->getSku())!==false ){
								if ($isAllowMultiples == 1) 
								{
									$freeGiftItem = $this->_cart->getQuote()->getAllItems();
									foreach($freeGiftItem as $freeItem)
						            {
						            	$options=$helper->getCustomOptions($freeItem);
						            	if ($options) 
						            	{
							            	foreach ($options as $option) {
							            		if ($option['value'] == "Free Product") 
							            		{
							            			$freeGiftQty = intval($this->freeQty);
							            			$freeItem->setQty($freeGiftQty)->save();
							            		}
							            	}
						            	}
						            }
									return;
								}
								return;
							}
	            		}
	            	}
	        	}
			}
			$storeId = $this->_storeManager->getStore()->getId();
			$freeGiftSkus = explode(',',$skus);
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$productTypeInstance = $objectManager->get('Magento\ConfigurableProduct\Model\Product\Type\Configurable');
			foreach($freeGiftSkus as $sku)
			{
				if ($isAllowMultiples == 1 && $this->freeQty != 0) {
					$qty = intval($this->freeQty);
				}
				$freeGiftProduct = $this->_productRepository->get($sku); 
				$loadProduct = $this->_productRepository->getById($freeGiftProduct->getId(), false, $storeId, true);

				
         		$productAttributeOptions = $productTypeInstance->getConfigurableAttributesAsArray($loadProduct);
         		$freegiftitems_quote = $this->_cart->getItems();
				foreach ($freegiftitems_quote as $freequoteitems) {
					//print_r(get_class_methods($freequoteitems));
					if($freequoteitems->getIsFreeItem()){
						$freeProduct = $this->_productRepository->get($freequoteitems->getSku()); 
						foreach ($freeProduct->getAttributes() as $freeAttributes) {
							if(array_key_exists($freeAttributes->getAttributeId(), $productAttributeOptions)){
								$super_attribute[$freeAttributes->getAttributeId()]=$freeProduct->getData($freeAttributes->getAttributeCode());
							}
							
							
						}
					}
					
				}
				$additionalOptions = [];
				$additionalOptions[] = array(
					'label' => "Free! ",
					'value' => "Free Product",
				);
				$allItems = $this->_cart->getQuote()->getAllItems();
				foreach ($allItems as $addedItems) {
						if($addedItems->getIsFreeItem()){
							$addedSkus[$addedItems->getSku()] = true;
						}

						if($addedItems->getPrice()){
							$lastItemId = $addedItems->getId();
						}
					}
				
				$loadProduct->addCustomOption('additional_options', serialize($additionalOptions));

				if(in_array($freeGiftProduct->getSku(), $addedSkus)){
					continue;
				}
				if($this->_cookieManager->getCookie('freegift_super_attribute')){
					$sattribute = unserialize($this->_cookieManager->getCookie('freegift_super_attribute'));
					foreach ($sattribute as $skey => $svalue) {
						$freeGiftProduct = $this->_productRepository->get($skey); 
						$additionalOptions = [];
						$additionalOptions[] = array(
							'label' => "Free! ",
							'value' => "Free Product",
						);
						$freeGiftProduct->addCustomOption('additional_options', serialize($additionalOptions));
						$freeGiftParams = array(
							'product' => $freeGiftProduct->getId(),
							'qty' => $qty,
							'super_attribute'=>$svalue
						);
						$this->_cart->addProduct($freeGiftProduct,$freeGiftParams); 	
						$lastFreeItem = $this->_cart->getItems()->getLastItem();
						$lastFreeItem->setParentProductId($lastItemId);
						$lastFreeItem->setIsFreeItem(1);
						$lastFreeItem->setPrice(0);
						$lastFreeItem->setBasePrice(0);
						$lastFreeItem->setCustomPrice(0);
						$lastFreeItem->setOriginalCustomPrice(0);
						$lastFreeItem->setPriceInclTax(0);
						$lastFreeItem->setBasePriceInclTax(0);
						$lastFreeItem->getProduct()->setIsSuperMode(true);
						$lastFreeItem->save();
						$this->_cart->removeItem($freeGiftProduct->getId());
						$this->_cart->save();
					}

					
				}else{
					$freeGiftParams = array(
						'product' => $freeGiftProduct->getId(),
						'qty' => $qty,
						'super_attribute'=>$super_attribute
					);
					$this->_cart->addProduct($loadProduct,$freeGiftParams); 
						$lastFreeItem = $this->_cart->getItems()->getLastItem();
						$lastFreeItem->setParentProductId($lastItemId);
						$lastFreeItem->setIsFreeItem(1);
						$lastFreeItem->setPrice(0);
						$lastFreeItem->setBasePrice(0);
						$lastFreeItem->setCustomPrice(0);
						$lastFreeItem->setOriginalCustomPrice(0);
						$lastFreeItem->setPriceInclTax(0);
						$lastFreeItem->setBasePriceInclTax(0);
						$lastFreeItem->getProduct()->setIsSuperMode(true);
						$lastFreeItem->save();
				}		
				
				
				
			}	
			
			$this->_cart->save();		
		}
	}
}
