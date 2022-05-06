<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCertificate\Model\ResourceModel\Codeset;
/** 
 * Codeset model collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{	
	/**
	 * Id Filed
	 *
	 * @var \Mageants\GiftCertificate\Model\Codeset
	 */
	protected $_idFieldName = \Mageants\GiftCertificate\Model\Codeset::code_set_id;

	/**
     * init constructor
     */
    protected function _construct()
    {
        $this->_init('Mageants\GiftCertificate\Model\Codeset', 'Mageants\GiftCertificate\Model\ResourceModel\Codeset');
    }
}