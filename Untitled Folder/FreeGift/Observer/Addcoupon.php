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
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $url,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    ) {
        $this->_rule = $rule;
        $this->_checkoutSession = $checkoutSession;
        $this->_couponFactory = $couponFactory;
        $this->_storeManager = $storeManager;  
        $this->messageManager = $messageManager;
        $this->responseFactory = $responseFactory;
        $this->url = $url;  
        $this->_productRepository = $productRepository;
        $this->quoteRepository = $quoteRepository;
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
            if($couponcodes->getRuleId()!=null){                  
                if(strpos($this->_checkoutSession->getQuote()->getAppliedRuleIds(), $couponcodes->getRuleId()) !==false) {
                    $rules = $this->_rule->load($couponcodes->getRuleId());
                    if($rules->getSimpleAction()=='add_free_item' && (int)$rules->getCouponType()== 2) {                  
                       $qty = $rules->getDiscountAmount();
                       $freeGiftItem = $this->_cart->getQuote()->getAllItems();

                        foreach ($freeGiftItem as $item) {                   
                            if(strpos($rules->getFreeGiftSku(), $item->getSku())!==false) {
                                $this->_cart->removeItem($item->getItemId())->save();
                            }
                        }                   
                        $this->_cart->save();
                        $this->updateFreeGifts($couponcodes->getRuleId());
                        $this->_checkoutSession->setCouponcode(true);
                    }               
                }
            }
        }
         
        if($couponcodes->getRuleId()!=null) {
            if(strpos($this->_checkoutSession->getQuote()->getAppliedRuleIds(), $couponcodes->getRuleId())!==false) {
                $rules = $this->_rule->load($couponcodes->getRuleId());
                if($rules->getSimpleAction()=='add_free_item' && (int)$rules->getCouponType()== 2) {
                    $freeGiftSkus = explode(',', $rules->getFreeGiftSku());
                    $qty = $rules->getDiscountAmount();
                }
                if (isset($freeGiftSkus)) {
                    if(is_array($freeGiftSkus)) {
                        $storeId = $this->_storeManager->getStore()->getId();
                        $i=0;
                        foreach($freeGiftSkus as $sku){
                            try {
                                $this->_productRepository->get($sku);
                                $i=0;
                            } catch (\Magento\Framework\Exception\NoSuchEntityException $e){
                               $i++;
                                //return $this;
                            }
                        }
                            
                        if($i == 1){
                        $redirectionUrl = $this->url->getUrl('checkout/cart');
                            $this->responseFactory->create()->setRedirect($redirectionUrl)->sendResponse();
                            $this->messageManager->addError(__("Freegift product is not exist."));
                            $cart = $this->quoteRepository->getActive($this->_cart->getQuote()->getId());
                            $cart->setCouponCode('');
                            $this->quoteRepository->save($cart->collectTotals());
                            // var_dump($this);
                            return $this;
                        }
                    }
                    var_dump($freeGiftSkus);
                    foreach($freeGiftSkus as $sku) {
                        $freeGiftItem = $this->_cart->getQuote()->getAllItems();
                        // var_dump($sku);
                        // foreach($freeGiftItem as $freeItem) {
                        //     $options=$helper->getCustomOptions($freeItem);

                        //     if ($options) {
                        //         foreach ($options as $option) {
                        //             if ($option['label'] == "Free! " && $option['value'] == "Product") {
                        //                 // return;
                        //             }
                        //         }
                        //     }
                        // }
                        // exit();
                        $freeGiftProduct = $this->_productRepository->get($sku); 
                        $loadProduct = $this->_productRepository->getById($freeGiftProduct->getId(), false, $storeId, true);
                        
                        if($loadProduct->isSalable() && $loadProduct->getTypeId() == "simple"){ 
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
                        } elseif ($loadProduct->isSalable() && $loadProduct->getTypeId() == "configurable") {
                            // $redirectionUrl = $this->url->getUrl('checkout/cart');
                            // $this->responseFactory->create()->setRedirect($redirectionUrl)->sendResponse();
                            // $this->messageManager->addError(__("Freegift product is configurable"));
                            // $getLastItem = $this->_cart->getItems()->getLastItem();
                            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                            $configProduct = $objectManager->create('Magento\Catalog\Model\Product')->load($freeGiftProduct->getId());
                            $_children = $configProduct->getTypeInstance()->getUsedProducts($configProduct);
                            foreach ($_children as $child){
                                $childid[] = $child->getID();
                            }
                            $new_item = $childid[0];
                            $loadProduct = $this->_productRepository->getById($new_item, false, $storeId, true);
                            // $loadProduct->addCustomOption('additional_options', $this->serializer->serialize($additionalOptions));
                            $additionalOptions = [];
                            $additionalOptions[] = array(
                                'label' => "Free! ",
                                'value' => "Product",
                            );
                            $loadProduct->addCustomOption('additional_options', $this->serializer->serialize($additionalOptions));
                            $freeGiftParams = array(
                                'product' => $new_item,
                                'qty' => $qty
                            );
                        }

                        $getLastItem = $this->_cart->getItems()->getLastItem();
                        $this->_cart->addProduct($loadProduct,$freeGiftParams);

                        $lastFreeItem = $this->_cart->getItems()->getLastItem();
                        // $lastFreeItem->setParentProductId($lastItemId);
                        $lastFreeItem->setIsFreeItem(1);
                        $lastFreeItem->setPrice(0);
                        $lastFreeItem->setBasePrice(0);
                        $lastFreeItem->setCustomPrice(0);
                        $lastFreeItem->setOriginalCustomPrice(0);
                        $lastFreeItem->setPriceInclTax(0);
                        $lastFreeItem->setBasePriceInclTax(0);
                        $lastFreeItem->getProduct()->setIsSuperMode(true);
                        $lastFreeItem->save();
                        
                        // code end for when freegift product is outofstock 
                        $parentItemId = $getLastItem->getParentItemId();
                        if($parentItemId) {
                            $lastItemId = $parentItemId;
                        } else {
                            $lastItemId = $getLastItem->getItemId();
                        }        
                    } 
                    // exit();    
                    $this->_cart->save();
                    // $this->updateFreeGifts($couponcodes->getRuleId());
                }   
            }
        }
    }
}