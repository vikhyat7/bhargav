<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderCustomization\Rewrite\PurchaseOrder\Edit\Fieldset\PurchaseSumary;


/**
 * Class Item
 *
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD)
 * @package Magestore\PurchaseOrderCustomization\Rewrite\PurchaseOrder\Edit\Fieldset\PurchaseSumary
 */
class Item extends \Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Fieldset\PurchaseSumary\Item
{
    /**
     * Prepare columns for grid purchase order item
     *
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            "in_purchase_order",
            [
                "type" => "checkbox",
                "name" => "in_purchase_order",
                "values" => $this->getSelectedProducts(),
                "index" => "product_id",
                "filter" => false,
                "use_index" => "product_id",
                "header_css_class" => "col-select col-massaction",
                "column_css_class" => "col-select col-massaction"
            ]
        );
        $this->addColumn("product_sku",
            [
                "header" => __("SKU"),
                "index" => "product_sku",
                "sortable" => true,
            ]
        );
        $this->addColumn("product_name",
            [
                "header" => __("Product Name"),
                "index" => "product_name",
                "sortable" => true,
            ]
        );
        $this->addColumn("product_supplier_sku",
            [
                "header" => __("Supplier SKU"),
                "index" => "product_supplier_sku",
                "sortable" => true,
            ]
        );
        $purchaseOrder = $this->getCurrentPurchaseOrder();
        $this->addColumn("original_cost",
            [
                "header" => __("Current Cost") . '(' . $purchaseOrder->getCurrencyCode() . ')',
                "index" => "original_cost",
                'type' => 'number',
                "sortable" => true,
                'rate' => '1'
            ]
        );
        $this->addColumn("cost",
            [
                "header" => __("Purchase Cost") . ' (' . $purchaseOrder->getCurrencyCode() . ')',
                'renderer' => 'Magestore\PurchaseOrderSuccess\Block\Adminhtml\Grid\Column\Renderer\Text',
                "index" => "cost",
                'type' => 'number',
                "editable" => true,
                "sortable" => true
            ]
        );
        $this->addColumn("price_difference",
            [
                'renderer' => 'Magestore\PurchaseOrderCustomization\Block\Adminhtml\Grid\Column\Renderer\PriceDifference',
                "header" => __("Price Difference") . ' (' . $purchaseOrder->getCurrencyCode() . ')',
                "index" => "price_difference",
                'type' => 'number',
                "sortable" => true
            ]
        );
        $this->addColumn("tax",
            [
                "header" => __("Tax (%)"),
                'renderer' => 'Magestore\PurchaseOrderSuccess\Block\Adminhtml\Grid\Column\Renderer\Text',
                "index" => "tax",
                'type' => 'number',
                "editable" => true,
                "sortable" => true
            ]
        );
        $this->addColumn("discount",
            [
                "header" => __("Discount (%)"),
                'renderer' => 'Magestore\PurchaseOrderSuccess\Block\Adminhtml\Grid\Column\Renderer\Text',
                "index" => "discount",
                'type' => 'number',
                "editable" => true,
                "sortable" => true
            ]
        );
        $this->addColumn("qty_orderred",
            [
                "header" => __("Qty Orderring"),
                'renderer' => 'Magestore\PurchaseOrderSuccess\Block\Adminhtml\Grid\Column\Renderer\Text',
                "index" => "qty_orderred",
                'type' => 'number',
                "editable" => true,
                "sortable" => true
            ]
        );
        $this->addColumn("delete",
            [
                "header" => __("Action"),
                'renderer' => 'Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Fieldset\PurchaseSumary\Item\Delete',
                'filters' => false,
                'sortable' => false,
            ]
        );
        $this->modifyColumns();
        $this->_eventManager->dispatch('prepare_purchase_order_grid_item', ['object' => $this]);
        return \Magento\Backend\Block\Widget\Grid\Extended::_prepareColumns();
    }
}
