<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCertificate\Model\Config\Source;
/**
 * Type classs 
 */ 
class Type implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return Array
     */
    public function toOptionArray()
    {
        $options=array();
        $options = [
            0 => [
                'label' => 'Simple Product',
                'value' => 'simple'
            ],
            1 => [
                'label' => 'Grouped Product',
                'value' => 'grouped'
            ],
            2 => [
                'label' => 'Configurable Product',
                'value' => 'configurable'
            ],
            3 => [
                'label' => 'Virtual Product',
                'value' => 'virtual'
            ],
            4 => [
                'label' => 'Downloadable Product',
                'value' => 'downloadable'
            ],
        ];
        return $options;
    }
}
