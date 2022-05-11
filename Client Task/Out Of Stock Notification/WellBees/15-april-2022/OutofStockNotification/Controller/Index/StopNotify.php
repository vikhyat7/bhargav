<?php
/**
 * @category Mageants Out Of Stock Notification
 * @package Mageants_OutOfStockNotification
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\OutofStockNotification\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;

class StopNotify extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;
    /**
     * @var \Mageants\OutofStockNotification\Model\Stocknotification
     */
    protected $_stockNotification;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

     /**
      * @param \Magento\Framework\App\Action\Context $context
      * @param \Magento\Framework\App\Request\Http $request
      * @param \Magento\Framework\Message\ManagerInterface $messageManager
      * @param \Mageants\OutofStockNotification\Model\Stocknotification $stockNotification
      */
    public function __construct(
        Context $context,
        \Magento\Framework\App\Request\Http $request,
        TypeListInterface $cacheTypeList,
        Pool $cacheFrontendPool,
        \Mageants\OutofStockNotification\Model\Stocknotification $stockNotification
    ) {
        $this->_request = $request;
        $this->_stockNotification = $stockNotification;
        $this->_messageManager = $context->getMessageManager();
        $this->cacheTypeList = $cacheTypeList;
        $this->cacheFrontendPool = $cacheFrontendPool;
        parent::__construct($context);
    }
    
    /**
     * return redirect at My OutOfStock Subscription
     */
    public function execute()
    {
        $types = [
            'config',
            'layout',
            'block_html',
            'collections',
            'reflection',
            'db_ddl',
            'eav',
            'config_integration',
            'config_integration_api',
            'full_page',
            'translate',
            'config_webservice'
        ];
        foreach ($types as $type) {
            $this->cacheTypeList->cleanType($type);
        }
        foreach ($this->cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
        $sku = $this->_request->getParam('sku');
        $customerId = $this->_request->getParam('customer_id');//@codingStandardsIgnoreStart
        $collection = $this->_stockNotification->getCollection()->addFieldToFilter('product_sku', $sku)->addFieldToFilter('customer_id', $customerId);//@codingStandardsIgnoreEnd
        foreach ($collection as $key => $value) {
            if ($value->getNotifyStatus() == 1) {
                $value->setNotifyStatus(0);
                $this->_messageManager->addSuccess(__("You are Stop Notify Successfully."));
            } else {
                $value->setNotifyStatus(1);
                $this->_messageManager->addSuccess(__("You are Start Notify Successfully."));
            }
            
            $this->_stockNotification->setData($value->getData());
            $this->_stockNotification->save();
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
}
