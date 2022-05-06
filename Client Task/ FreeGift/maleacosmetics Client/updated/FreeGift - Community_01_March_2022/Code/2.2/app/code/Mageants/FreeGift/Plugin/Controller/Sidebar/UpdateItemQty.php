<?php
/**
 * @category Mageants FreeGift
 * @package Mageants_FreeGift
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\FreeGift\Plugin\Controller\Sidebar;

class UpdateItemQty
{    
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_cart;
    
	/**
     * @var \Mageants\FreeGift\Helper\Data
     */
    protected $_freeGiftHelper; 
    
	/**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    
    /**
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Mageants\FreeGift\Helper\Data $freeGiftHelper
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Checkout\Model\Cart $cart,
        \Mageants\FreeGift\Helper\Data $freeGiftHelper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->_cart = $cart;
  		$this->_freeGiftHelper = $freeGiftHelper;
  		$this->resultJsonFactory = $resultJsonFactory;
    }
    
    public function aroundExecute(\Magento\Checkout\Controller\Sidebar\UpdateItemQty $subject, \Closure $proceed)
	{		
		$isEnable = $this->_freeGiftHelper->getFreeGiftConfig('mageants_freegift/general/active');
	
		$itemId = (int)$subject->getRequest()->getParam('item_id');
		$itemQty = (int)$subject->getRequest()->getParam('item_qty');
		
		$this->_freeGiftHelper->updateConfigFreeGiftItem();
		
		$item = $this->_cart->getQuote()->getItemById($itemId);

		if($item->getIsFreeItem() == 1 && $isEnable)
		{
			$result = $this->resultJsonFactory->create();
			return $result->setData(['success' => true]);
		}	
		$result = $proceed();
		return $result;
	}
}
