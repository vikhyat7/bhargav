<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\GiftCertificate\Block; 

class Giftcode extends \Magento\Framework\View\Element\Template
{
	/**
     * @var \Mageants\GiftCertificate\Helper\Data
     */
    protected $_helper;
    
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_cart;

    /**

     * checkout session

     *

     * @var \Magento\Checkout\Model\Session

     */

    protected $_checkoutSession;
    
    /**
     * @param Context $context
     * @param \Mageants\GiftCertificate\Helper\Data $helper
     * @param \Magento\Checkout\Model\Cart $cart
     * @param array $data
     */
    public function __construct(
        \Mageants\GiftCertificate\Helper\Data $helper,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = []
    ) {
        $this->_helper = $helper;
        $this->_cart = $cart;
        $this->_checkoutSession=$checkoutSession;
        parent::__construct($context);
    } 
    
    /**
     * {@inheritdoc}
     */
    public function isLoggedIn()
    {
       return $this->_helper->isLoggedIn();
    }
    
    /**
     * @return int
     */
    public function isCartEmpty()
    {
       if(empty($this->_cart->getQuote()->getAllItems()))
       {
           return 1;
       }
       return 0;
    }

    public function getGiftCertificateCode()
    {
       return $this->_checkoutSession->getGiftCertificateCode();
    }
}
