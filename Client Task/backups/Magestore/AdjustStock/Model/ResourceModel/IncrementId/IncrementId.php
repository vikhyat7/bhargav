<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\AdjustStock\Model\ResourceModel\IncrementId;

/**
 * Class IncrementId
 *
 * Increment id resource model
 */
class IncrementId extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('os_increment_id', 'increment_id');
    }
}
