<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Controller\Adminhtml\Staff\Role;

/**
 * class \Magestore\Appadmin\Controller\Adminhtml\Staff\Role\Staff
 *
 * Staff tab
 * Methods:
 *  execute
 *
 * @category    Magestore
 * @package     Magestore\Appadmin\Controller\Adminhtml\Role
 * @module      Appadmin
 * @author      Magestore Developer
 */
class Staff extends \Magestore\Appadmin\Controller\Adminhtml\Staff\Role
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultLayout = $this->resultLayoutFactory->create();
        $resultLayout->getLayout()->getBlock('role.edit.tab.staff')
            ->setStaffs($this->getRequest()->getPost('ostaff', null));
        return $resultLayout;
    }
}
