<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
 
namespace Mageants\CustomStockStatus\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ReplaceTeamplate implements ObserverInterface {
	
    // const XML_CONFIG_TYPE = 'extragallery/general/glr_type';
	
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
	
	/**
     * @var \Magento\Framework\App\Request\Http
     */
	protected $request;
	
	/**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
	

    /**
     * AdminFailed constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\App\Request\Http $request,
		\Magento\Framework\Registry $registry
    )
    {
        $this->scopeConfig = $scopeConfig;
		$this->_request = $request;
		$this->registry = $registry;
    }
    
   public function execute(Observer $observer) {
        
		$product = $this->registry->registry('product');
		
		if ($product && $product->getId()) {
		  //  echo "if";
			$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
			$type = $this->scopeConfig->getValue("extragallery/general/glr_type", $storeScope);
			if($product->getData('extragallery_glr_type')){
			    $type = $product->getData('extragallery_glr_type');
			}
			
			$action = $this->_request->getFullActionName();
			
			$layout = $observer->getLayout();
			$blockProductGallery = $layout->getBlock('product.info.media.image');
			
			if($blockProductGallery){
				// $layout->unsetElement('product.info.media.image');
				if($type == 3){
				    // echo "if";
					$layout->unsetElement('breadcrumbs');
					$layout->unsetElement('product.info.overview');
					$layout->unsetElement('product.info.media');
				}else {
 				    // echo "else";
				// 	$layout->unsetElement('product.info.extra.media.image.type');
				// 	$layout->unsetElement('product.info.overview.copy');
				}
			}
		}
		
		return $this;
    }
}
