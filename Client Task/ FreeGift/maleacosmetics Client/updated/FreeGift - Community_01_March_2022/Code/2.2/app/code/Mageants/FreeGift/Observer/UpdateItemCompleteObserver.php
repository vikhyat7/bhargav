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

class UpdateItemCompleteObserver implements ObserverInterface
{
	/**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $_productRepository;
    
	/**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_cart;
    
	/**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    
	/**
     * @var \Mageants\FreeGift\Helper\Data
     */
    protected $_freeGiftHelper;
    
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;
    
    /**
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Mageants\FreeGift\Helper\Data $freeGiftHelper
     */
    public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Mageants\FreeGift\Helper\Data $freeGiftHelper,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    ) {
        $this->_productRepository = $productRepository;
        $this->_cart = $cart;    
        $this->_storeManager = $storeManager;  
		$this->_freeGiftHelper = $freeGiftHelper;
		$this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
	}
	
	public function execute(\Magento\Framework\Event\Observer $observer) 
	{
		$isActive = $this->_freeGiftHelper->getFreeGiftConfig('mageants_freegift/general/active');
		if($isActive) {	
		if($this->_freeGiftHelper->getCartBasedValidRuleOnAddtoCart()){	
			$event = $observer->getEvent();
			
			$getLastItem = $event->getData('item');

			$request = $event->getData('request');
			$params = $request->getParams();

			$parentItemId = $getLastItem->getParentItemId();
			if($parentItemId)
			{
				$lastItemId = $parentItemId;
			} else {
				$lastItemId = $getLastItem->getItemId();
			}
		
			$selectedFreeGiftSkus = '';
			$freeGiftSuperAttrs = '';
			$selectedFreeGiftQty = '';	
			$selectedFreeGiftSkusArray = '';		
			if(isset($params['selected_free_gifts']))
			{
				$selectedFreeGiftSkus = $params['selected_free_gifts'];
			}
			if(isset($params['freegift_super_attribute']))
			{
				$freeGiftSuperAttrs = $params['freegift_super_attribute'];
			}
			if(isset($params['selected_free_gifts_qty']))
			{
				$selectedFreeGiftQty = $params['selected_free_gifts_qty'];
			}
			$storeId = $this->_storeManager->getStore()->getId();

			if($selectedFreeGiftSkus != '')
			{
				$selectedFreeGiftSkusArray = explode(',',$selectedFreeGiftSkus);
			}
			
			$quote = $this->_cart->getQuote();
			$freeQuoteItems = $quote->getItemsCollection();

			$beforeFreeGiftIds = array();

			foreach($freeQuoteItems as $freeItems)
			{
				if($freeItems->getParentProductId() == $params['id'] && $freeItems->getProductType() != 'configurable')
				{
					$beforeFreeGiftIds[] = $freeItems->getItemId();
				}
			}
			foreach($beforeFreeGiftIds as $id)
			{
				$quote->removeItem($id)->save();
			}
			
			foreach($selectedFreeGiftSkusArray as $sku)
			{
				$freeGiftProduct = $this->_productRepository->get($sku); 
				$loadProduct = $this->_productRepository->getById($freeGiftProduct->getId(), false, $storeId, true);
				
				$additionalOptions = [];
				$additionalOptions[] = array(
					'label' => "Free! ",
					'value' => "Product",
				);
				
				$loadProduct->addCustomOption('additional_options', $this->serializer->serialize($additionalOptions));
				
				$freeGiftParams = array(
					'product' => $freeGiftProduct->getId(),
					'qty' => $selectedFreeGiftQty
				);

				if(isset($freeGiftSuperAttrs[$sku]))
				{
					$freeGiftParams = array(
						'product' => $loadProduct->getId(),
						'qty' => $selectedFreeGiftQty,
						'super_attribute' => $freeGiftSuperAttrs[$sku]
					);
				}
				
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
			$this->_cart->save();
			
			$this->_freeGiftHelper->updateConfigFreeGiftItem();
			}	
		}
	}
}
