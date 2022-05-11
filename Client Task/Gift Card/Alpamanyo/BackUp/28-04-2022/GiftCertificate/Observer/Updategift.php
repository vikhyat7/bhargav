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
class Updategift implements ObserverInterface
{
	/**
     * request object
     *
     * @var \Magento\Framework\App\RequestInterface
     */
	protected $_request;

	/**
     * helper
     *
     * @var \Mageants\GiftCertificate\Helper\Data
     */
	protected $_helper;

	/**
     * gift Quote
     *
     * @var \Mageants\GiftCertificate\Model\Giftquote
     */
	protected $_giftquote;

	/**
     * Model for product
     *
     * @var \Magento\Catalog\Model\Product
     */
	protected $modelProduct;

	/**
     * checkout session
     *
     * @var \Magento\Checkout\Model\Session
     */
	protected $_checkoutSession;

	/**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Mageants\GiftCertificate\Model\Giftquote $giftquote
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Mageants\GiftCertificate\Helper\Data $helper
     * @param \Magento\Catalog\Model\Product $modelProduct
     */
	public function __construct(
		\Magento\Framework\App\RequestInterface $request,
		\Mageants\GiftCertificate\Model\Giftquote $giftquote,
		\Magento\Catalog\Model\Product $modelProduct,
		\Magento\Checkout\Model\Session $checkoutSession,
		\Mageants\GiftCertificate\Helper\Data $helper
	)
	{
		$this->_request = $request;
		$this->_helper=$helper;
		$this->modelProduct=$modelProduct;
		$this->_giftquote=$giftquote;
		$this->_checkoutSession = $checkoutSession;
	}

	/**
     * configure product and update cart
     */
	public function execute(\Magento\Framework\Event\Observer $observer) 
	{
		$post = $this->_request->getPostValue();
		$collection = $this->_giftquote->getCollection()->addFieldToFilter("product_id",$post['giftproductid'])
					 			->addFieldToFilter("customer_id",$post['customerid']);
		$id = $collection->getData()[0]['id'];
		if($this->_request->getPostValue('manual-giftprices')!=''):
			$price = $this->_request->getPostValue('manual-giftprices');
		else:
			$price = $this->_request->getPostValue('giftprices');
		endif; 

		$model = $this->_giftquote->load($id);
		$model->setGiftCardValue($price);
		$model->setTemplateId($post['giftimage']);
		$model->setSenderName($post['sender-name']);
		$model->setSenderEmail($post['sender-email']);
		$model->setRecipientName($post['recipient-name']);
		$model->setRecipientEmail($post['recipient-email']);
		if(array_key_exists('giftmessage', $post)){
			$model->setMessage($post['giftmessage']);
		}
		if(array_key_exists('del-date', $post)){
			$model->setDateOfDelivery($post['del-date']);
		}
		$model->save();
		
		$item = $observer->getItem();
		$item->setCustomPrice($price);
		$item->setOriginalCustomPrice($price);
		$item->save();

		$this->_checkoutSession->unsGiftCustomPrice();
		$this->_checkoutSession->unsGiftImage();
		$this->_checkoutSession->unsGiftSenderName();
		$this->_checkoutSession->unsGiftSenderEmail();
		$this->_checkoutSession->unsGiftRecipientName();
		$this->_checkoutSession->unsGiftRecipientEmail();			
	}   
}
     
    
