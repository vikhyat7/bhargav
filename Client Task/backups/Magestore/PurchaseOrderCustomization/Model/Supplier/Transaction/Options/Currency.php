<?php

namespace Magestore\PurchaseOrderCustomization\Model\Supplier\Transaction\Options;

/**
 * Class Currency
 *
 * @package Magestore\PurchaseOrderCustomization\Model\Supplier\Transaction\Options
 */
class Currency implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * To Option Type Array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'KWD', 'label' => 'KWD'],
            ['value' => 'USD', 'label' => 'USD'],
            ['value' => 'EUR', 'label' => 'EUR'],
            ['value' => 'AED', 'label' => 'AED'],
            ['value' => 'SAR', 'label' => 'SAR'],
            ['value' => 'CNY', 'label' => 'CNY'],
        ];
    }
}
