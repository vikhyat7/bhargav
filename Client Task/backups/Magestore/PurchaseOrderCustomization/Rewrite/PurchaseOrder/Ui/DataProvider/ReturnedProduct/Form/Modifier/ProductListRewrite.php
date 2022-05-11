<?php

namespace Magestore\PurchaseOrderCustomization\Rewrite\PurchaseOrder\Ui\DataProvider\ReturnedProduct\Form\Modifier;

use Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\ReturnedProduct\Form\Modifier\ProductList;

/**
 * Class ProductListRewrite
 *
 * @package Magestore\PurchaseOrderCustomization\Rewrite\PurchaseOrder\Ui\DataProvider\ReturnedProduct\Form\Modifier
 */
class ProductListRewrite extends ProductList
{
    protected $mapFields = [
        'id' => 'product_id',
        'product_sku' => 'product_sku',
        'product_name' => 'product_name',
        'product_supplier_sku' => 'product_supplier_sku',
        'available_qty' => 'available_qty',
        'cost' => 'cost'
    ];

    /**
     * Fill meta columns
     *
     * @return array
     */
    public function fillModifierMeta()
    {
        $result = parent::fillModifierMeta();
        $result['cost'] =  $this->getTextColumn('cost', false, 'Purchase Cost', 15);
        return $result;
    }
}
