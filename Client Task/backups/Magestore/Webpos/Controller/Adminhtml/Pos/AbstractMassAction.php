<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Pos;

use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magestore\Webpos\Model\ResourceModel\Pos\Pos\CollectionFactory;
use Magestore\Webpos\Model\ResourceModel\Pos\Pos\Collection;

/**
 * class \Magestore\Webpos\Controller\Adminhtml\Pos\AbstractMassAction
 *
 * Abstract class for mass action
 * Methods:
 *  _isAllowed
 *  execute
 *  getComponentRefererUrl
 *  massAction
 *
 * @category    Magestore
 * @package     Magestore\Webpos\Controller\Adminhtml\Pos
 * @module      Webpos
 * @author      Magestore Developer
 */
abstract class AbstractMassAction extends \Magento\Backend\App\Action
{
    /**
     * @var string
     */
    protected $redirectUrl = '*/*/index';

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(\Magento\Backend\App\Action\Context $context, Filter $filter, CollectionFactory $collectionFactory)
    {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
    }


    /**
     * @return $this
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            return $this->massAction($collection);
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath($this->redirectUrl);
        }
    }


    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_Webpos::pos');
    }


    /**
     * @return string
     */
    public function getComponentRefererUrl()
    {
        return '*/*/index';
    }

    /**
     * @param Collection $collection
     * @return mixed
     */
    abstract protected function massAction(Collection $collection);
}