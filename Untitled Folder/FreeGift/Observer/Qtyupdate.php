<?php
/**
 * @category Mageants FreeGift
 * @package Mageants_FreeGift
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
 
namespace Mageants\FreeGift\Observer;

use Magento\Framework\Event\ObserverInterface;

class Qtyupdate implements ObserverInterface
{
    /**
     * @var \Magento\SalesRule\Model\Rule
     */
    protected $_rule;   
    
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;
    
    /**
     * @param \Magento\SalesRule\Model\Rule $rule
     */    
    public function __construct(
        \Magento\SalesRule\Model\Rule $rule,
        \Mageants\FreeGift\Helper\Data $freeGiftHelper,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    ) {
        $this->_rule = $rule;
        $this->freeGiftHelper = $freeGiftHelper;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {   
    	$subtotalDisplay = $this->freeGiftHelper->getFreeGiftConfig('tax/cart_display/subtotal');     
        $ruleCollections = $this->_rule->getCollection()->addFilter('is_active',1);
        $valid = true;
        $validArray = array();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->create('\Magento\Checkout\Model\Cart');
        $quoteTotals = $cart->getQuote()->collectTotals()->getTotals();
        $allitems = $cart->getQuote()->getAllItems();
        $subtotal = $quoteTotals['subtotal']->getValue();
        //// echo $subtotal;exit();
        // display for exclude tax subtotal
        if ($subtotalDisplay == 1 ) {
            $subtotalExcl = $quoteTotals['subtotal']->getValue();
            $subtotal_include_Tax = $subtotal + $quoteTotals['tax']->getValue();
        }
        // display for include tax and both
        if ($subtotalDisplay == 2 || $subtotalDisplay == 3) {
            $subtotal_include_Tax = $quoteTotals['subtotal']->getValue();
            $subtotalExcl = $subtotal - $quoteTotals['tax']->getValue();
        }

        $i=0;
        foreach($ruleCollections as $ruleCollection)
        {
            // if($ruleCollection->getSimpleAction()=='add_free_item' && (int)$ruleCollection->getCouponType()!== 2)
            if($ruleCollection->getSimpleAction()=='add_free_item' || (int)$ruleCollection->getCouponType()!== 2)
            {
                $conditionSerialized = $ruleCollection->getConditionsSerialized();				
				$cond = $this->serializer->unserialize($conditionSerialized);
				$trueskus = array();
				$falsesku =array();
				$aggregator='';
				$result = null;
				
				if(array_key_exists('aggregator', $cond)){
					$aggregator = $cond['aggregator'];
					$result  = $cond['value'];
				}
				if(!array_key_exists('conditions', $cond)){
					foreach ($allitems as $cartitems) {					
						if(strpos($ruleCollection->getFreeGiftSku(), $cartitems->getSku())!==false){
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
							$cart->save();							
						}
					}
				}

				if(array_key_exists('conditions', $cond))
				{ 
					foreach ($cond['conditions'] as $rulecond) {
						if($rulecond['attribute'] == 'total_qty'){
							//// echo "In IF 1";exit();
							$valid = false;  
							$code = 'if((int)$cart->getQuote()->getItemsQty()
							 '.$rulecond['operator'].' (int)$rulecond["value"]){$trueskus[] = $ruleCollection->getFreeGiftSku(); $validArray[] = true; }else{ $falsesku[] = $ruleCollection->getFreeGiftSku(); $validArray[] = false; $valid = true; }';    
							eval($code );
						}
						if($rulecond['attribute'] == 'base_subtotal'){
							//// echo "In IF 2";exit();
							$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        					$logger = $objectManager->create(\Psr\Log\LoggerInterface::class);
							$logger->debug('Test Callled Start');
							$logger->debug((int)$subtotal);
							$logger->debug($rulecond['operator']);
							$logger->debug((int)$rulecond["value"]);
							$logger->debug('Test Callled End');
							//echo (int)$subtotal;
							//echo $rulecond['operator'];
							//echo (int)$rulecond["value"];
							// exit();
							$valid = false;  
							$code = 'if((int)$subtotal '.$rulecond['operator'].' (int)$rulecond["value"]){ $trueskus[] = $ruleCollection->getFreeGiftSku(); $validArray[] = true; }else{ $falsesku[] = $ruleCollection->getFreeGiftSku(); $validArray[] = false; $valid=false;}';
							eval($code );
						}
						if($rulecond['attribute'] == 'base_subtotal_with_discount'){
							//// echo "In IF 3";exit();
							$valid = false;  
							$code = 'if((int)$subtotalExcl '.$rulecond['operator'].' (int)$rulecond["value"]){ $trueskus[] = $ruleCollection->getFreeGiftSku(); $validArray[] = true; }else{ $falsesku[] = $ruleCollection->getFreeGiftSku(); $validArray[] = false; $valid=false;}';
							eval($code );
						}
						if($rulecond['attribute'] == 'base_subtotal_total_incl_tax'){
							//// echo "In IF 4";exit();
							$valid = false;  
							$code = 'if((int)$subtotal_include_Tax '.$rulecond['operator'].' (int)$rulecond["value"]){ $trueskus[] = $ruleCollection->getFreeGiftSku(); $validArray[] = true; }else{ $falsesku[] = $ruleCollection->getFreeGiftSku(); $validArray[] = false; $valid=false;}';
							eval($code );
						}
					}
                        
                    //echo "<pre>";
					 if($aggregator == 'all'){
						if($result){ 
							//echo $result;
							//echo "In If";
							if(in_array(false, $validArray)){
								$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
								$cart = $objectManager->get('\Magento\Checkout\Model\Cart'); 
								
								$itemsCollection = $cart->getQuote()->getItemsCollection();
								
								$itemsVisible = $cart->getQuote()->getAllVisibleItems();
								
								$items = $cart->getQuote()->getAllItems();
								$remove_pro_sku = array();

								foreach($items as $item) {
									if($item->getPrice() == '0' || $item->getPrice() == '0.0000'){
										$remove_pro_sku[] = $item->getSku();
									}
								}
								//var_dump($remove_pro_sku);
				
								$sku=implode(" ",$remove_pro_sku);
								//var_dump($sku);
								foreach ($allitems as $cartitems) {
									$sku=implode(" ",$remove_pro_sku);
									if(strpos($sku,$cartitems->getSku())!==false){
										$cart->removeItem($cartitems->getItemId())->save();
									}
								}								
								// exit();
							}
							//else{
								//echo "In Else of If"."<br>";
							//}
						}
						else{
							//echo "In else";
							if(in_array(1, $validArray)){
								foreach ($allitems as $cartitems) {
								   $sku=implode(" ",$trueskus);
								   if(strpos($sku,$cartitems->getSku())!==false){
										$cart->removeItem($cartitems->getItemId())->save();
									}
								}								
							}
						}
				   }elseif($aggregator == 'any'){
						if($result){
							if(!in_array($result, $validArray)){
								$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
								$cart = $objectManager->get('\Magento\Checkout\Model\Cart'); 
								
								$itemsCollection = $cart->getQuote()->getItemsCollection();
								
								$itemsVisible = $cart->getQuote()->getAllVisibleItems();
								
								$items = $cart->getQuote()->getAllItems();
								$remove_pro_sku = array();
								foreach($items as $item) {
									if($item->getPrice() == '0' || $item->getPrice() == '0.0000'){
										$remove_pro_sku[] = $item->getSku();
									}
								}
								$sku=implode(" ",$remove_pro_sku);
								foreach ($allitems as $cartitems) {
									$sku=implode(" ",$remove_pro_sku);
									if(strpos($sku,$cartitems->getSku())!==false){
										$cart->removeItem($cartitems->getItemId())->save();
									}
								}								
							}
						}
						else{
							if(!in_array(0, $validArray)){
								foreach ($allitems as $cartitems) {
									$sku=implode(" ",$trueskus);
								   if(strpos($sku,$cartitems->getSku())!==false){
										$cart->removeItem($cartitems->getItemId())->save();
									}
								 }
							}   
						}
					}
				}
            }   
        }
	}
}
