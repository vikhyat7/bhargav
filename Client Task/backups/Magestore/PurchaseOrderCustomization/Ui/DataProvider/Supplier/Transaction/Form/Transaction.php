<?php
/**
 * Copyright © Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderCustomization\Ui\DataProvider\Supplier\Transaction\Form;

use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magestore\PurchaseOrderCustomization\Model\ResourceModel\Supplier\Transaction\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;


/**
 * Class Transaction
 *
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD)
 * @package Magestore\PurchaseOrderCustomization\Ui\DataProvider\Supplier\Transaction\Form
 */
class Transaction extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var Increment
     */
    protected $increment;
    
    /**
     * @var RequestInterface
     */
    protected $request;
    
    /**
     * @var PurchaseOrderShipmentRepository
     */
    protected $poShipmentRepository;
    
    /**
     * @var PoolInterface
     */
    protected $pool;
    
    /**
     * Shipment constructor.
     *
     * @param string                          $name
     * @param string                          $primaryFieldName
     * @param string                          $requestFieldName
     * @param CollectionFactory               $collectionFactory
     * @param Increment                       $increment
     * @param RequestInterface                $request
     * @param PurchaseOrderShipmentRepository $poShipmentRepository
     * @param PoolInterface                   $pool
     * @param array                           $meta
     * @param array                           $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        PoolInterface $pool,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->request = $request;
        $this->pool = $pool;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $supplierTransactionId = (int)$this->request->getParam('supplier_transaction_id');
        if ($supplierTransactionId) {
//            $shipment = $this->poShipmentRepository->get($poShipmentId);
//            $this->data[$shipment->getId()] = $shipment->getData();
        } else {
            $supplierId = $this->request->getParam('supplier_id');
            $this->data[$supplierId]['supplier_id'] = $supplierId;
        }
        /** @var ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $this->data = $modifier->modifyData($this->data);
        }

        return $this->data;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getMeta()
    {
        $meta = parent::getMeta();
        
        /** @var ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        return $meta;
    }
}
