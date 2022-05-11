<?php

namespace Magestore\PurchaseOrderCustomization\Rewrite\PurchaseOrder\Block\Adminhtml\Edit\Fieldset\ReturnSumary;

use Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemInterface;
use Magestore\PurchaseOrderSuccess\Block\Adminhtml\ReturnOrder\Edit\Fieldset\ReturnSumary\Item\Delete;

/**
 * Class Item
 *
 * @package Magestore\PurchaseOrderCustomization\Rewrite\PurchaseOrder\Block\Adminhtml\Edit\Fieldset\ReturnSumary
 */
class Item extends \Magestore\PurchaseOrderSuccess\Block\Adminhtml\ReturnOrder\Edit\Fieldset\ReturnSumary\Item
{
    /**
     * @var array
     */
    protected $editFields = ['qty_returned', 'cost'];

    /**
     * Prepare columns for grid return order item
     *
     * @return \Magestore\PurchaseOrderSuccess\Block\Adminhtml\ReturnOrder\Edit\Fieldset\ReturnSumary\Item
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            "in_return_order",
            [
                "type" => "checkbox",
                "name" => "in_return_order",
                "values" => $this->getSelectedProducts(),
                "index" => "product_id",
                "filter" => false,
                "use_index" => "product_id",
                "header_css_class" => "col-select col-massaction",
                "column_css_class" => "col-select col-massaction"
            ]
        );
        $this->addColumn(
            "product_sku",
            [
                "header" => __("SKU"),
                "index" => "product_sku",
                "sortable" => true,
            ]
        );
        $this->addColumn(
            "product_name",
            [
                "header" => __("Product Name"),
                "index" => "product_name",
                "sortable" => true,
            ]
        );
        $this->addColumn(
            "product_supplier_sku",
            [
                "header" => __("Supplier SKU"),
                "index" => "product_supplier_sku",
                "sortable" => true,
            ]
        );
        $this->addColumn(
            "cost",
            [
                "header" => __("Purchase Cost"),
                'renderer' => \Magestore\PurchaseOrderSuccess\Block\Adminhtml\Grid\Column\Renderer\Text::class,
                "index" => "cost",
                'type' => 'number',
                "editable" => true,
                "sortable" => true
            ]
        );
        $this->addColumn(
            "qty_returned",
            [
                "header" => __("Qty Returned"),
                'renderer' => \Magestore\PurchaseOrderSuccess\Block\Adminhtml\Grid\Column\Renderer\Text::class,
                "index" => "qty_returned",
                'type' => 'number',
                "editable" => true,
                "sortable" => true
            ]
        );
        $this->addColumn(
            "qty_transferred",
            [
                "header" => __("Qty Delivered"),
                "index" => "qty_transferred",
                'type' => 'number',
                "sortable" => true
            ]
        );
        $this->addColumn(
            "total_row",
            [
                "header" => __("Total Row"),
                'renderer'=> \Magestore\PurchaseOrderCustomization\Block\Adminhtml\Grid\Column\Renderer\TotalRow::class,
                "index" => "total_row",
                'type' => 'number',
            ]
        );
        $this->addColumn(
            "delete",
            [
                "header" => __("Action"),
                'renderer' => Delete::class,
                'filters' => false,
                'sortable' => false,
            ]
        );
        $this->modifyColumns();
        $this->_eventManager->dispatch('prepare_return_order_grid_item', ['object' => $this]);
        return \Magento\Backend\Block\Widget\Grid\Extended::_prepareColumns();
    }

    /**
     * Get selected products
     *
     * @return array
     */
    public function getSelectedProducts()
    {
        if (empty($this->selectedId)) {
            $collection = $this->getDataColllection()
                ->addFieldToFilter(ReturnOrderItemInterface::QTY_RETURNED, ['lteq' => 0]);
            /** @var ReturnOrderItemInterface $item */
            foreach ($collection as $item) {
                $this->selectedProductData[$item->getProductId()] = [
                    ReturnOrderItemInterface::QTY_RETURNED => $item->getQtyReturned(),
                    ReturnOrderItemInterface::QTY_RETURNED . '_old' => $item->getQtyReturned(),
                    'cost' => $item->getCost(),
                    'cost_old' => $item->getCost()
                ];
            }
            $this->selectedId = array_keys($this->selectedProductData);
        }
        return $this->selectedId;
    }
}
