<?php


namespace Mageants\Orderattachment\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class AttachmentDisplayOptions implements ArrayInterface
{
    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Do Not Display'),
                'value' => ''
            ],
            [
                'label' => __('On Shipping '),
                'value' => 'after-shipping-methods'
            ],
            [
                'label' => __('On Payment '),
                'value' => 'after-payment-methods'
            ]
        ];
    }
}
