<?php
/**
 * @category Mageants FreeGift
 * @package Mageants_FreeGift
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
 
namespace Mageants\FreeGift\Observer;
use Magento\Framework\Event\ObserverInterface;

class Addcoupon implements ObserverInterface
{
    /**
     * @var \Mageants\FreeGift\Helper\Data
     */
    protected $_freeGiftHelper;   
    
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;
    
    /**
     * @param \Mageants\FreeGift\Helper\Data $freeGiftHelper
     */    
    public function __construct(
        \Magento\SalesRule\Model\Rule $rule,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\SalesRule\Model\CouponFactory $couponFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    ) {
        $this->_rule = $rule;
        $this->_checkoutSession = $checkoutSession;
        $this->_couponFactory = $couponFactory;
        $this->_storeManager = $storeManager;  
        $this->_productRepository = $productRepository;
        $this->_cart = $cart;   
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $request = $observer->getEvent()->getData('request');        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->get('\Magento\Catalog\Helper\Product\Configuration');        
        $coupon = $this->_couponFactory->create();
        $couponcodes = $coupon->load($request->getParam('coupon_code'), 'code');

        if($request->getParam('remove'))
        {
            $couponcodes = $coupon->load($request->getParam('coupon_code'), 'code');                
			if(strpos($this->_checkoutSession->getQuote()->getAppliedRuleIds(), $couponcodes->getRuleId()) !==false)
			{
				$rules = $this->_rule->load($couponcodes->getRuleId());
				if($rules->getSimpleAction()=='add_free_item' && (int)$rules->getCouponType()== 2)
				{				   
				   $qty = $rules->getDiscountAmount();
				   $freeGiftItem = $this->_cart->getQuote()->getAllItems();
				   
				   foreach ($freeGiftItem as $item)
				   {				   
						if(strpos($rules->getFreeGiftSku(), $item->getSku())!==false)
						{
							$this->_cart->removeItem($item->getItemId())->save();
						}
					}					
					$this->_cart->save();
					$this->updateFreeGifts($couponcodes->getRuleId());
					$this->_checkoutSession->setCouponcode(true);
				}				
			}
        }
         
        if(strpos($this->_checkoutSession->getQuote()->getAppliedRuleIds(), $couponcodes->getRuleId())!==false)
        {
            $rules = $this->_rule->load($couponcodes->getRuleId());
            if($rules->getSimpleAction()=='add_free_item' && (int)$rules->getCouponType()== 2)
            {
				$freeGiftSkus = explode(',', $rules->getFreeGiftSku());
				$qty = $rules->getDiscountAmount();
            }
            if (isset($freeGiftSkus)) {
                if(is_array($freeGiftSkus))
                {
                    $storeId = $this->_storeManager->getStore()->getId();
                    
                    foreach($freeGiftSkus as $sku)
                    {
                        $freeGiftItem = $this->_cart->getQuote()->getAllItems();
                        foreach($freeGiftItem as $freeItem)
                        {
                            $options=$helper->getCustomOptions($freeItem);
                            if ($options) 
                            {
                                foreach ($options as $option) {
                                    if ($option['label'] == "Free! " && $option['value'] == "Product") {
                                        return;
                                    }
                                }
                            }
                        }
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
                            'qty' => $qty
                        );
                        $getLastItem = $this->_cart->getItems()->getLastItem();
                        $this->_cart->addProduct($loadProduct,$freeGiftParams); 
                        $parentItemId = $getLastItem->getParentItemId();
                        if($parentItemId)
                        {
                            $lastItemId = $parentItemId;
                        } else {
                            $lastItemId = $getLastItem->getItemId();
                        }
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
                    $this->updateFreeGifts($couponcodes->getRuleId());
                }
               
            }
		}
	}

	public function updateFreeGifts($ruleid)
	{
		$allitems = $this->_cart->getItems();
		$rules = $this->_rule->load($ruleid);
		foreach ($allitems as $cartitems) 
		{
			if(strpos($rules->getFreeGiftSku(), $cartitems->getSku())!==false)
			{
				$cartitems->setPrice(0);
				$cartitems->setIsFreeItem(1);
				$cartitems->setPrice(0);
				$cartitems->setBasePrice(0);
				$cartitems->setCustomPrice(0);
				$cartitems->setOriginalCustomPrice(0);
				$cartitems->setPriceInclTax(0);
				$cartitems->setBasePriceInclTax(0);
				$cartitems->getProduct()->setIsSuperMode(true);
				$cartitems->save();
				$this->_cart->save();
		   }
		}            
		return;       
	}	
}

