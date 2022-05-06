<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Helper;

use Magestore\BarcodeSuccess\Model\History;

/**
 * Class Attribute
 *
 * Import barcode from product's attribute
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class Attribute extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magestore\BarcodeSuccess\Model\ResourceModel\Product\CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var \Magestore\BarcodeSuccess\Model\HistoryFactory
     */
    protected $historyFactory;
    
    /**
     * @var \Magestore\BarcodeSuccess\Api\Data\HistoryInterface
     */
    protected $historyInterface;
    
    /**
     * @var \Magestore\BarcodeSuccess\Model\ResourceModel\History
     */
    protected $historyResource;
    
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $session;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    /**
     * @var \Magestore\BarcodeSuccess\Model\BarcodeFactory
     */
    protected $barcodeFactory;
    
    /**
     * Attribute constructor.
     *
     * @param \Magento\Framework\App\Helper\Context                                   $context
     * @param \Magestore\BarcodeSuccess\Model\ResourceModel\Product\CollectionFactory $collectionFactory
     * @param \Magestore\BarcodeSuccess\Model\HistoryFactory                          $historyFactory
     * @param \Magestore\BarcodeSuccess\Model\BarcodeFactory                          $barcodeFactory
     * @param \Magestore\BarcodeSuccess\Api\Data\HistoryInterface                     $historyInterface
     * @param \Magestore\BarcodeSuccess\Model\ResourceModel\History                   $historyResource
     * @param \Magento\Framework\Message\ManagerInterface                             $messageManager
     * @param \Magento\Backend\Model\Auth\Session                                     $session
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magestore\BarcodeSuccess\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Magestore\BarcodeSuccess\Model\HistoryFactory $historyFactory,
        \Magestore\BarcodeSuccess\Model\BarcodeFactory $barcodeFactory,
        \Magestore\BarcodeSuccess\Api\Data\HistoryInterface $historyInterface,
        \Magestore\BarcodeSuccess\Model\ResourceModel\History $historyResource,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Backend\Model\Auth\Session $session
    ) {
        $this->historyFactory = $historyFactory;
        $this->collectionFactory = $collectionFactory;
        $this->historyInterface = $historyInterface;
        $this->historyResource = $historyResource;
        $this->session = $session;
        $this->messageManager = $messageManager;
        $this->barcodeFactory = $barcodeFactory;
        parent::__construct($context);
    }
    
    /**
     * Save History
     *
     * @param int    $totalQty
     * @param string $type
     * @param string $reason
     * @return int|string|null
     */
    public function saveHistory($totalQty, $type, $reason = '')
    {
        $historyId = '';
        $history = $this->historyInterface;
        $historyResource = $this->historyResource;
        $adminSession = $this->session;
        try {
            $admin = $adminSession->getUser();
            $adminId = ($admin)? $admin->getId() : 0;
            $history->setData('type', $type);
            $history->setData('reason', $reason);
            $history->setData('created_by', $adminId);
            $history->setData('total_qty', $totalQty);
            $historyResource->save($history);
            $historyId = $history->getId();
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $historyId;
    }
    
    /**
     * Import To Barcode
     *
     * @param string $attributeCode
     */
    public function importToBarcode($attributeCode)
    {
        $model = $this->barcodeFactory->create();
        $resource = $model->getResource();
        $connection = $resource->getConnection();
        /* @see Varien_Db_Adapter_Pdo_Mysql */
        $connection->truncateTable($resource->getMainTable());
        $collection = $this->collectionFactory->create()
                                              ->addAttributeToSelect($attributeCode);
        $historyId = $this->saveHistory($collection->getSize(), History::GENERATED, __('Import from attribute'));
        $collection->walk(
            'migrateBarcode',
            [
                'attribute_code' => $attributeCode,
                'history_id' => $historyId
            ]
        );
    }
}
