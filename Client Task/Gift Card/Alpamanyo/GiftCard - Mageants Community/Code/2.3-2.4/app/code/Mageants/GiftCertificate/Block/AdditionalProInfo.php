<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCertificate\Block;

/**
 * AdditionalProInfo class for add aditional info in product view page
 */ 
class AdditionalProInfo extends \Magento\Framework\View\Element\Template
{	
	/**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;
    
	/**
     * @var \Mageants\GiftCertificate\Model\Giftquote
     */
    protected $_giftquote;

	/**
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
     * @param Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Mageants\GiftCertificate\Model\Giftquote $quotes
     * @param \Mageants\GiftCertificate\Helper\Data $helper
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
     * @param array $data
     */    
    public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Checkout\Model\Session $checkoutSession,
        \Mageants\GiftCertificate\Model\Giftquote $quotes,
        \Mageants\GiftCertificate\Helper\Data $helper, 
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        array $data = []
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_giftquote=$quotes;
        $this->_helper=$helper;
        $this->cookieManager = $cookieManager;
        parent::__construct($context, $data);
    }

    /**
     * prepare constructor
     */
    protected function _construct()
    {
        parent::_construct();
    }
    
    /**
     * prepare Layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }
 
    /**
     * @return string 
     */
    public function getAdditionalData()
    {
        return "Gift Card Details";
    }
    
    /**
     * @return object
     */
    
    public function getGiftQuoteItems($customerid='')
    {
        if($customerid!=''):
            return $this->_giftquote->getCollection()->addFieldToFilter('customer_id',$customerid);
        endif;
    }
    

    /**
     * @param string
     * @return array
     */
    public function getCardType($typeid='')
    {
        $cardtype=array('0'=>'Virtual','1'=>'Printed', '2'=>'Combined');
        return $cardtype[$typeid];
    }
    

    /**
     * save gift  quote
     * @param int
     * @return void
     */
    public function saveQuote($quoteid='')
    {
       if($quoteid!=''):
            $this->_checkoutSession->setGiftquote($quoteid);
       endif;
    }
    
    /**
     * @return integer
     */
    public function getcustomerId()
    {
       return $this->_helper->getCustomerId();
    }

    /**
     * @return integer
     */
    public function geNotLoggedIntcustomerId()
    {
       return $this->cookieManager->getCookie('temp_customer_id');
    }

      /**
     * @return object
     */
    
    public function getNotLoggedInGiftQuoteItems($temptd='')
    {
        if($temptd!=''):
            return $this->_giftquote->getCollection()->addFieldToFilter('temp_customer_id',$temptd);
        endif;
    }
}
