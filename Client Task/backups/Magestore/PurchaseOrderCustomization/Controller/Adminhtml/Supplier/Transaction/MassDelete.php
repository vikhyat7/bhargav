<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\PurchaseOrderCustomization\Controller\Adminhtml\Supplier\Transaction;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class MassDelete
 *
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD)
 * @package Magestore\PurchaseOrderCustomization\Controller\Adminhtml\Supplier\Transaction
 * @SuppressWarnings(PHPMD.AllPurposeAction
 */
class MassDelete extends Action
{
    /**
     * @var Filter
     */
    protected $filter;
    /**
     * @var \Magestore\PurchaseOrderCustomization\Model\ResourceModel\Supplier\Transaction\CollectionFactory
     */
    protected $transactionCollectionFactory;

    /**
     * MassDelete constructor.
     *
     * @param Action\Context $context
     * @param Filter $filter
     * @param \Magestore\PurchaseOrderCustomization\Model\ResourceModel\Supplier\Transaction\CollectionFactory $transactionCollectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Filter $filter,
        \Magestore\PurchaseOrderCustomization\Model\ResourceModel\Supplier\Transaction\CollectionFactory $transactionCollectionFactory
    ) {
        $this->filter = $filter;
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        parent::__construct($context);
    }
    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->transactionCollectionFactory->create());
        $collectionSize = $collection->getSize();
        $supplierId = '';
        foreach ($collection as $transaction) {
            $supplierId = $transaction->getSupplierId();
            $transaction->delete();
        }

        $this->messageManager->addSuccessMessage(__('%1 transaction(s) have been deleted.', $collectionSize));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath(
            'suppliersuccess/supplier/edit',
            ['id' => $supplierId]
        );
    }
}
