<?php
/**
 * @category Mageants StoreLocator
 * @package Mageants_StoreLocator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@Mageants.com>
 */
namespace Mageants\StoreLocator\Model\ResourceModel\ExportStore;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * collection for store
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    public $_idFieldName = 'store_id'; // @codingStandardsIgnoreLine

    /**
     * Define resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            \Mageants\StoreLocator\Model\ExportStore::class,
            \Mageants\StoreLocator\Model\ResourceModel\ExportStore::class
        );
    }
}
