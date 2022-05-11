<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCertificate\Model;
use Magento\Framework\Exception\LocalizedException as CoreException;
/**
 * Codeset Model class
 */
class Codeset extends \Magento\Framework\Model\AbstractModel
{
	/**
	 * Init Model class
	 */
  	protected function _construct()
    {
        $this->_init('Mageants\GiftCertificate\Model\ResourceModel\Codeset');
    }
}