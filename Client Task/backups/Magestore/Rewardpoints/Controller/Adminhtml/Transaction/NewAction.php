<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Rewardpoints\Controller\Adminhtml\Transaction;

class NewAction extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Backend\Model\View\Result\Forward
     */
    protected $resultForwardFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context
    ) {


        parent::__construct($context);
        $this->resultForwardFactory = $this->_objectManager->create('Magento\Backend\Model\View\Result\ForwardFactory');
    }
    /**
     * Create new customer action
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();
        $resultForward->forward('edit');
        return $resultForward;
    }

    /**
     * Check the permission to Manage Customers
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_Rewardpoints::Manage_transaction');
    }
}
