<?php

namespace Magestore\PurchaseOrderCustomization\Model\Supplier\Transaction\Options;

/**
 * Class Type
 *
 * @package Magestore\PurchaseOrderCustomization\Model\Supplier\Transaction\Options
 */
class Type implements \Magento\Framework\Option\ArrayInterface
{
    const TYPE_DEBIT = 'debit';
    const TYPE_CREDIT = 'credit';

    /**
     * To Option Type Array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::TYPE_CREDIT,
                'label' => __('Credit')
            ],
            [
                'value' => self::TYPE_DEBIT,
                'label' => __('Debit')
            ],
        ];
    }
}
