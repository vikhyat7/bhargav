<?php

/**

 * @category Mageants GiftCertificate

 * @package Mageants_GiftCertificate

 * @copyright Copyright (c) 2016 Mageants

 * @author Mageants Team <support@mageants.com>

 */



namespace Mageants\GiftCertificate\Controller\Cart;

use Magento\Framework\View\Result\PageFactory;



/**

 * Apply the Gift code in checkout page

 */

class Cancel extends \Magento\Framework\App\Action\Action

{

	/**

     * helper object

     *

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
	
	protected $resultJsonFactory; 
	

    /**

     * @param \Magento\Backend\Block\Template\Context $context

     * @param \Mageants\GiftCertificate\Helper\Data $helper

     * @param \Magento\Checkout\Model\Session $checkoutSession

     */

    public function __construct

	(\Magento\Framework\App\Action\Context $context,\Magento\Checkout\Model\Session $checkoutSession,\Mageants\GiftCertificate\Helper\Data $helper,\Magento\Checkout\Model\Cart $cart,\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory)

	{	 
		$this->resultJsonFactory = $resultJsonFactory;
        $this->_checkoutSession=$checkoutSession;
        $this->_cart = $cart;

        $this->_helper=$helper;

      	parent::__construct($context);          

	}



    /**

     * Perform Apply Action

     */ 

	public function execute()

    {
		$result_return = $this->resultJsonFactory->create();
        $this->_checkoutSession->unsGift();

    	$data=$this->getRequest()->getPostValue();

        $catids=$data['categoryids'];

        $subtotal=0;

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $gifCodes = $objectManager->get('Mageants\GiftCertificate\Model\Codelist');

        $availableCode=$objectManager->get('Mageants\GiftCertificate\Model\Account')->getCollection()->addFieldToFilter('gift_code',trim($data['gift_code']))->addFieldToFilter('status', 1);

        



         $certificate_value=0;

         	foreach($availableCode as $code){

                $certificate_value=  $code->getCurrentBalance();

                    $quote = $objectManager->get('\Magento\Checkout\Model\Cart')->getQuote();

                    $gift_value=$this->_checkoutSession->getGift();

		            $accund_id=$code->getAccountId();

                    $updateblance=$code->getCurrentBalance() + $gift_value;

                    $result=array(0=>'3',1=>'Gift Certificate Cancelled');

                //echo json_encode($result);

                  
					$result_return->setData($result);
                    $_SESSION['custom_gift'] = $gift_value;

                $this->_checkoutSession->setGift(0); 
                $this->_checkoutSession->setGiftCertificateCode("");  

                $this->_checkoutSession->setAccountid($accund_id);  

                $this->_checkoutSession->setGiftbalance($updateblance); 
                 $this->_checkoutSession->getQuote()->collectTotals()->save();

                return $result_return;  

         	}
 	}
}