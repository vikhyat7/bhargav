<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCertificate\Controller\Adminhtml\Gcaccount;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Mageants\GiftCertificate\Model\ResourceModel\Account\CollectionFactory;
 
/*
 * Account MassDelete Controller
 */
class Massdelete extends \Magento\Backend\App\Action
{
    /**
     * For Filter Data
     *
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $_filter;

    /**
     * For Account Collection
     *
     * @var Mageants\GiftCertificate\Model\ResourceModel\Account\CollectionFactory
     */
    protected $_collectionFactory;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param Mageants\GiftCertificate\Model\ResourceModel\Account\CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) 
    {
        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context);
    }
 
    /**
     * Execute method for perform MassDelete controller
     */ 
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());
        $collectionSize = $collection->getSize();
        foreach($collection->getData() as $items)
        {
          $id=$items['account_id'];
            $row = $this->_objectManager->get('Mageants\GiftCertificate\Model\Account')->load($id);
            $row->delete();
        
            
         }
         $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $collectionSize) );
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }

    
}