<?php
/**
 * Copyright © Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderCustomization\Controller\Adminhtml\Supplier\Transaction;

use Magento\Backend\App\Action;
use Magestore\PoMultipleTracking\Model\Repository\PurchaseOrderShipmentRepository;
use Magestore\PoMultipleTracking\Model\PurchaseOrderShipmentFactory;
use Magestore\PoMultipleTracking\Model\PurchaseOrderShipment;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Save
 *
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD)
 * @package Magestore\PoMultipleTracking\Controller\Adminhtml\PurchaseOrder\Shipment
 * @SuppressWarnings(PHPMD.AllPurposeAction)
 */
class Save extends Action
{

    /**
     * @var \Magestore\PurchaseOrderCustomization\Model\ResourceModel\Supplier\Transaction\CollectionFactory
     */
    protected $transactionCollectionFactory;

    /**
     * @var \Magestore\PurchaseOrderCustomization\Model\ResourceModel\Supplier\TransactionFactory
     */
    protected $transactionResourceFactory;

    /**
     * Save constructor.
     *
     * @param Action\Context $context
     * @param \Magestore\PurchaseOrderCustomization\Model\ResourceModel\Supplier\Transaction\CollectionFactory $transactionCollectionFactory
     * @param \Magestore\PurchaseOrderCustomization\Model\ResourceModel\Supplier\TransactionFactory $transactionResourceFactory
     */
    public function __construct(
        Action\Context $context,
        \Magestore\PurchaseOrderCustomization\Model\ResourceModel\Supplier\Transaction\CollectionFactory $transactionCollectionFactory,
        \Magestore\PurchaseOrderCustomization\Model\ResourceModel\Supplier\TransactionFactory $transactionResourceFactory

    )
    {
        parent::__construct($context);
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        $this->transactionResourceFactory = $transactionResourceFactory;
    }

    /**
     * Execture
     *
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $supplierTransactionId = $this->getRequest()->getParam('supplier_transaction_id');
        /** @var \Magestore\PurchaseOrderCustomization\Model\ResourceModel\Supplier\Transaction\Collection $transactionCollection */
        $transactionCollection = $this->transactionCollectionFactory->create();
        /** @var \Magestore\PurchaseOrderCustomization\Model\Supplier\Transaction $transaction */
        $transaction = $transactionCollection->addFieldToFilter('supplier_transaction_id', $supplierTransactionId)
            ->setPageSize(1)
            ->setCurPage(1)
            ->getFirstItem();
        $data = $this->getRequest()->getParams();
        $transactionCreatedDate = '';
        $transactionDate = '';
        if(isset($data['transaction_created_date']) && $data['transaction_created_date']){
            if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $data['transaction_created_date'])) {
                $transactionCreatedDate = date_create_from_format("Y-m-d", $data['transaction_created_date']);
            }else{
                $transactionCreatedDate = date_create_from_format("d/m/Y", $data['transaction_created_date']);
            }
            $transactionCreatedDate = date_format($transactionCreatedDate, 'm/d/Y');
        }

        if(isset($data['transaction_date']) && $data['transaction_date']){
            $transactionDate = date_create_from_format("d/m/Y",$data['transaction_date']);
            $transactionDate = date_format($transactionDate, 'm/d/Y');
        }
        $dataImport = [
            'supplier_id' => isset($data['supplier_id']) ? $data['supplier_id'] : '',
            'supplier_transaction_id' => isset($data['supplier_transaction_id']) ? $data['supplier_transaction_id'] : '',
            'transaction_created_date' => $transactionCreatedDate,
            'transaction_date' => $transactionDate,
            'type' => isset($data['type']) ? $data['type'] : '',
            'doc_no' => isset($data['doc_no']) ? $data['doc_no'] : '',
            'chq_no' => isset($data['chq_no']) ? $data['chq_no'] : '',
            'amount' => isset($data['amount']) ? $data['amount'] : '',
            'currency' => isset($data['currency']) ? $data['currency'] : '',
            'description_option' => isset($data['description_option']) ? $data['description_option'] : ''
        ];
        try{
            $transaction->setSupplierId($dataImport['supplier_id']);
            $transaction->setTransactionCreatedDate($dataImport['transaction_created_date']);
            $transaction->setTransactionDate($dataImport['transaction_date']);
            $transaction->setType($dataImport['type']);
            $transaction->setDocNo($dataImport['doc_no']);
            $transaction->setChqNo($dataImport['chq_no']);
            $transaction->setAmount($dataImport['amount']);
            $transaction->setCurrency($dataImport['currency']);
            $transaction->setDescriptionOption($dataImport['description_option']);

            $this->transactionResourceFactory->create()->save($transaction);
            $this->messageManager->addSuccessMessage(__('Save transaction successfully.'));

        }catch (\Exception $e){
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('suppliersuccess/supplier/edit', ['id' => $this->getRequest()->getParam('supplier_id')]);
    }
}
