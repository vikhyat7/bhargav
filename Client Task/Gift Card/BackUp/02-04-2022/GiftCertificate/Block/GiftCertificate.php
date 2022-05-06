<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\GiftCertificate\Block;

/**
 * GiftCertificate Class for giftCertificate
 */ 
class GiftCertificate extends \Magento\Framework\View\Element\Template
{
	/**
     * @var \Magento\Framework\Registry
     */
	protected $_registry;
	
	/**
     * @var \Mageants\GiftCertificate\Helper\Data
     */
	protected $_helper;
	
	/**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;
    
   	/**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */

    protected $_timezone;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Mageants\GiftCertificate\Helper\Data $helper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $_timezone
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Mageants\GiftCertificate\Helper\Data $helper,
        \Magento\Checkout\Model\Session $checkoutSession,
    	\Magento\Framework\Registry $registry,
		\Magento\Framework\Message\ManagerInterface $messageManager,

          \Magento\Config\Model\Config\Source\Locale\Timezone $timezone,
        \Magento\Framework\App\Request\Http $request
       ) {     
		$this->_registry = $registry;
        $this->_helper = $helper;
        $this->_checkoutSession = $checkoutSession;
        $this->_storeManager = $context;      
        $this->_messageManager = $messageManager;
        $this->_request= $request;
        $this->_timezone=$timezone;
        
        if($this->_registry->registry('product')->getTypeId()=='giftcertificate'):
			if(!$this->_helper->availibilityProduct($this->_registry->registry('product')->getAttributeText('giftcerticodeset'))):
				$this->_helper->setProductStock($this->_registry->registry('product')->getId());
                $this->_messageManager->addError("Out Of Stock");
			endif;
        endif;     
        parent::__construct($context);
    }
     
    /**
     * @return int
     */
    public function getProductTypeId()
    {
		return $this->getProduct()->getTypeId();
    }
     
    /**
     * @return string
     */
    public function getGiftCustomPrice()
    {
      if($this->_request->getParam('product_id')){
        return $this->_checkoutSession->getGiftCustomPrice();
        }
        return null;
    }

    /**
     * @return string
     */
    public function getGiftImage()
    {
        if($this->_request->getParam('product_id')){
            return $this->_checkoutSession->getGiftImage();
           }
           return null; 
    }
     
    /**
     * @return string
     */
    public function getGiftSenderName()
    {
        if($this->_request->getParam('product_id')){
            return $this->_checkoutSession->getGiftSenderName();
         }
         return null;   
    }

    /**
	 * @return string
     */
    public function getGiftSenderEmail()
    {
        if($this->_request->getParam('product_id')){
            return $this->_checkoutSession->getGiftSenderEmail();
           }
           return null; 
    }

    /**
     * @return string
     */
	public function getGiftRecipientName()
    {
        if($this->_request->getParam('product_id')){
            return $this->_checkoutSession->getGiftRecipientName();
        }
        return null;
    }

    /**
     * @return string
     */
    public function getGiftRecipientEmail()
    {
		if($this->_request->getParam('product_id')){
            return $this->_checkoutSession->getGiftRecipientEmail();
         }
         return null;   
    }
    /**
     * @return date
     */
    public function getDateOfDelivery()
    {
        if($this->_request->getParam('product_id')){
            return $this->_checkoutSession->getDateOfDelivery();
         }
         return null;   
    }

    /**
     * @return string
     */
    public function getMessage()
    {
           
        if($this->_request->getParam('product_id')){
            return $this->_checkoutSession->getMessage();
        }
        return null;


    }
    
    /**
     * @return object
     */
    public function getProduct()
    {
        return $this->_registry->registry('product');
	}
     
    /**
     * @return array
     */
    public function getProductPrice()
    {
     	if(!empty($this->getProduct()->getTierPrice())):
     		$prices=$this->_helper->getPriceDropdown($this->getProduct()->getTierPrice());
     		return $prices;
     	else:	
     		$prices=$this->_helper->getPriceDropdown($this->getProduct()->getPrice());
     		return $prices;
     	endif;
    }
     
    /**
     * @return int
     */
	public function getCurrency()
    {
		return $this->_helper->getCurrency();
	}
     
    /**
     * @return object
     */
    public function getGiftType()
    {
     	return $this->getProduct()->getAttributeText('gifttype');	
    }
     
    /**
     * @return object
     */
    public function getGiftTypeOption()
    {
     	if($this->getGiftType()=='Combined'):
     		return $this->_helper->getGiftTypeDropdown();
     	endif;
     	return '';
    } 
    
    /**
     * @return int
     */
    public function getGiftTemplates()
    {
		$template=$this->getProduct()->getResource()->getAttributeRawValue($this->getProduct()->getId(),'giftimages',$this->_storeManager->getStore()->getId());
		$templateid=explode(',',$template);
        return $templateid;
    }
    
    /**
     * @return string
     */ 
    public function getTemplateImages()
    {
        return $this->_helper->getTemplate($this->getGiftTemplates());
    }
    
    /**
     * @return int
     */ 
	public function isallowGreetings()
    {
        if($this->getProduct()->getAttributeText('allowmessage')=='No'):    
            return 0;
        endif;
        return 1;
    }
     
	/**
     * {@inheritdoc}
     */
    public function isAllowDeliveryDate()
	{
		return $this->_helper->isAllowDeliveryDate();
	}
	
    /**
     * {@inheritdoc}
     */
	public function isLoggedIn()
	{
		return $this->_helper->checkCustomerLogin();
	}
	
    /**
     * @return string
     */
	public function getCodeSetId()
	{
		return $this->getProduct()->getAttributeText('giftcerticodeset');   
	}
	
    /**
     * @return int
     */
	public function getCustomerId()
	{
		return $this->_helper->getCustomerId();
	}
	
    public function getMinPrice()
    {
        return $this->getProduct()->getMinprice();
    }
    public function getMaxPrice()
    {
        return $this->getProduct()->getMaxprice();
    }
    
    /**
     * @return object
     */
	public function availibilityProduct()
	{
		return $this->_helper->availibilityProduct($this->getProduct()->getAttributeText('giftcerticodeset'));
	}

    /**
     * @return object
     */
    public  function getCartQuoteById($prdId=''){
        return $this->_helper->getCartQuoteById($prdId);
    }

    /**
     * @return object
     */
    public  function getTempCustomerId($prdId=''){
        return mt_rand(000000, 999999);
    }

    /**
     * @return array
     */
    public function getTimeZoneList(){
        return $this->_timezone->toOptionArray();
    }
    /**
     * @return boolean
     */
    public function isallowtimezone(){
        return $this->_helper->isallowtimezone();
    }
    /**
     * @return boolean
     */
    public function isallowimageupload(){
         return $this->_helper->isallowimageupload();   
    }
}
