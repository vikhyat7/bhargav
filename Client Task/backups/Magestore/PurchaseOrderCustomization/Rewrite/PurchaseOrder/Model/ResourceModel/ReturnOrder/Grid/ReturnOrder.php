<?php

namespace Magestore\PurchaseOrderCustomization\Rewrite\PurchaseOrder\Model\ResourceModel\ReturnOrder\Grid;

/**
 * Class ReturnOrder
 *
 * @package Magestore\PurchaseOrderCustomization\Rewrite\PurchaseOrder\Model\ResourceModel\ReturnOrder\Grid
 */
class ReturnOrder extends \Magestore\PurchaseOrderSuccess\Model\ResourceModel\ReturnOrder\Grid\ReturnOrder
{
    /**
     * Init select
     *
     * @return $this|\Magestore\PurchaseOrderSuccess\Model\ResourceModel\ReturnOrder\Grid\ReturnOrder|void
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->joinLeft(
            ['os_return_order_item' => $this->getTable('os_return_order_item')],
            'os_return_order_item.return_id = main_table.return_id',
            []
        )->group('main_table.return_id')
            ->columns(
                [
                    'returned_total' => new \Zend_Db_Expr(
                        'SUM(os_return_order_item.cost * os_return_order_item.qty_returned)'
                    )
                ]
            );
        return $this;
    }

    /**
     * Add field to filter
     *
     * @param array|string $field
     * @param mixed $condition
     * @return $this|\Magestore\PurchaseOrderSuccess\Model\ResourceModel\ReturnOrder\Grid\ReturnOrder
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'returned_total') {
            $field = new \Zend_Db_Expr('SUM(os_return_order_item.cost * os_return_order_item.qty_returned)');
            $resultCondition = $this->_translateCondition($field, $condition);
            $this->_select->having($resultCondition, null, \Magento\Framework\DB\Select::TYPE_CONDITION);
            return $this;
        }
        return parent::addFieldToFilter($field, $condition);
    }
}
