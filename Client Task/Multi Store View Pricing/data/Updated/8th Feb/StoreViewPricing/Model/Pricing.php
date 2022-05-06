<?php
/**
 * @category Mageants StoreViewPricing
 * @package Mageants_StoreViewPricing
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\StoreViewPricing\Model;

use Magento\Framework\Exception\LocalizedException as CoreException;

/**
 * Pricing Model class
 */
class Pricing extends \Magento\Framework\Model\AbstractModel
{
    /**
     * init Model class
     */
    public function _construct()
    {
        $this->_init(\Mageants\StoreViewPricing\Model\ResourceModel\Pricing::class);
    }
}
