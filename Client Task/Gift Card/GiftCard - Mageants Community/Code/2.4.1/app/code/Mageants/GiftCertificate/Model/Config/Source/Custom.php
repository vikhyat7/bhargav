<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCertificate\Model\Config\Source;
/**
 * custome class for return array
 */ 
class Custom implements \Magento\Framework\Option\ArrayInterface
{
	/**
     * @return Array
     */
    public function toOptionArray()
    {
       return [
            ['value' => 0, 'label' => __('No')],
            ['value' => 1, 'label' => __('Yes')],
        ];
    }
}