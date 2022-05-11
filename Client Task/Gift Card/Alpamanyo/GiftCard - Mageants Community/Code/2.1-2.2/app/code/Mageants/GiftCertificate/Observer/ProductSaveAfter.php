<?php

namespace Mageants\GiftCertificate\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Controller\ResultFactory;

class ProductSaveAfter implements ObserverInterface
{    
	/**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    protected $resultRedirect;

    //protected $this->_redirect;

    public function __construct(
    			\Magento\Framework\Message\ManagerInterface $messageManager,
    			\Magento\Framework\App\Response\RedirectInterface $redirect,
    			\Magento\Framework\Controller\ResultFactory $resultfactory
    		){
    		$this->_messageManager = $messageManager;
    		$this->resultRedirectFactory = $resultfactory;  
    		$this->redirect = $redirect;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
    	$data = $observer->getProduct();
    	$typedid=$data->getTypeId();
    	if($typedid == "giftcertificate"){

    	$minprice = $observer->getProduct()->getCustomAttribute('minprice')->getValue();
    	$img = $observer->getProduct()->getCustomAttribute('giftimages')->getValue();
    	$ctgry = $observer->getProduct()->getCustomAttribute('category')->getValue();
    	$maxprice = $observer->getProduct()->getCustomAttribute('maxprice')->getValue();
    	if($minprice!=null){
          $productprice=$data['price'];
          if($productprice < $minprice || $productprice > $maxprice){
          	$this->_messageManager->addError("Make Sure The Product Price is between Minprice To Maxprice of Giftcard.");
          	$backtoproductpage = $this->redirect->getRefererUrl();
            return $backtoproductpage;

          }
        }
      }
    }   
}