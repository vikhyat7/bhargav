<?php
/**
 * @category Mageants StoreLocator
 * @package Mageants_StoreLocator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@Mageants.com>
 */
namespace Mageants\StoreLocator\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Locator Address tab
 */
class ExportStore extends AbstractModel
{

    /**
     * init store
     */
    public function _construct()
    {
        $this->_init(
            \Mageants\StoreLocator\Model\ResourceModel\ExportStore::class
        );
    }
    
    /**
     * fetch product for store
     *
     * @param \Mageants\StoreLocator\Model\ExportStore $object
     * @return $string
     */
    //@codingStandardsIgnoreStart
    public function getProducts(\Mageants\StoreLocator\Model\ExportStore $object)
    {
        $tbl = $this->getResource()->getTable(
            \Mageants\StoreLocator\Model\ResourceModel\ExportStore::TBL_ATT_PRODUCT
        );
        $select = $this->getResource()->getConnection()->select()->from(
            $tbl,
            ['product_id']
        )
        ->where(
            'store_id = ?',
            (int)$object->getId()
        );
        return $this->getResource()->getConnection()->fetchCol($select);
    }
    //@codingStandardsIgnoreStart
}
