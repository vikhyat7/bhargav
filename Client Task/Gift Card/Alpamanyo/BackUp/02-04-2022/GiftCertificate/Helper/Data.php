<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016  Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCertificate\Helper;
/**
 * Data class for Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	/**
	 * website object for get website
	 *
 	 * @var \Magento\Store\Model\Website
	 */
	protected $_website;

	/**
	 * for current url
	 *
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $url;

	/**
 	 * store manager
	 *
 	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $_storeManager;

	/**
	 * template 
	 *
	 * @var \Mageants\GiftCertificate\Model\Templates
	 */
	protected $_templates;

	/**
	 * scope config 
	 *
	 * @var \Magento\Framework\App\Config\ScopeConfigInterface
	 */
	protected $_scopeConfig;

	/**
 	 * message Manager
	 *
	 * @var \Magento\Framework\Message\ManagerInterface
	 */
	protected $_messageManager;

	/**
	 * product repository
	 *
	 * @var \Magento\Catalog\Api\ProductRepositoryInterface
	 */
	protected $_productrepository;

	/**
	 * checkout cart
	 *
	 * @var \Magento\Checkout\Model\Cart
	 */
	protected $checkoutCart;

	/**
	 * model Session
	 *
	 * @var \Magento\Customer\Model\Session
	 */
	protected $_modelSession;

	/**
	 * Url Interface
	 *
	 * @var \Magento\Framework\UrlInterface
	 */
	protected $_urlInterface;

	/**
	 * model codeset
	 *
	 * @var \Mageants\GiftCertificate\Model\Codeset 
	 */
	protected $_modelCodeset;

	/**
	 * model codelist
	 *
	 * @var \Mageants\GiftCertificate\Model\Codelist
	 */
	protected $_modelCodelist;

	/**
	 * category Factory
	 *
	 * @var \Magento\Catalog\Model\CategoryFactory
	 */
	protected $_categoryFactory;

	 /**
     * notification confif
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_notificationConfig;
    
    /**
     * Inline Translation
     *
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;
    
    /**
     * Transport Builder
     *
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;
    
    /**
     * MOdel Account
     *
     * @var \Mageants\GiftCertificate\Model\Account 
     */
    protected $_modelAccount;

    
    /**
     * MOdel Customer
     *
     * @var \Mageants\GiftCertificate\Model\Customer 
     */
    protected $_modelCustomer;

    /**
     * currency helper
     *
     * @var \Magento\Framework\Pricing\Helper\Data 
     */
    protected $currencyHelper;

	/**
	 * @param \Magento\Store\Model\Website $website
 	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Mageants\GiftCertificate\Model\Templates $templates
	 * @param \Magento\Store\Model\StoreManagerInterface $url
	 * @param \Magento\Framework\Message\ManagerInterface $messageManager
	 * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
	 * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
	 * @param \Magento\Checkout\Model\Cart $checkoutCart
	 * @param \Magento\Customer\Model\Session $modelSession
	 * @param \Magento\Framework\UrlInterface $urlInterface
	 * @param \Mageants\GiftCertificate\Model\Codeset $modelCodeset
	 * @param \Magento\Framework\UrlInterface $urlInterface
	 * @param \Mageants\GiftCertificate\Model\Codelist $modelCodelist
	 * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
	 * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
	 * @param \Magento\Framework\App\Config\ScopeConfigInterface $emailConfig
	 * @param \Mageants\GiftCertificate\Model\Account $modelAccount
	 * @param \Mageants\GiftCertificate\Model\Customer $modelCustomer
	 * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
	 * @param \Magento\Framework\Pricing\Helper\Data $currencyHelper,
	 */
	public function __construct(
		\Magento\Store\Model\Website $website,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Mageants\GiftCertificate\Model\Templates $templates,
		\Magento\Store\Model\StoreManagerInterface $url,
		\Psr\Log\LoggerInterface $logger,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
		\Magento\Checkout\Model\Cart $checkoutCart,
		\Magento\Framework\App\Config\ScopeConfigInterface $emailConfig,
		\Magento\Customer\Model\Session $modelSession,
		\Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
		\Magento\Framework\UrlInterface $urlInterface,
		\Mageants\GiftCertificate\Model\Codeset $modelCodeset,
		\Mageants\GiftCertificate\Model\Codelist $modelCodelist,
		\Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
		\Magento\Catalog\Model\CategoryFactory $categoryFactory,
		\Mageants\GiftCertificate\Model\Customer $modelCustomer,
		\Magento\Framework\Pricing\Helper\Data $currencyHelper,
		\Mageants\GiftCertificate\Model\Account $modelAccount
	) { 
		$this->_categoryFactory=$categoryFactory;
		$this->_modelCodelist=$modelCodelist;
		$this->_modelCodeset=$modelCodeset;
		$this->_urlInterface=$urlInterface;    
		$this->_modelSession=$modelSession;
		$this->_website = $website;
		$this->_storeManager = $storeManager;
		$this->_templates=$templates;
		$this->url = $url;  
		$this->currencyHelper = $currencyHelper;
		 $this->inlineTranslation = $inlineTranslation;
		$this->_messageManager = $messageManager;
		$this->_scopeConfig = $scopeConfig;
		$this->_productrepository=   $productRepository;
		$this->checkoutCart=$checkoutCart;
		 $this->_modelAccount=$modelAccount;
        $this->_modelCustomer=$modelCustomer;
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder; 
        $this->_notificationConfig=$emailConfig;
         $this->_logger = $logger;
	}  

	/**
	 * @return Array
	 */ 
	public function getWebsites()
	{
		$websites=$this->_website->getCollection();
		$options=array();
		foreach($websites as $website){
			$options[$website->getWebsiteId()]=['label'=>$website->getName(),'value'=>$website->getWebsiteId()];
		}
		return $options;
	}

	/**
	 * @return String
	 */ 
	public function getPriceDropdown($prices='')
	{
		$html="";
		if($prices!=''):
			$html.="<select name='giftprices' id='gift-prices' class='required gift-prices'>";
			if(!is_array($prices)):

				$html.="<option value=".$this->currencyHelper->currency($prices,false,false).">".$this->currencyHelper->currency($prices,true,false)."</option>";
			else:
				foreach($prices as $key=>$price):
					$html.="<option value=".$this->currencyHelper->currency($price['price'],false,false).">".$this->currencyHelper->currency($price['price'],false,false)."</option>";
				endforeach;
			endif;  
			$html.="</select>";
			return $html;
	   endif;
	} 

	/**
	 * @return String
	 */  
	public function getCurrency()
	{
	   return $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
	}

	/**
	 * @return Symbol
	 */ 
	public function getCurrencySymbol()
	{
	   return $this->_storeManager->getStore()->getCurrentCurrency()->getCurrencySymbol();
	}

	/**
	 * @return string
     */ 
	public function getGiftTypeDropdown()
	{
		$html="<select name='gift-type' id='gift-types' class='required gift-type'>
					<option value='0'>".__('Virtual')."</option>
					<option value='1'>".__('Printed')."</option>
					<option value='2'>".__('Both Virtual & Printed')."</option>
				</select>";
		return $html;        
	}

	/**
 	 * @return String
	 */ 
	public function getTemplate($templateid='')
	{
		if($templateid!=''):
			$html='';		  
			foreach($templateid as $tplid):
				$templateCollection=$this->_templates->load($tplid);
				$html.="<div class='giftemplates'><img src=".$this->url->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).$templateCollection->getImage()." class='template-image' width='100px' height='50px' style='padding:5px;'/>
					<input type='hidden' name='temp_id' value='".$templateCollection->getImageId()."' class='temp_id' />
				</div>";
			endforeach;
			return $html;
		endif;
	}

	/**
	 * @return String
	 */ 
	public function getValidity()
	{
		return $this->_scopeConfig->getValue('giftcertificate/gcoption/gcvalidity', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * @return String
	 */ 
	public function isAllowDeliveryDate()
	{
		return $this->_scopeConfig->getValue('giftcertificate/gcoption/allowdelvdate', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * @return string
	 */ 
	public function getEmailTemplate()
	{
		return $this->_scopeConfig->getValue('giftcertificate/email/gifttemplate', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * @return string
	 */ 
	public function allowSelfUse()
	{
		return $this->_scopeConfig->getValue('giftcertificate/gcoption/allowselfuse', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * @return String
	 */ 
	public function getBcc()
	{
		return $this->_scopeConfig->getValue('giftcertificate/email/cc', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * @return Login Object
	 */ 
	public function loginRedirect()
	{	
		$login=$this->isLoggedIn();
		if($login==0):
			$this->_messageManager->addError("Please login to get giftcards");
				$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
				$customer = $objectManager->create('Magento\Customer\Model\SessionFactory')->create();
			$customerSession = $customer;
			$urlInterface = $this->_urlInterface;
			$customerSession->setAfterAuthUrl($urlInterface->getCurrentUrl());
			$customerSession->authenticate();
		endif;
		return $login;
	}


	/**
	 * @return Login Object
	 */ 
	public function checkCustomerLogin()
	{	
		$login=$this->isLoggedIn();
		return $login;
	}

	/**
	 * @return int
	 */ 
	public function getCustomerId()
	{
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$customer = $objectManager->create('Magento\Customer\Model\SessionFactory')->create();
		return  $customer->getCustomer()->getId();
	}

	/**
  	 * @return int
	 */ 
	public function isLoggedIn()
	{
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$customer = $objectManager->create('Magento\Customer\Model\SessionFactory')->create();

		if(!$customer->isLoggedIn()) {
			return 0;
		}
		return 1; 
	}

	/**
	 * @return int
	 */ 
	public function availibilityProduct($codeset='')
	{
		if($codeset!=''):
			
			$codesettitle = $this->_modelCodeset->getCollection()->addFieldToFilter('code_title',$codeset);
			$id=0;
			foreach($codesettitle as $code){
				$id=$code->getCodeSetId();
			}
			$codelist = $this->_modelCodelist->getCollection()->addFieldToFilter('code_set_id',$id)->addFieldToFilter('allocate',0);
			if(empty($codelist->getData())):
				return 0;
			endif;
			return 1;
		endif;
	}

	/**
	 * @return int
	 */ 
	public function setProductStock($id='')
	{
		if($id!=''):
		$product = $this->_productrepository->getById($id);
		$product->setQuantityAndStockStatus(['qty' => 0, 'is_in_stock' => 0]);
		$this->_productrepository->save($product);
		endif;
	}

	/**
	 * @return int
	 */ 
	public function getCartQuoteById($prdId='')
	{
		if($prdId != '')
		{
			$cartData = $this->checkoutCart->getQuote()->getAllVisibleItems();
			$cartDataCount = count($cartData);
			if($cartDataCount > 0)
			{
				foreach( $cartData as $item )
				{
					$productId = $item->getProduct()->getId();
					if($prdId==$productId)
					{
						return 1;
					}
				}
				return 0;
			}
			else
			{
				return 0;
			}
		}
		return 0;
	}

	public function getCategoriesName($catId='')
	{
		if(!empty($catId))
		{
			 $_category = $this->_categoryFactory->create()->load($catId);
			 return $_category->getName();
		}
		return;
	}
	 /**
     * @return coNFIG
     */
    protected function getConfigValue($path, $storeId)
    {
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
 
    /**
     * @return $this
     */
    public function getStore()
    {
        return $this->_storeManager->getStore();
    }
 
    /**
     * @return int
     */
    public function getTemplateId($xmlPath)
    {
        return $this->getConfigValue($xmlPath, $this->getStore()->getStoreId());
    }
 
    /**
     * @return $this
     */
    public function generateTemplate($emailTemplateVariables,$senderInfo,$receiverInfo)
    { 
      $emailTemplate = 'giftcertificate_email_gifttemplate';
      if($this->getEmailTemplate()!=''){
      	$emailTemplate = $this->getEmailTemplate();	
      }
    
        $template =  $this->_transportBuilder->setTemplateIdentifier(trim($emailTemplate))
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND, 
                        'store' => $this->_storeManager->getStore()->getId(),
                    ]
                )
                ->setTemplateVars($emailTemplateVariables)
                ->setFrom($senderInfo)
                ->addTo($receiverInfo['email'],$receiverInfo['name']);
        return $this;        
    }
 
    /**
     * send Template in Email
     */
    public function sendTemplate($emailTemplateVariables)
    {
    	try
       	{

		$this->inlineTranslation->suspend();

       $emailTemplate = 'giftcertificate_email_gifttemplate';
	      if($this->getEmailTemplate()!=''){
	      	$emailTemplate = $this->getEmailTemplate();	
	     }
         $postObject = new \Magento\Framework\DataObject();
                $postObject->setData($emailTemplateVariables);
        $sender = [
        'name' => $emailTemplateVariables['sender_name'],
        'email' => $emailTemplateVariables['sender_email'],
        ];
        $bcc='bhargav@rocktechnolabs.com';
        if(isset($emailTemplateVariables['bcc'])){
        	$bcc=$emailTemplateVariables['bcc'];
        }
       
           $transport = $this->_transportBuilder 
            ->setTemplateIdentifier($emailTemplate)
             ->setTemplateOptions( [
             'area' => \Magento\Framework\App\Area::AREA_FRONTEND, 
             'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
             // 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
             	// 'area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE, 
             ]) // My email template
             ->setTemplateVars(['data' =>$postObject])
             ->setFrom($sender)
             ->addBcc($bcc)
             ->addTo($emailTemplateVariables['recipient_email'])
             ->getTransport();
              
         	  	$transport->sendMessage();
                $this->_messageManager->addSuccess(__('Account data has been successfully saved.'));
            	$this->inlineTranslation->resume();
              } 
              catch (\Exception $e) {
				$this->_messageManager->addError("Unable to send Email to Customer  !");
                  
            }     
    }

    /**
     * Send Notification
     */
    public function sendNotification($emailTemplateVariables='')
    {
         $emailTemplate = 'giftcertificate_email_gifttemplate';
	      if($this->getEmailTemplate()!=''){
	      	$emailTemplate = $this->getEmailTemplate();	
	     }
        $postObject = new \Magento\Framework\DataObject();
                $postObject->setData($sender);
        if($this->_notificationConfig->getValue('giftcertificate/gcoption/advancenotification', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)):
        
               $customerAccount = $this->_modelAccount->getCollection();
               foreach($customerAccount as $code){
                   if($code->getExpireAt()!='0000-00-00'):
                    $currentDate= date('Y-m-d');
                      if($currentDate < $code->getExpireAt()):
                        $customerid=$code->getOrderId();
                        $customer_data = $this->_modelCustomer->load($customerid);
                                 $emailTemplateVariables['message'] = "Your Gift Certificate is going to expire";
                                 $emailTemplateVariables['current_balance'] =$code->getCurrentBalance();
                                  $emailTemplateVariables['code'] =$code->getGiftCode();
                                 $emailTemplateVariables['recipient_name'] = $customer_data->getRecipientName();     
                                 $emailTemplateVariables['recipient_email'] = $customer_data->getRecipientEmail();
                                 $emailTemplateVariables['category_name']=$code->getCategories();
                                $sender = [
                                    'name' => $customer_data->getSenderName(),
                                    'email' => $customer_data->getSenderEmail(),
                                    ];        
                                 try{
		                                $postObject = new \Magento\Framework\DataObject();
		                                $postObject->setData($emailTemplateVariables);
		                                $transport = $this->_transportBuilder 
		                                ->setTemplateIdentifier(trim($emailTemplate))
		                                 ->setTemplateOptions( [
		                                 'area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE, 
		                                 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
		                                 ]) 
		                                 ->setTemplateVars(['data' =>$postObject])
		                                 ->setFrom($sender)
		                                 ->addTo($customer_data->getRecipientEmail())
		                                 ->getTransport();
		                            $transport->sendMessage();
		                             $this->inlineTranslation->resume();
		                           }catch(Exception $ex){
		                           	$this->_logger->addDebug($ex->getMessage());    
		                           }  

                    endif;
                endif;
               }
        endif;    
    }

    public function isallowtimezone(){
		return $this->_scopeConfig->getValue('giftcertificate/email/is_timezone', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);    	
    }
    public function isallowimageupload(){
    	return $this->_scopeConfig->getValue('giftcertificate/gcoption/allow_custom_upload', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);    		
    }

    public function notifyBefore(){
    	return $this->_scopeConfig->getValue('giftcertificate/email/notify_before', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);    		
    }

    public function isNotify(){
    	return $this->_scopeConfig->getValue('giftcertificate/email/is_notify', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);    			
    }
    public function getNotifyTemplate(){
    	return $this->_scopeConfig->getValue('giftcertificate/email/notify_template', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);    				
    }

     public function getSendTime(){
    	return $this->_scopeConfig->getValue('giftcertificate/email/start_date', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);    				
    }
}
