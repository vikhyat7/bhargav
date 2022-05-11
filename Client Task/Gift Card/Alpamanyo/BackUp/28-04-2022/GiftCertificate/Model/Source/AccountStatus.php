<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCertificate\Model\Source;
use Magento\Framework\Option\ArrayInterface;
/** 
 * Account status class
 */
class AccountStatus implements ArrayInterface
{
    /**
     * @return Array
     */
    public function toOptionArray()
    {
        $options = [
            0 => [
                'label' => 'Inactive',
                'value' => 0
            ],
            1 => [
                'label' => 'Active',
                'value' => 1
            ],
            2 => [
                'label' => 'Expired',
                'value' => 2
            ],
            3 => [
                'label' => 'Used',
                'value' => 3
            ],
        ];
        return $options;
    }
}