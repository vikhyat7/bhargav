<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCertificate\Model\ResourceModel\Templates;
/** 
 * Templates model collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{	
	/**
     * init constructor
     */
	protected function _construct()
    {
        $this->_init('Mageants\GiftCertificate\Model\Templates', 'Mageants\GiftCertificate\Model\ResourceModel\Templates');
    }
}