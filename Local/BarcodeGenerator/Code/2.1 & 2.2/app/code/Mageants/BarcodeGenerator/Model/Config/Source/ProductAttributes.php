<?php
 

namespace Mageants\BarcodeGenerator\Model\Config\Source;

class ProductAttributes implements \Magento\Framework\Data\OptionSourceInterface
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'Barcode',  'label' => __('Barcode')],
            ['value' => 'SKU',  'label' => __('SKU')],
            ['value' => 'ProductName',  'label' => __('Product Name')],
        ];
    }
}
