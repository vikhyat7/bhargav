<?php
/**
 * @category Mageants CustomStockStatus
 * @package Mageants_CustomStockStatus
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <info@mageants.com>
 */

namespace Mageants\CustomStockStatus\Plugin;

use \Magento\Sales\Block\Adminhtml\Order\View\Items\Renderer\DefaultRenderer;

class DefaultRendererPlugin
{
    public $stockHelper;

    public function __construct(
        \Mageants\CustomStockStatus\Helper\Data $stockHelper
    ) {
        $this->stockHelper = $stockHelper;
    }
    public function aroundGetColumnHtml(
        DefaultRenderer $defaultRenderer,
        \Closure $proceed,
        \Magento\Framework\DataObject $item,
        $column,
        $field = null
    ) {
        $defaultRenderer = $defaultRenderer;
        if ($column == 'custom-stock') {
            $html = '';
            if ($item->getProductType() == "configurable" || $item->getProductType() == "grouped") {
                try{
                    $ordered_product = $this->stockHelper->getProductBySku($item->getSku());
                }catch (\Magento\Framework\Exception\NoSuchEntityException $e){
                    // $ordered_product = false;
                    $ordered_product = $this->stockHelper->getLoadProduct($item->getProductId());
                }
            } else {
                $ordered_product = $this->stockHelper->getLoadProduct($item->getProductId());
            }
            if ($this->stockHelper->getDisplayOrderItemGrid()) {
                $stockItems = $this->stockHelper->getStockItem($item->getProductId());
                $html .= $stockItems->getIsInStock() ? __('In Stock')." " : ('Out of stock')." ";
            }
            if ($ordered_product->getData('mageants_custom_stock_status') && $ordered_product->getData('mageants_custom_stock_rule') && $ordered_product->getData('mageants_qty_base_rule_status') !== null) {
                $icons = $this->stockHelper->getCustomStockLabel(
                    $ordered_product->getData('mageants_custom_stock_status'),
                    $ordered_product->getData('mageants_custom_stock_rule'),
                    $ordered_product->getData('mageants_qty_base_rule_status'),
                    $item->getProductId()
                );
            }
            if (empty($icons) && ($item->getProductType() == "configurable" || $item->getProductType() == "grouped")) {
                $ordered_product = $this->stockHelper->getLoadProduct($item->getProductId());
                $icons = $this->stockHelper->getCustomStockLabel(
                    $ordered_product->getData('mageants_custom_stock_status'),
                    $ordered_product->getData('mageants_custom_stock_rule'),
                    $ordered_product->getData('mageants_qty_base_rule_status'),
                    $item->getProductId()
                );
            }
            //echo $icons['icon']; exit;
            if (@$icons['icon'] !="" && $this->stockHelper->getDisplayOrderItemGrid()) {
                $html.="<img src={$icons['icon']} alt={$icons['label']} title='stock status' />";
                echo  __($ordered_product->getResource()->getAttribute('mageants_custom_stock_status')->getFrontend()->getValue($ordered_product)); 
            }
            elseif (@$icons['icon'] == "" && $this->stockHelper->getDisplayOrderItemGrid()) 
            {echo  __($ordered_product->getResource()->getAttribute('mageants_custom_stock_status')->getFrontend()->getValue($ordered_product));}
            // echo  __($ordered_product->getResource()->getAttribute('mageants_custom_stock_status')->getFrontend()->getValue($ordered_product)); 
            $result = $html;
        } else {
            if ($field) {
                $result = $proceed($item, $column, $field);
            } else {
                $result = $proceed($item, $column);
            }
        }

        return $result;
    }
}
