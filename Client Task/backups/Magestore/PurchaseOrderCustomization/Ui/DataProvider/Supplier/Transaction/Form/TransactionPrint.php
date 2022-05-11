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
 * Class TransactionPrint
 *
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD)
 * @package Magestore\PurchaseOrderCustomization\Ui\DataProvider\Supplier\Transaction\Form
 */
class TransactionPrint extends \Magento\Ui\DataProvider\AbstractDataProvider
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
     * TransactionPrint constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param PoolInterface $pool
     * @param array $meta
     * @param array $data
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
     * Get Data
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getData()
    {
        $supplierId = $this->request->getParam('supplier_id');
        $this->data[$supplierId]['supplier_id'] = $supplierId;
        /** @var ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $this->data = $modifier->modifyData($this->data);
        }

        return $this->data;
    }

    /**
     * Get Meta
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
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
