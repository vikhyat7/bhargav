<?php
 

namespace Mageants\BarcodeGenerator\Model\Config\Source;

class DiscriptionAttributes implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        
        $DiscriptionAttr = [];
        $DiscriptionAttr[0] = [
            'label' => "Product Name",
            'value' => "ProductName",
        ];
        $DiscriptionAttr[1] = [
            'label' => "Sku",
            'value' => "Sku",
        ];
        $DiscriptionAttr[2] = [
            'label' => "Price",
            'value' => "Price",
        ];
        $DiscriptionAttr[3] = [
            'label' => "Product Qty",
            'value' => "Qty",
        ];
        $DiscriptionAttr[4] = [
            'label' => "Product Status",
            'value' => "Status",
        ];
        $DiscriptionAttr[5] = [
            'label' => "URL Key",
            'value' => "URL",
        ];
        
        return $DiscriptionAttr;
    }
}
