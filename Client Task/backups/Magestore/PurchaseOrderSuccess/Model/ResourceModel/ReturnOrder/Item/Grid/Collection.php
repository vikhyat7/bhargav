<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\ResourceModel\ReturnOrder\Item\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;
use Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemInterface;

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
        $mainTable = 'os_return_order_item',
        $resourceModel = 'Magestore\PurchaseOrderSuccess\Model\ResourceModel\ReturnOrder\Item'
    ) {
        $this->request = $request;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    protected function _initSelect()
    {
        $id = $this->request->getParam('return_id', null);
        $this->getSelect()->from(['main_table' => $this->getMainTable()]);
//        $this->getSelect()->from(['main_table' => $this->getMainTable()])
//            ->joinLeft(
//                ['return_order' => $this->getTable('os_return_order')],
//                'main_table.return_id = return_order.return_id',
//                ['currency_code']
//            );
        if($id) {
            $this->getSelect()->where(
                'main_table.' . ReturnOrderItemInterface::RETURN_ID . ' = ?',
                $id
            );
        }
        return $this;
    }
}
