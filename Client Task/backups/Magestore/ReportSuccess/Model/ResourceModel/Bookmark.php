<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */
namespace Magestore\ReportSuccess\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Bookmark
 * @package Magestore\ReportSuccess\Model\ResourceModel
 */
class Bookmark extends AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('reportsuccess_bookmark', 'id');
    }
}
