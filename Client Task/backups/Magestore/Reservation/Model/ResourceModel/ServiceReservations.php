<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @developer   Louis Coding
 * @category    Magestore
 * @package     Magestore_Reservation
 * @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

namespace Magestore\Reservation\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;
use Magento\InventoryReservationsApi\Model\ReservationInterface;
use Magento\Sales\Model\OrderFactory;

/**
 * Class ServiceReservations
 *
 * @package Magestore\Reservation\Model\ResourceModel
 */
class ServiceReservations
{
    const OBJECT_ID = 'object_id';
    const RESERVE_QTY = 'reserve_qty';

    /**
     * @var ResourceConnection
     */
    public $resource;

    /**
     * @var Order
     */
    public $salesModelOrder;

    /**
     * ServiceReservations constructor.
     *
     * @param ResourceConnection $resource
     * @param OrderFactory $salesModelOrder
     */
    public function __construct(
        ResourceConnection $resource,
        OrderFactory $salesModelOrder
    ) {
        $this->resource = $resource;
        $this->salesModelOrder = $salesModelOrder;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        if ($this->doBackup()) {
            $countShiped = $this->deteleShipedOrder();
            $countDontNeed = $this->deteleDontNeedToShipOrder();
            return __("FINISH: correct %1 records", $countShiped + $countDontNeed);
        } else {
            return __('Cant create backup!');
        };
    }

    /**
     * Detele Dont Need To Ship Order
     *
     * @return int
     */
    public function deteleDontNeedToShipOrder()
    {
        $selects = [
            'stock_id', 'sku',
            self::RESERVE_QTY => "SUM(" . ReservationInterface::QUANTITY . ")",
            self::OBJECT_ID => "JSON_EXTRACT(metadata, '$.object_id')",
        ];
        $groups = [ReservationInterface::STOCK_ID, ReservationInterface::SKU, self::OBJECT_ID];
        $condition = self::RESERVE_QTY . ' < 0';
        $shipedOrder = $this->fetchAllData($selects, $groups, $condition);
        $count = 0;
        foreach ($shipedOrder as $order) {
            $orderId = (int)(str_replace('"', '', $order['object_id']));
            /** @var \Magento\Sales\Model\Order $orderModel */
            $orderModel = $this->salesModelOrder->create()->load($orderId);
            $willDelete = true;

            if ($orderModel->getState() != \Magento\Sales\Model\Order::STATE_COMPLETE) {
                /** @var \Magento\Sales\Model\Order\Item $orderItem */
                foreach ($orderModel->getItems() as $orderItem) {
                    if ($orderItem->getSku() == $order['sku'] && $orderItem->canShip()) {
                        $willDelete = false;
                    }
                }
            }

            if ($willDelete) {
                $this->deteteReservationByObjectId($order['object_id']);
                $count++;
            }
        }
        return $count;
    }

    /**
     * Detele Shiped Order
     *
     * @return int
     */
    public function deteleShipedOrder()
    {
        $selects = [
            'stock_id', 'sku',
            self::RESERVE_QTY => "SUM(" . ReservationInterface::QUANTITY . ")",
            self::OBJECT_ID => "JSON_EXTRACT(metadata, '$.object_id')",
        ];
        $groups = [ReservationInterface::STOCK_ID, ReservationInterface::SKU, self::OBJECT_ID];
        $condition = self::RESERVE_QTY . ' >= 0';
        $shipedOrder = $this->fetchAllData($selects, $groups, $condition);
        $count = 0;
        foreach ($shipedOrder as $order) {
            if (isset($order['object_id']) && $order['object_id']) {
                $this->deteteReservationByObjectId($order['object_id']);
                $count++;
            }
        }
        return $count;
    }

    /**
     * Fetch All Data
     *
     * @param array $selects
     * @param array $groups
     * @param string $condition
     * @return array
     */
    public function fetchAllData(array $selects, array $groups, $condition = "")
    {
        $connection = $this->resource->getConnection();
        $reservationTable = $this->resource->getTableName('inventory_reservation');

        $select = $connection->select()
            ->from(
                $reservationTable,
                $selects
            )
            ->group($groups)
            ->having($condition);
        $data = $connection->fetchAll($select);
        return $data;
    }

    /**
     * Delete Reservation
     *
     * @param string $objectId
     * @return bool
     */
    public function deteteReservationByObjectId($objectId)
    {
        $connection = $this->resource->getConnection($objectId);
        $reservationTable = $this->resource->getTableName('inventory_reservation');
        try {
            $sqlDelete = "Delete FROM " . $reservationTable // @codingStandardsIgnoreLine
                . " WHERE JSON_EXTRACT(metadata, '$.object_id') = " . $objectId;
            $connection->query($sqlDelete);
        } catch (\Exception $exception) {
            return false;
        }
        return true;
    }

    /**
     * Create backup table form inventory_reservation
     *
     * @return bool
     */
    public function doBackup()
    {
        $date = date('Y_m_d_H_i_s', time());
        $connection = $this->resource->getConnection();
        $reservationTable = $this->resource->getTableName('inventory_reservation');
        $newTable = $reservationTable . "_temp_" . $date;
        try {
            $sqlCreate = "CREATE TABLE " . $newTable . " AS SELECT * FROM " . $reservationTable; // @codingStandardsIgnoreLine
            $connection->query($sqlCreate);
        } catch (\Exception $exception) {
            return false;
        }
        return $newTable;
    }
}
