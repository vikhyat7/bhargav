<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderCustomization\Rewrite\PurchaseOrder\Item;

/**
 * Class Collection
 *
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD)
 * @package Magestore\PurchaseOrderCustomization\Rewrite\PurchaseOrder\Item
 */
class Collection extends \Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item\Collection
{
    /**
     * Init Select
     *
     * @return $this|\Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item\Collection|void
     */
    protected function _initSelect()
    {

        $this->getSelect()->from(['main_table' => $this->getMainTable()])
            ->columns(
                [
                    'price_difference' => new \Zend_Db_Expr('main_table.cost - main_table.original_cost')
                ]
            );

        return $this;
    }

    /**
     * Add Field To Filter
     *
     * @param array|string $field
     * @param null $condition
     * @return \Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item\Collection
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'price_difference') {
            $field = new \Zend_Db_Expr('main_table.cost - main_table.original_cost');
        } else {
            $field = new \Zend_Db_Expr('main_table.' . $field);
        }
        return parent::addFieldToFilter($field, $condition);
    }
}