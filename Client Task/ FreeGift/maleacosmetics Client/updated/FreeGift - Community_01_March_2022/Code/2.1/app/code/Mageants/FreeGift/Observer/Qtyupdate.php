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
     * @param \Magento\SalesRule\Model\Rule $rule
     */    
    public function __construct(
        \Magento\SalesRule\Model\Rule $rule
    ) {
        $this->_rule = $rule;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {        
        $ruleCollections = $this->_rule->getCollection()->addFilter('is_active',1);
        $valid = true;
        $validArray = array();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->create('\Magento\Checkout\Model\Cart');
		$helper = $objectManager->get('\Magento\Catalog\Helper\Product\Configuration');
        $quoteTotals = $cart->getQuote()->collectTotals()->getTotals();
        $allitems = $cart->getQuote()->getAllItems();
        $subtotal = $quoteTotals['subtotal']->getValue();
        $i=0;
        foreach($ruleCollections as $ruleCollection)
        {
            if($ruleCollection->getSimpleAction()=='add_free_item' && (int)$ruleCollection->getCouponType()!== 2)
            {
                $conditionSerialized = $ruleCollection->getConditionsSerialized();				
				$cond = unserialize($conditionSerialized);
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
							$valid = false;  
							$code = 'if((int)$cart->getQuote()->getItemsQty()
							 '.$rulecond['operator'].' (int)$rulecond["value"]){$trueskus[] = $ruleCollection->getFreeGiftSku(); $validArray[] = true; }else{ $falsesku[] = $ruleCollection->getFreeGiftSku(); $validArray[] = false; $valid = true; }';    
							eval($code );
						}
						if($rulecond['attribute'] == 'base_subtotal'){
							$valid = false;  
							$code = 'if((int)$subtotal '.$rulecond['operator'].' (int)$rulecond["value"]){ $trueskus[] = $ruleCollection->getFreeGiftSku(); $validArray[] = true; }else{ $falsesku[] = $ruleCollection->getFreeGiftSku(); $validArray[] = false; $valid=false;}';
							eval($code );
						}
					}
                        
                        
					 if($aggregator == 'all'){
						if($result){ 
							if(in_array(false, $validArray)){
								foreach ($allitems as $cartitems) {
									$options=$helper->getCustomOptions($cartitems);
									if ($options) 
									{
										foreach ($options as $option) {
											if ($option['value'] == "Free Product") 
											{
												$sku=implode(" ",$falsesku);
												if(strpos($sku,$cartitems->getSku())!==false){
													$cart->removeItem($cartitems->getItemId())->save();
												}
											}
										}
									}
								 }								
							}else{
								$allitems = $cart->getQuote()->getAllItems();
								foreach ($allitems as $addedItems) {
										$options = $helper->getCustomOptions($addedItems);
											if(!empty($options)){
												foreach ($options as $option) {
													if($option['value'] == 'Free Product' && $addedItems->getRowTotal()){
														$cart->removeItem($addedItems->getId());
													}
												}
											}
											
									}
							}
						}
						else{
							if(in_array(1, $validArray)){
								foreach ($allitems as $cartitems) {
									$options=$helper->getCustomOptions($cartitems);
									if ($options) 
									{
										foreach ($options as $option) {
											if ($option['value'] == "Free Product") 
											{
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
				   }elseif($aggregator == 'any'){
						if($result){
							if(!in_array($result, $validArray)){
								foreach ($allitems as $cartitems) {
									$options=$helper->getCustomOptions($cartitems);
									if ($options) 
									{
										foreach ($options as $option) {
											if ($option['value'] == "Free Product") 
											{
												$sku=implode(" ",$falsesku);
												if(strpos($sku,$cartitems->getSku())!==false){
													$cart->removeItem($cartitems->getItemId())->save();
												}
											}
										}
									}
								}								
							}
						}
						else{
							if(!in_array(0, $validArray)){
								foreach ($allitems as $cartitems) {
									$options=$helper->getCustomOptions($cartitems);
									if ($options) 
									{
										foreach ($options as $option) {
											if ($option['value'] == "Free Product") 
											{
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
        }
	}
}

