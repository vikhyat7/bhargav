<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\GiftCertificate\Controller\Cart;

use Magento\Framework;
use Magento\Framework\Controller\ResultFactory;

class Configure extends \Magento\Checkout\Controller\Cart
{
    /**
     * cart Quote Item
     *
     * @var \Magento\Quote\Model\Quote\Item
     */
    protected $_cartQuoteItem;

    /**
     * Customer Data
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerData;

    /**
     * gift Quote Item
     *
     * @var \Mageants\GiftCertificate\Model\Giftquote
     */
    protected $_giftQuote;
    
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Quote\Model\Quote\Item $cartQuoteItem,
     * @param \Magento\Customer\Model\Session $customerData,
     * @param \Mageants\GiftCertificate\Model\Giftquote $giftQuote
     */
    public function __construct(
        Framework\App\Action\Context $context,
        Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Quote\Model\Quote\Item $cartQuoteItem,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Customer\Model\Session $customerData,
        \Mageants\GiftCertificate\Model\Giftquote $giftQuote
    ) {
        $this->_cartQuoteItem=$cartQuoteItem;
        $this->_customerData=$customerData;
        $this->_giftQuote=$giftQuote;
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
    }

    /**
     * Action to reconfigure cart item
     *
     * @return \Magento\Framework\View\Result\Page|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        // Extract item and product to configure
        $id = (int)$this->getRequest()->getParam('id');
        $productId = (int)$this->getRequest()->getParam('product_id');
        $quoteItem = null;
        if ($id) {
            $quoteItem = $this->cart->getQuote()->getItemById($id);
        }
        
        try {
            if (!$quoteItem || $productId != $quoteItem->getProduct()->getId()) {
                $this->messageManager->addError(__("We can't find the quote item."));
                return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('checkout/cart');
            }
            $params = new \Magento\Framework\DataObject();
            $params->setCategoryId(false);
            $params->setConfigureMode(true);
            $params->setBuyRequest($quoteItem->getBuyRequest());
            $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
            $this->_objectManager->get('Magento\Catalog\Helper\Product\View')
                ->prepareAndRender(
                    $resultPage,
                    $quoteItem->getProduct()->getId(),
                    $this,
                    $params
                );
            $customerId= $this->_customerData->getCustomer()->getId();
            $collection=$this->_cartQuoteItem->getCollection()->addFieldToFilter('product_id',$productId)->addFieldToFilter('item_id',$id);

           foreach($collection->getData() as $giftcardQuote){
                 $this->_checkoutSession->setGiftCustomPrice($giftcardQuote['custom_price']);
           }
           $quoteCollection=$this->_giftQuote->getCollection()->addFieldToFilter('customer_id',$customerId)->addFieldToFilter('product_id',$productId);
           foreach ($quoteCollection as $quote) {
              $this->_checkoutSession->setGiftImage($quote->getTemplateId());
              $this->_checkoutSession->setGiftSenderName($quote->getSenderName());
              $this->_checkoutSession->setGiftSenderEmail($quote->getSenderEmail());
              $this->_checkoutSession->setGiftRecipientName($quote->getRecipientName());
              $this->_checkoutSession->setGiftRecipientEmail($quote->getRecipientEmail());
              $this->_checkoutSession->setMessage($quote->getMessage());
              
              $this->_checkoutSession->setDateOfDelivery($quote->getDateOfDelivery());

           }
          return $resultPage;
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We cannot configure the product.'));
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            return $this->_goBack();
        }
    }
}
