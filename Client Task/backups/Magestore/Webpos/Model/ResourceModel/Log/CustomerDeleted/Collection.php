<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\ResourceModel\Log\CustomerDeleted;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {
    /**
     *
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Initialize collection resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\Webpos\Model\Log\CustomerDeleted', 'Magestore\Webpos\Model\ResourceModel\Log\CustomerDeleted');
    }
}