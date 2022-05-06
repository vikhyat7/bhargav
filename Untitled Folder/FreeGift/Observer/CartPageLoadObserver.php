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

class CartPageLoadObserver implements ObserverInterface
{ 
    protected $_cart;
    
	protected $_messageManager;
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
    
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;


    protected $_responseFactory;


    protected $_url;
    
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
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\ResponseFactory $responseFactory,
		\Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    ) {
        $this->_cart = $cart;   
  		$this->_freeGiftHelper = $freeGiftHelper;
  		$this->_checkoutSession = $checkoutSession;
  		$this->_ruleCollectionFactory = $ruleCollectionFactory;
  		$this->_storeManager = $storeManager;  
        $this->_responseFactory = $responseFactory;
        $this->_url = $url;
		$this->_messageManager = $messageManager;
  		$this->_productRepository = $productRepository;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
	}
	
	public function execute(\Magento\Framework\Event\Observer $observer) 
	{
		$validation = $this->_freeGiftHelper->getCartBasedValidRuleOnAddtoCart();
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$helper = $objectManager->get('\Magento\Catalog\Helper\Product\Configuration');
		$subTotals = $this->_cart->getQuote()->getTotals()['subtotal']['value'];
		$totalQty = $this->_cart->getQuote()->getItemsQty();
		$appliedRuleId = explode(',',$this->_checkoutSession->getQuote()->getAppliedRuleIds());
		$freeGiftItem = $this->_cart->getQuote()->getAllItems();

		$validRules =  $this->_ruleCollectionFactory->create()
			->addFieldToFilter('rule_id', ['in' => $appliedRuleId]);

			$allRules = $this->_ruleCollectionFactory->create();
			
			foreach ($allRules as $value) 
			{
				if ($value->getSimpleAction() == 'add_free_item') 
				{
					$getConditios = $this->serializer->unserialize($value->getConditionsSerialized());

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

		foreach($validRules as $_rule):
			if($_rule->getSimpleAction() == 'add_free_item' && (int)$_rule->getCouponType()!== 2):
				$getConditiosSerialize = $this->serializer->unserialize($_rule->getConditionsSerialized());
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

		// $sku_array = explode(',', $skus);
		// $tempcount = 1;
		// var_dump($sku);
		// exit();
		$isActive = $this->_freeGiftHelper->getFreeGiftConfig('mageants_freegift/general/active');
		if($isActive) {
			$allitems = $this->_cart->getQuote()->getAllItems();
			// $itemcount = count($allitems);
			// echo $itemcount;
			foreach ($allitems as $items) {
				//$cart_Ids[] = $items->getId();
				//$cart_Skus[] = $items->getSku();
				// if(strpos($skus, $items->getSku())!==false ){
				// echo "<pre>";
				// echo "<br>";
				// echo $skus;
				// echo "<br>";
				// echo $items->getSku()."->";
				// echo $items->getProductId();
				// echo "<br>";

				$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $Product_id = $items->getProductId();//item id of particular item
                $parent_id = $objectManager->get('Magento\ConfigurableProduct\Model\Product\Type\Configurable')->getParentIdsByChild($Product_id);
                if ($parent_id) {
                    // echo $parent_id."<br>";
                    // var_dump($parent_id);
                    $product = $objectManager->get('Magento\Catalog\Model\ProductRepository')->getById($parent_id[0]);
                    $parent_sku = $product->getSku();
                    // echo $parent_sku;exit();
                    if(strpos($skus, $parent_sku)!==false){
						return;
                    }
                }

				if(strpos($skus, $items->getSku())!==false ){
					// echo "In If";
					return;
				}
				// echo "</pre>";
				// if (in_array($items->getSku(), $sku_array)){
				// 	return;
				// }
			}
			// echo "Out of If";

			$storeId = $this->_storeManager->getStore()->getId();
			$freeGiftSkus = explode(',',$skus);
			foreach($freeGiftSkus as $sku)
			{
				$freeGiftProduct = $this->_productRepository->get($sku); 
				$loadProduct = $this->_productRepository->getById($freeGiftProduct->getId(), false, $storeId, true);
				
				$additionalOptions = [];
				$additionalOptions[] = array(
					'label' => "Free! ",
					'value' => "Free Product",
				);
				
				$loadProduct->addCustomOption('additional_options', $this->serializer->serialize($additionalOptions));
				
				$freeGiftParams = array(
					'product' => $freeGiftProduct->getId(),
					'qty' => $qty
				);
				
				if($freeGiftProduct->getTypeId() == 'configurable'){
					// $this->_messageManager->addErrorMessage(__("Please selct congigurable free gift product option"));
					// return false;
					$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
					$configProduct = $objectManager->create('Magento\Catalog\Model\Product')->load($freeGiftProduct->getId());
					$_children = $configProduct->getTypeInstance()->getUsedProducts($configProduct);
					foreach ($_children as $child){
						$childid[] = $child->getID();
					}
					$new_item = $childid[0];
					$loadProduct = $this->_productRepository->getById($new_item, false, $storeId, true);
					$loadProduct->addCustomOption('additional_options', $this->serializer->serialize($additionalOptions));
					$freeGiftParams = array(
						'product' => $new_item,
						'qty' => $qty
					);
				}
				$this->_cart->addProduct($loadProduct,$freeGiftParams); 
				
				$lastFreeItem = $this->_cart->getItems()->getLastItem();
				//$lastFreeItem->setParentProductId($lastItemId);
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
		}
	}
}
