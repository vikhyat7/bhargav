<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCertificate\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;
/**
 *	configure product when update from cart
 */
class CheckStatus implements ObserverInterface
{
	

	/**
     * category
     *
     * @var \Magento\Catalog\Model\Category $category
     */
	protected $_category;
	/**
     * store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
	protected $_storeManager;

	/**
     * scope config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
	protected $_scopeConfig;

	
	/**
     * @param \Magento\Catalog\Model\Category $category,
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager,
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
	public function __construct(
		\Magento\Catalog\Model\Category $category,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		TypeListInterface $cacheTypeList,
        Pool $cacheFrontendPool
	)
	{
		$this->_category=$category;	
		$this->_storeManager=$storeManager;	
		$this->_scopeConfig=$scopeConfig;
		$this->cacheTypeList = $cacheTypeList;
        $this->cacheFrontendPool = $cacheFrontendPool;
	}

	/**
     * configure product and update cart
     */
	public function execute(\Magento\Framework\Event\Observer $observer) 
	{
		 $cat_info = $this->_category->load($this->_storeManager->getStore()->getRootCategoryId()); 
	       $cate=$this->_category->getCollection()->addAttributeToFilter('url_key','giftcard')->getFirstItem();
        if($cate->getId())
            {
                $_gcstatus=$this->_scopeConfig->getValue('giftcertificate/general/statusgc', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $status = ($_gcstatus == 1) ? true : false;
                $_updateCat=$this->_category->load($cate->getId());
				$_updateCat->setIsActive($status);
				$_updateCat->setId($cate->getId());
                $_updateCat->save();
                $invalidatedTypes = $this->cacheTypeList->getInvalidated();
            	if (isset($invalidatedTypes)) {
                foreach ($invalidatedTypes as $value) {
                    if ($value["id"] == "layout" || $value["id"] == "full_page") {
                        $this->cacheTypeList->cleanType($value["id"]);
                    }
                }
            }
            }     
	}   
}
     
    
