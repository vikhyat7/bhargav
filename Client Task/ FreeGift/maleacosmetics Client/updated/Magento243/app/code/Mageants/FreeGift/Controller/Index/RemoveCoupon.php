<?php
namespace Mageants\FreeGift\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class RemoveCoupon extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

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

    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    protected $_logger;

    public function __construct(
        Context $context, 
        \Magento\SalesRule\Model\Rule $rule,  
        \Magento\Checkout\Model\Session $checkoutSession,     
        \Magento\SalesRule\Model\CouponFactory $couponFactory,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    )
    {     
        $this->_rule = $rule;
        $this->_checkoutSession = $checkoutSession;
        $this->_couponFactory = $couponFactory;
        $this->_cart = $cart;   
        $this->quoteRepository = $quoteRepository;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
        parent::__construct($context);
    }
    
	/**
	 * return redirect at customer Dashboard
	 */
    public function execute()
    {
        $cartId = $this->_cart->getQuote()->getId();      
        $quote = $this->quoteRepository->getActive($cartId);
        $my_code = $quote->getCouponCode();
        $coupon = $this->_couponFactory->create();
        $couponcodes = $coupon->load($my_code, 'code');
        
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