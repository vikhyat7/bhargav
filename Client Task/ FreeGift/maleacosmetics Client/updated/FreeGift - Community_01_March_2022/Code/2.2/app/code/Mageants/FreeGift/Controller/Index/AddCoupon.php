<?php
namespace Mageants\FreeGift\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class AddCoupon extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Customer\Model\Account\Redirect
     */
    protected $_redirectCustomer;


    protected $_stockNotification;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    protected $_logger;

    public function __construct(
        Context $context, 
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\SalesRule\Model\Rule $rule,  
        \Magento\Checkout\Model\Session $checkoutSession, 
        \Magento\Catalog\Model\ProductRepository $productRepository,   
        \Magento\SalesRule\Model\CouponFactory $couponFactory,  
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    )
    {
        $this->_storeManager = $storeManager; 
        $this->_rule = $rule;
        $this->_checkoutSession = $checkoutSession;
        $this->_couponFactory = $couponFactory;
        $this->_productRepository = $productRepository;
        $this->_cart = $cart;   
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
        parent::__construct($context);
    }
    
    /**
     * return redirect at customer Dashboard
     */
    public function execute()
    {
        $my_code = $_POST['Id'];        
        
        $coupon = $this->_couponFactory->create();
        $couponcodes = $coupon->load($my_code, 'code');
         
        if(strpos($this->_checkoutSession->getQuote()->getAppliedRuleIds(), $couponcodes->getRuleId())!==false)
        {
            $rules = $this->_rule->load($couponcodes->getRuleId());
            if($rules->getSimpleAction()=='add_free_item' && (int)$rules->getCouponType()== 2)
            {
                $freeGiftSkus = explode(',', $rules->getFreeGiftSku());
                $qty = $rules->getDiscountAmount();
            }
            if(is_array($freeGiftSkus))
            {
                $storeId = $this->_storeManager->getStore()->getId();
                $allitems = $this->_cart->getItems();
                $addedSkus=array();
                foreach ($allitems as $addedItems) {
                    if($addedItems->getIsFreeItem()){
                        $addedSkus[$addedItems->getSku()] = true;
                    }
                }

                foreach($freeGiftSkus as $sku)
                {
                    if(array_key_exists($sku, $addedSkus)){
                        continue;
                    }
                    $freeGiftProduct = $this->_productRepository->get($sku); 
                    $loadProduct = $this->_productRepository->getById($freeGiftProduct->getId(), false, $storeId, true);
                    
                    // code start for when freegift product is outofstock
                    if ($freeGiftProduct->getTypeId() == "simple") {
                        if($loadProduct->isSalable()){                        
                            $additionalOptions = [];
                            $additionalOptions[] = array(
                                'label' => "Free! ",
                                'value' => "Product",
                            );
                            
                            $loadProduct->addCustomOption('additional_options', serialize($additionalOptions));
                            
                            $freeGiftParams = array(
                                'product' => $freeGiftProduct->getId(),
                                'qty' => $qty
                            );
                            $getLastItem = $this->_cart->getItems()->getLastItem();
                            $this->_cart->addProduct($loadProduct,$freeGiftParams);
                        }
                        else{
                            $url = $objectManager->get('\Magento\Framework\UrlInterface');
                            $responseFactory =$objectManager->get('\Magento\Framework\App\ResponseFactory');
                            $messageManager = $objectManager->get('\Magento\Framework\Message\ManagerInterface');
                            $quoteRepository = $objectManager->get('\Magento\Quote\Api\CartRepositoryInterface');
                            $redirectionUrl = $url->getUrl('checkout/cart');
                            $responseFactory->create()->setRedirect($redirectionUrl)->sendResponse();
                            $messageManager->addError(__("Freegift product is outofstock"));
                            $cart = $quoteRepository->getActive($this->_cart->getQuote()->getId());
                            $cart->setCouponCode('');
                            $quoteRepository->save($cart->collectTotals());
                            return $this;
                        }
                    }
                    else{
                        $url = $objectManager->get('\Magento\Framework\UrlInterface');
                            $responseFactory =$objectManager->get('\Magento\Framework\App\ResponseFactory');
                            $messageManager = $objectManager->get('\Magento\Framework\Message\ManagerInterface');
                            $quoteRepository = $objectManager->get('\Magento\Quote\Api\CartRepositoryInterface');
                            $redirectionUrl = $url->getUrl('checkout/cart');
                            $responseFactory->create()->setRedirect($redirectionUrl)->sendResponse();
                            $messageManager->addError(__("You need to choose options for your item."));
                            $cart = $quoteRepository->getActive($this->_cart->getQuote()->getId());
                            $cart->setCouponCode('');
                            $quoteRepository->save($cart->collectTotals());
                            return $this;
                    }
                    // code end for when freegift product is outofstock

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
                // $this->updateFreeGifts($couponcodes->getRuleId());
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