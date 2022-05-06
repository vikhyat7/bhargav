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
 * GiftQuote  model class
 */
class Giftquote extends \Magento\Framework\Model\AbstractModel
{
	/**
	 * init of model
	 */
	protected function _construct()
    {
        $this->_init('Mageants\GiftCertificate\Model\ResourceModel\Giftquote');
    }
}