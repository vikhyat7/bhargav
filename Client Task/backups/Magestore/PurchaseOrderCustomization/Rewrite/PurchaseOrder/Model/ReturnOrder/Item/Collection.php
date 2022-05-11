<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderCustomization\Rewrite\PurchaseOrder\Model\ReturnOrder\Item;

/**
 * Class Collection
 *
 * @package Magestore\PurchaseOrderCustomization\Rewrite\PurchaseOrder\Model\ReturnOrder\Item
 */
class Collection extends \Magestore\PurchaseOrderSuccess\Model\ResourceModel\ReturnOrder\Item\Collection
{
    /**
     * Init Select
     *
     * @return $this|\Magestore\PurchaseOrderSuccess\Model\ResourceModel\ReturnOrder\Item\Collection|void
     */
    protected function _initSelect()
    {

        $this->getSelect()->from(['main_table' => $this->getMainTable()])
            ->columns(
                [
                    'total_row' => new \Zend_Db_Expr('main_table.cost * main_table.qty_returned')
                ]
            );
        return $this;
    }

    /**
     * Add Field To Filter
     *
     * @param array|string $field
     * @param mixed $condition
     * @return \Magestore\PurchaseOrderSuccess\Model\ResourceModel\ReturnOrder\Item\Collection
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'total_row') {
            $field = new \Zend_Db_Expr('main_table.cost * main_table.qty_returned');
        } else {
            $field = new \Zend_Db_Expr('main_table.' . $field);
        }
        return parent::addFieldToFilter($field, $condition);
    }
}
