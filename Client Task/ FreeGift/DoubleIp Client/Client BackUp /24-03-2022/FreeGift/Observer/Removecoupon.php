<?php
/**
 * @category Mageants FreeGift
 * @package Mageants_FreeGift
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
 
namespace Mageants\FreeGift\Observer;

use Magento\Framework\Event\ObserverInterface;

class Removecoupon implements ObserverInterface
{
    /**
     * @var \Mageants\FreeGift\Helper\Data
     */
    protected $_freeGiftHelper;   
    
    /**
     * @param \Mageants\FreeGift\Helper\Data $freeGiftHelper
     */    
    public function __construct(
        \Magento\SalesRule\Model\Rule $rule,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\SalesRule\Model\CouponFactory $couponFactory,
        \Magento\Checkout\Model\Cart $cart
    ) {
        $this->_rule = $rule;
        $this->_checkoutSession = $checkoutSession;
        $this->couponFactory = $couponFactory;
        $this->_cart = $cart;   
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $request = $observer->getEvent()->getData('request');

        if($request->getParam('remove')){
                
                $coupon = $this->couponFactory->create();
                $couponcodes = $coupon->load($request->getParam('freegift-coupon_code'), 'code');
                if(strpos($this->_checkoutSession->getQuote()->getAppliedRuleIds(), $couponcodes->getRuleId())!==false){

                    $rules = $this->_rule->load($couponcodes->getRuleId());
                    if($rules->getSimpleAction()=='add_free_item' && (int)$rules->getCouponType()== 2){
                       
                       $qty = $rules->getDiscountAmount();
                       $freeGiftItem = $this->_cart->getQuote()->getAllItems();
                       
                        $checkoutSession = $this->getCheckoutSession();
                        $allItems = $checkoutSession->getQuote()->getAllItems();//returns all teh items in session
                        foreach ($allItems as $item) {
                            $itemId = $item->getItemId();//item id of particular item
                            if(strpos($rules->getFreeGiftSku(), $item->getSku())!==false){
                                $quoteItem=$this->getItemModel()->load($itemId);//load particular item which you want to delete by his item id
                                $quoteItem->delete();//deletes the item
                                $this->_cart->removeItem($itemId);
                            }
                        }
                        
                    }
                    
                }
                
            }    
    }


    public function getItemModel(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();//instance of object manager
        $itemModel = $objectManager->create('Magento\Quote\Model\Quote\Item');//Quote item model to load quote item
        return $itemModel;
    }

    public function getCheckoutSession(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();//instance of object manager 
        $checkoutSession = $objectManager->get('Magento\Checkout\Model\Session');//checkout session
        return $checkoutSession;
    }
}

