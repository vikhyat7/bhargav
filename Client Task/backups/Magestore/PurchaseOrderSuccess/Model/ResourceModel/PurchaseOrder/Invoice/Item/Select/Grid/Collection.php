<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Invoice\Item\Select\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;
use Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface;

/**
 * Class Collection
 * @package Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Invoice\Item\Select\Grid
 */
class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;
    
    /**
     * Initialize dependencies.
     *
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        \Magento\Framework\App\RequestInterface $request,
        $mainTable = 'os_purchase_order_item',
        $resourceModel = 'Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item'
    ) {
        $this->request = $request;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    public function getData()
    {
        parent::getData();
        foreach ($this->_data as &$data) {
            $data['cost_default'] = $data['cost'];
        }
        return $this->_data;
    }

    protected function _initSelect()
    {
        $id = $this->request->getParam('purchase_id', null);
        $this->getSelect()->from(['main_table' => $this->getMainTable()])
            ->joinLeft(
                ['purchase_order' => $this->getTable('os_purchase_order')],
                'main_table.purchase_order_id = purchase_order.purchase_order_id',
                ['currency_code']
            )
            ->columns([
                'available_qty' => new \Zend_Db_Expr('main_table.qty_orderred - main_table.qty_billed')
            ])
            ->where(new \Zend_Db_Expr('main_table.qty_orderred - main_table.qty_billed') . ' > 0');
        if($id)
            $this->getSelect()->where(
                'main_table.'.PurchaseOrderItemInterface::PURCHASE_ORDER_ID . ' = ?',
                $id
            );
        return $this;
    }
}
