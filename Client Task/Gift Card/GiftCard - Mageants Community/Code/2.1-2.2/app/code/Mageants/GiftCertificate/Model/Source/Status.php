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
 * Status class
 */
class Status implements ArrayInterface
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
        ];
        return $options;
    }
}