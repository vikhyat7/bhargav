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
        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer);
        // $logger->info('Removecoupon');

        $request = $observer->getEvent()->getData('request');
        if($request->getParam('remove')){
                
                $coupon = $this->couponFactory->create();
                $couponcodes = $coupon->load($request->getParam('freegift-coupon_code'), 'code');
                if(strpos($this->_checkoutSession->getQuote()->getAppliedRuleIds(), $couponcodes->getRuleId())!==false) {

                    $rules = $this->_rule->load($couponcodes->getRuleId());
                    if($rules->getSimpleAction()=='add_free_item' && (int)$rules->getCouponType()== 2){
                       
                       $qty = $rules->getDiscountAmount();
                       $freeGiftItem = $this->_cart->getQuote()->getAllItems();
                       
                        $checkoutSession = $this->getCheckoutSession();
                        $allItems = $checkoutSession->getQuote()->getAllItems();//returns all teh items in session
                        // var_dump(count($allItems));exit();
                        foreach ($allItems as $item) {
                            $itemId = $item->getItemId();//item id of particular item
                            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                            $Product_id = $item->getProductId();//item id of particular item
                            $parent_id = $objectManager->get('Magento\ConfigurableProduct\Model\Product\Type\Configurable')->getParentIdsByChild($Product_id);
                            if ($parent_id) {
                                // echo $parent_id."<br>";
                                var_dump($parent_id);
                                $product = $objectManager->get('Magento\Catalog\Model\ProductRepository')->getById($parent_id[0]);
                                $parent_sku = $product->getSku();
                                // echo $parent_sku;exit();
                                if(strpos($rules->getFreeGiftSku(), $parent_sku)!==false){
                                    $quoteItem=$this->getItemModel()->load($itemId);//load particular item which you want to delete by his item id
                                    $quoteItem->delete();//deletes the item
                                    $this->_cart->removeItem($itemId);
                                }
                            }
                            if(strpos($rules->getFreeGiftSku(), $item->getSku())!==false){
                                $quoteItem=$this->getItemModel()->load($itemId);//load particular item which you want to delete by his item id
                                $quoteItem->delete();//deletes the item
                                $this->_cart->removeItem($itemId);
                            }
                            // else{
                            //     var_dump($item->getSku());exit();
                            //     $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                            //     $child_id = $product->getId();
                            //     echo $child_id;exit();
                            //     // $product = $objectManager->get('Magento\ConfigurableProduct\Model\Product\Type\Configurable')->getParentIdsByChild($child_id);;

                            // }
                        }
                        // exit();
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
