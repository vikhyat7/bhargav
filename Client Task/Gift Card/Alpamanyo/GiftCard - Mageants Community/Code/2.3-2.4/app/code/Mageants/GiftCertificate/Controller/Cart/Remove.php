<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\GiftCertificate\Controller\Cart;
use Magento\Checkout\Model\Cart as CustomerCart;
/**
 * Remove quote from model
 */
class Remove extends \Magento\Framework\App\Action\Action
{
    /**
     * gift quote
     *
     * @var \Mageants\GiftCertificate\Model\Giftquote
     */
    protected $giftQuote;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;
	
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Mageants\GiftCertificate\Model\Giftquote $giftQuote
     */
    public function __construct
	(
        \Magento\Framework\App\Action\Context $context,
        \Mageants\GiftCertificate\Model\Giftquote $giftQuote,
        CustomerCart $cart
    ){
        $this->giftQuote=$giftQuote;
        $this->cart = $cart;
    	parent::__construct($context);          
	}

    /** 
     * Perform Remove Action
     */
	public function execute()
    {
    	$data=$this->getRequest()->getPostValue();
        try{
            $gifCodes = $this->giftQuote->load($data['quoteid']);
            
            $gifCodes->delete();

            $this->deleteQuoteItems($data['productId']);
            $this->messageManager->addSuccess(__("Quote removed.." )); 
         }
        catch(Exception $ex){
            $this->messageManager->addSuccess(__( $ex->getMessage(), count($id)));
        }       
  	}

    /** 
     * remove cart item & quote
     * @param product id
     * @return void
     */
    public function deleteQuoteItems($productId = ''){
        $checkoutSession = $this->getCheckoutSession();
        $allItems = $checkoutSession->getQuote()->getAllVisibleItems();//returns all teh items in session
        foreach ($allItems as $item) {
            if((int)$item->getProduct()->getId()===(int)$productId){
                $itemId = $item->getItemId();//item id of particular item
                $quoteItem=$this->getItemModel()->load($itemId);//load particular item which you want to delete by his item id
               // $quoteItem->delete();//deletes the item
                $this->cart->removeItem($itemId)->save();
              }  
        }
    }
    public function getCheckoutSession(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();//instance of object manager 
        $checkoutSession = $objectManager->get('Magento\Checkout\Model\Session');//checkout session
        return $checkoutSession;
    }
 
    public function getItemModel(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();//instance of object manager
        $itemModel = $objectManager->create('Magento\Quote\Model\Quote\Item');//Quote item model to load quote item
        return $itemModel;
    }
}
