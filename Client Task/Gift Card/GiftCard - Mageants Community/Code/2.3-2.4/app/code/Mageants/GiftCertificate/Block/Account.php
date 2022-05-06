<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCertificate\Block;

/**
 * Account class for customer account
 */ 
class Account extends \Magento\Framework\View\Element\Template
{
	/**
     * @var \Mageants\GiftCertificate\Model\Account
     */
	protected $_giftorders;
	
	/**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

	/**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Mageants\GiftCertificate\Model\Account $giftAccount
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */    
     public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
        \Mageants\GiftCertificate\Model\Account $giftAccount,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        $this->_giftorders = $giftAccount;
        $this->_customerSession = $customerSession;
        $this->_isScopePrivate = true;
        parent::__construct($context, $data);
    }

    /**
     * _construct
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();       
    }

    /**
     * @param object
     * @return string
     */
    public function getReorderUrl($order)
    {
        return $this->getUrl('sales/order/reorder', ['order_id' => $order->getId()]);
    }
    
    /**
     * @return collection object
     */
    public function getGiftOrder(){
        $collection = $this->_giftorders->getCollection();
        $joinConditions = 'main_table.order_id = gift_code_customer.customer_id';
        $collection->getSelect()->joinLeft(
                     ['gift_code_customer'],
                     $joinConditions,
                     []
                    )->columns("gift_code_customer.*");
        
        return $collection->addFieldToFilter('recipient_email',$this->_customerSession->getCustomerData()->getEmail());        
	}
}
