<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderCustomization\Rewrite\PurchaseOrder\Item\Grid;


/**
 * Class Item
 *
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD)
 * @package Magestore\PurchaseOrderCustomization\Rewrite\PurchaseOrder\Item\Grid
 */
class Collection extends \Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item\Grid\Collection
{
    /**
     * Init Select
     *
     * @return $this|\Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item\Grid\Collection|void
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()
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
     * @return \Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item\Grid\Collection
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
