<?php
/**
 * @category Mageants CustomStockStatus
 * @package Mageants_CustomStockStatus
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <info@mageants.com>
 */
namespace Mageants\CustomStockStatus\Model\ResourceModel\CustomStockRule;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    public $idFieldName = 'id';
    
    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            'Mageants\CustomStockStatus\Model\CustomStockRule',
            'Mageants\CustomStockStatus\Model\ResourceModel\CustomStockRule'
        );
    }
}
