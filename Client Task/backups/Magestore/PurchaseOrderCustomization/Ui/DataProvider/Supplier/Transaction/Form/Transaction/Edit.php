<?php
/**
 * Copyright © Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderCustomization\Ui\DataProvider\Supplier\Transaction\Form\Transaction;

use Magestore\PurchaseOrderCustomization\Model\ResourceModel\Supplier\Transaction\CollectionFactory;
use Magento\Framework\App\RequestInterface;

/**
 * Class Edit
 *
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD)
 * @package Magestore\PurchaseOrderCustomization\Ui\DataProvider\Supplier\Transaction\Form\Transaction
 */
class Edit extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    
    /**
     * @var RequestInterface
     */
    protected $request;
    
    /**
     * Shipment constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param Increment $increment
     * @param RequestInterface $request
     * @param PurchaseOrderShipmentRepository $poShipmentRepository
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->request = $request;
    }
    
    /**
     * @inheritdoc
     */
    public function getData()
    {
        $data = [];
        $supplierTransactionId = (int)$this->request->getParam('supplier_transaction_id');
        if ($supplierTransactionId) {
            $transaction = $this->collection->addFieldToFilter('supplier_transaction_id', $supplierTransactionId)
                ->setPageSize(1)
                ->setCurSize(1)
                ->getFirstItem();
            $data = $transaction->getData();
        } else {
            $data['supplier_id'] = $this->request->getParam('supplier_id');
        }
        $result[''] = $data;
        
        return $result;
    }
}
