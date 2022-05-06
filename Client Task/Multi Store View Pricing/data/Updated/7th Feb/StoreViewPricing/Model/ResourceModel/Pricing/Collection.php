<?php
/**
 * @category Mageants StoreViewPricing
 * @package Mageants_StoreViewPricing
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\StoreViewPricing\Model\ResourceModel\Pricing;

/**
 * Pricing model collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * init constructor
     */
    public function _construct()
    {
        $this->_init(
            \Mageants\StoreViewPricing\Model\Pricing::class,
            \Mageants\StoreViewPricing\Model\ResourceModel\Pricing::class
        );
    }
}
