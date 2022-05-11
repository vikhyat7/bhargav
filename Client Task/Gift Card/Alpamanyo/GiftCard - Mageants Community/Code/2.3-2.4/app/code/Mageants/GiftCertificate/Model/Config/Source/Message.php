<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCertificate\Model\Config\Source;
/**
 * Message class
 */ 
class Message extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
	/**
     * @return Array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options=  [ ['value' => 0, 'label' => __('No')],
            ['value' => 1, 'label' => __('Yes')]];
        }
        return $this->_options;
    }
}
