<?php
/**
 * @category Mageants FreeGift
 * @package Mageants_FreeGift
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\FreeGift\Observer;

use Magento\Framework\Event\ObserverInterface;

class ApiQuoteRemoveItemObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_cart;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Checkout\Model\Cart $cart
     */   
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Checkout\Model\Cart $cart,
        \Mageants\FreeGift\Helper\Data $freeGiftHelper,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\SalesRule\Model\Rule $rule
    )
    {
        $this->_request = $request;
        $this->_cart = $cart;   
        $this->_freeGiftHelper = $freeGiftHelper; 
        $this->quoteRepository = $quoteRepository;        
        $this->_rule = $rule;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {   
// $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
// $logger = new \Zend\Log\Logger();
// $logger->addWriter($writer);
// $logger->info(print_r('ApiQuoteRemoveItemObserver',true));     
        /** @var \Magento\Quote\Model\Quote\Item $item */
        $item = $observer->getEvent()->getQuoteItem();     
        $quote = $this->quoteRepository->get($item->getQuoteId( ));            
		$freeGiftItem = $quote->getAllItems();

        foreach($freeGiftItem as $freeItem)
        { 
            if($item->getId() == $freeItem->getParentProductId())
            {
                $quote->removeItem($freeItem->getItemId());
            }            
        }
        $quote->save();
    }
}
