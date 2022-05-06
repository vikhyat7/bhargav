<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\GiftCertificate\Controller\Adminhtml\Gcimages;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Mageants\GiftCertificate\Model\ResourceModel\Templates\CollectionFactory;

/**
 * MassDelete Image Template
 */ 
class Massdelete extends \Magento\Backend\App\Action
{
    /**
     * Filter
     *
     * @var Magento\Ui\Component\MassAction\Filter
     */
    protected $_filter;
    
    /**
     * template collection
     *
     * @var Mageants\GiftCertificate\Model\ResourceModel\Templates\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param Mageants\GiftCertificate\Model\ResourceModel\Templates\CollectionFactory $collectionFactory
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
     * Execute MassDelete for Template
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());
        $collectionSize = $collection->getSize();
        foreach($collection->getData() as $items)
        {
            $id=$items['image_id'];
            $row = $this->_objectManager->get('Mageants\GiftCertificate\Model\Templates')->load($id);
            $row->delete();
        }
            $this->messageManager->addSuccess(
            __('A total of %1 record(s) have been deleted.', $collectionSize)
        );
 
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }

}