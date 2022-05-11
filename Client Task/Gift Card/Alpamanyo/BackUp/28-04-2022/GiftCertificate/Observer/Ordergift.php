<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCertificate\Observer;
use \Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
/** 
 * Order Gift observer for place order event
 */
class Ordergift implements ObserverInterface
{   
    /**
     * gift Quote
     *
     * @var \Mageants\GiftCertificate\Model\Giftquote
     */
    protected $_giftquote;

    /**
     * logger
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;
    
    /**
     * checkout session
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;
    
    /**
     * customer
     *
     * @var \Mageants\GiftCertificate\Model\Customer
     */
    protected $_customer;
    
    /**
     * account
     *
     * @var \Mageants\GiftCertificate\Model\Account
     */
    protected $_account;
    
    /**
     * store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    
    /**
     * codeset
     *
     * @var \Mageants\GiftCertificate\Model\Codeset
     */
    protected $_codeset;
    
    /**
     * code list
     *
     * @var \Mageants\GiftCertificate\Model\Codelist
     */
    protected $_codelist; 
    
    /**
     * email
     *
     * @var \Mageants\GiftCertificate\Helper\Email
     */
    protected $_email;
    
    /**
     * helper
     *
     * @var \Mageants\GiftCertificate\Helper\Data
     */
    protected $_helper;

      /**
     * cookie manager
     *
     * @var \Magento\Framework\Stdlib\CookieManagerInterface 
     */
    protected $cookieManager; 

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Mageants\GiftCertificate\Model\Giftquote $giftquote
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Mageants\GiftCertificate\Model\Customer $customer
     * @param \Mageants\GiftCertificate\Model\Account $account
     * @param \Mageants\GiftCertificate\Model\Codeset $codeset
     * @param \Mageants\GiftCertificate\Model\Codelist $codelist
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Mageants\GiftCertificate\Helper\Email $email
     * @param \Mageants\GiftCertificate\Helper\Data $helper
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager 
     */
    public function __construct(
		\Magento\Framework\App\RequestInterface $request,
		\Mageants\GiftCertificate\Model\Giftquote $giftquote,
		\Psr\Log\LoggerInterface $logger,
		\Magento\Checkout\Model\Session $checkoutSession,
		\Mageants\GiftCertificate\Model\Customer $customer,
		\Mageants\GiftCertificate\Model\Account $account,
		\Mageants\GiftCertificate\Model\Codeset $codeset, 
		\Mageants\GiftCertificate\Model\Codelist $codelist, 
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Mageants\GiftCertificate\Helper\Data $helper,
		\Magento\Framework\Stdlib\CookieManagerInterface $cookieManager 
    )
    {
        $this->_giftquote=$giftquote;
        $this->_logger = $logger;
        $this->_checkoutSession = $checkoutSession;
        $this->_request = $request;
        $this->_customer = $customer;
       	$this->_account = $account;
        $this->_storeManager = $storeManager;
        $this->_codeset=$codeset;  
  		$this->_codelist=$codelist;  
        $this->_helper=$helper;
        $this->cookieManager = $cookieManager;
    }

    /**
     * Execute and perform order action with gift
     */
    public function execute(\Magento\Framework\Event\Observer $observer) 
    {
		if($this->_checkoutSession->getAccountid()!='' &&  $this->_checkoutSession->getGift()!=''):
			$updateOrder = $observer->getEvent()->getOrder();
            $updateOrder->setCouponRuleName('gift_certificate')->save();
            $updateOrder->setOrderGift($this->_checkoutSession->getGift())->save();
			$this->updateBalance();
			return;
		endif;
		
		if($this->_checkoutSession->getGiftquote()):
			$order = $observer->getEvent()->getOrder();
			$order_id = $order->getIncrementId();
			$items =$order->getAllVisibleItems();
			$productIds = array();
			if($this->_helper->getCustomerId()!==null){
				$gift_quotes= $this->_giftquote->getCollection()->addFieldToFilter('customer_id', $this->_helper->getCustomerId());
			}
			else{
				$gift_quotes= $this->_giftquote->getCollection()->addFieldToFilter('temp_customer_id', $this->cookieManager->getCookie('temp_customer_id'));	
			}	
			$quote_id=array();
			foreach($items as $item){
				if($item->getProductType()=='giftcertificate'):
					foreach($gift_quotes as $gift){
						if($gift->getProductId()==$item->getProductId()):
							$quote_id[]=$gift->getId();
							$codesetModel=$this->_codeset->getCollection()->addFieldToFilter('code_title',$gift->getCodesetid());
							foreach($codesetModel as $codeset){
								$id=$codeset->getId();   
							}
							if($id):
								
								$codes=$this->_codelist->getCollection()->addFieldToFilter('code_set_id',$id);
								$applicableCodes='';
								foreach($codes as $giftcode){
									if($giftcode->getAllocate()==0):
										
									$applicableCodes=$giftcode->getCode();
									$code_list_id=$giftcode->getCodeListId();
										if($code_list_id):
											try{
												$updatecode=array('code_list_id'=>$code_list_id,'allocate'=>1);
												$this->_codelist->setData($updatecode);
												$this->_codelist->save();
											}catch(Exception $e){
									
											}	
										endif;   
										break;
									endif; 
								}
								$certificateCode=array();
								if($applicableCodes!=''):
									$certificateCode[]=$applicableCodes;
								

									$customerdata=array('code_value'=>$gift->getGiftCardValue(),'card_type'=>$gift->getCardTypes(),'sender_name'=>$gift->getSenderName(),'sender_email'=>$gift->getSenderEmail(),'recipient_name'=>$gift->getRecipientName(),'recipient_email'=>$gift->getRecipientEmail(),'date_of_delivery'=>$gift->getDateOfDelivery(),'message'=>$gift->getMessage(),'order_id'=>$order_id, 'timezone'=>$gift->getTimezone(),'emailtime'=>$gift->getEmailtime());
									
									$this->_customer->setData($customerdata);
									$orderid=$this->_customer->save()->getId();
									$accountdata=array('order_id'=>$orderid,'status'=>'1','website'=>$this->_storeManager->getStore()->getWebsiteId(),'initial_code_value'=>$gift->getGiftCardValue(),'current_balance'=>$gift->getGiftCardValue(),'comment'=>$gift->getMessage(),'gift_code'=>$applicableCodes,'expire_at'=>$gift->getExpiryDate(),'template'=>$gift->getTemplateId(),'customer_id'=>$gift->getCustomerId(),'categories'=>$gift->getCategories(),'custom_upload'=>$gift->getCustomUpload(), 'sendtemplate_id'=>$gift->getSendtemplateId(), 'order_increment_id'=>$order_id);
									$this->_account->setData($accountdata);
									$this->_account->save();
									//$emailTemplateVariables['bcc']='test@giftcertificate.com';
									/*if($this->_helper->getBcc()!=''):
										$emailTemplateVariables['bcc']=explode(",",$this->_helper->getBcc());
									endif;
									$gift_template = $gift->getTemplateId();
									if($gift->getCustomUpload()){
									 $mediapath = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
									 $gift_template = $mediapath."giftcertificate/".$gift->getTemplateId();
									}
									if($gift->getCardTypes()!=1):
										 $emailTemplateVariables['left'] = '0px';
				                         $emailTemplateVariables['top'] = '96px';
				                         $emailTemplateVariables['bgcolor'] = '#f00';
				                         $emailTemplateVariables['color'] = '#fff';

				                         if($gift->getSendtemplateId()){
				                         	$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
				                         	$templateData = $objectManager->get('Mageants\GiftCertificate\Model\Templates')->load($gift->getSendtemplateId());
				                         	if($templateData){
				                         		if($templateData->getPositionleft()){ 
								                        $emailTemplateVariables['left'] = $templateData->getPositionleft().'px';

								                      }
								                       if($templateData->getPositiontop()){ 
								                        $emailTemplateVariables['top'] = $templateData->getPositiontop().'px';

								                      }
								                      if($templateData->getColor()){ 
								                        $emailTemplateVariables['bgcolor'] = $templateData->getColor() ;

								                      }
								                      if($templateData->getForecolor()){ 
								                        $emailTemplateVariables['color'] = $templateData->getForecolor() ;

								                      }
                     
				                         	}
				                         }	
										$emailTemplateVariables['template'] = $gift_template;
										$emailTemplateVariables['message'] = $gift->getMessage();
										$emailTemplateVariables['current_balance'] = $gift->getGiftCardValue();
										$emailTemplateVariables['sender_name'] = $gift->getSenderName();     
										$emailTemplateVariables['sender_email'] = $gift->getSenderEmail();     
										$emailTemplateVariables['recipient_name'] = $gift->getRecipientName();     
										$emailTemplateVariables['recipient_email'] = $gift->getRecipientEmail();
										$emailTemplateVariables['validity'] = 'Unlimited';
										$catArray=array();
										$catArray=explode(',',$gift->getCategories());
										$categoryname="";
										foreach ($catArray as $cat) {
											$categoryname .= $this->_helper->getCategoriesName($cat).",";
										}
										$emailTemplateVariables['category_name'] = $categoryname;

										if($gift->getExpiryDate()!='0000-00-00'):
											$emailTemplateVariables['validity'] = $gift->getExpiryDate();
										endif;

										$emailTemplateVariables['code'] = $applicableCodes;
										if($gift->getTimezone()==''):
											if(!empty($emailTemplateVariables)):
												try{
												
													$this->_helper->sendTemplate($emailTemplateVariables);
												}
												catch(Exception $ex){
													$this->_logger->addDebug($ex->getMessage());    
												}
											endif;  
										endif;	
									endif; */ 
								endif;  
							endif;    
						endif;
						if(!empty($quote_id)):
							foreach($quote_id as $id){
								$quote=$this->_giftquote->load($id);
								$quote->setOrderIncrementId($order_id);
								$quote->save();
							}
						endif;
					}
				endif;
			}
        $this->_checkoutSession->unsGiftquote();
        $this->_checkoutSession->setGiftCertificateCode("");  
		endif; 
	}

	/**
	 * Update balance and unset session
     */
	public function updateBalance(){
	    $status=1;
		if($this->_checkoutSession->getGiftbalance()===0 || $this->_checkoutSession->getGiftbalance()=='0'):
			$status=0;
		endif;
		$accountdata=array('status'=>$status,'current_balance'=>$this->_checkoutSession->getGiftbalance(),'account_id'=>$this->_checkoutSession->getAccountid());
		$this->_account->setData($accountdata);
		$this->_account->save();
		$this->_checkoutSession->unsGiftbalance();
		$this->_checkoutSession->unsAccountid();
		$this->_checkoutSession->unsGift();

        $this->_checkoutSession->unsGiftCertificateCode(); 
	}
}
