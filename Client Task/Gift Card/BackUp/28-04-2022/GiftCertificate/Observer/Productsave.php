<?php

namespace Mageants\GiftCertificate\Observer;

use Magento\Framework\Event\ObserverInterface;

class Productsave implements ObserverInterface
{    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
    	echo die("hello");
        $_product = $observer->getProduct();  // you will get product object
        $_sku=$_product->getSku();
        var_dump($_sku); // for sku

    }   
}