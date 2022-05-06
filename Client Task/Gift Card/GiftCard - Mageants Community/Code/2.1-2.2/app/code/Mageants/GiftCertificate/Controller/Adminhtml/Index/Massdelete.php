<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCertificate\Controller\Adminhtml\Index;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Mageants\GiftCertificate\Model\ResourceModel\Codeset\CollectionFactory;
/**
 * Perform MassDelete controller Action
 */
class Massdelete extends \Magento\Backend\App\Action
{
    /**
     * Filter
     *
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $_filter;

    /**
     * Collection factory
     *
     * @var \Mageants\GiftCertificate\Model\ResourceModel\Codeset\CollectionFactory
     */
    protected $_collectionFactory;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param  \Mageants\GiftCertificate\Model\ResourceModel\Codeset\CollectionFactory $collectionFactory
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
     * Perform MassDelete Action
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());
        $collectionSize = $collection->getSize();
        foreach($collection->getData() as $items)
        {
            $id=$items['code_set_id'];
            $row = $this->_objectManager->get('Mageants\GiftCertificate\Model\Codeset')->load($id);
            $codelist = $this->_objectManager->get('Mageants\GiftCertificate\Model\Codelist')->getCollection()->addFieldToFilter('code_set_id',$id);
            $row->delete();
            foreach($codelist as $list){
                $list->delete();
            }
        }
            $this->messageManager->addSuccess(
            __('A total of %1 record(s) have been deleted.', $collectionSize)
        );
 
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }
}