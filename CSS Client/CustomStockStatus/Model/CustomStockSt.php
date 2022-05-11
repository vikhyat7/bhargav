<?php
/**
 * @category Mageants CustomStockStatus
 * @package Mageants_CustomStockStatus
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <info@mageants.com>
 */

namespace Mageants\CustomStockStatus\Model;

class CustomStockSt extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Mageants\CustomStockStatus\Model\ResourceModel\CustomStockSt');
    }
}
