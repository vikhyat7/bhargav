<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCertificate\Observer;
use Magento\Framework\Event\ObserverInterface;
/**
 *	configure product when update from cart
 */
class Removegift implements ObserverInterface
{
	/**
     * checkout session
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

	/**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */

	  public function __construct(
		\Magento\Checkout\Model\Session $checkoutSession
	){
        $this->_checkoutSession = $checkoutSession;
    
    }

    /**
     * remove gift card from discount charge
     * @return void 
     */
	public function execute(\Magento\Framework\Event\Observer $observer) 
	{
		if($this->_checkoutSession->getGift()){
            $this->_checkoutSession->setGiftCertificateCode("");
			$this->_checkoutSession->unsGift();
		}
	}   
}
     
    
