<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderCustomization\Rewrite\Block\Adminhtml\Email\EmailToSupplier;

use Magestore\PurchaseOrderSuccess\Block\Adminhtml\Email\EmailToSupplier\Total as TotalParent;

/**
 * Class Total
 *
 * @package Magestore\PurchaseOrderCustomization\Rewrite\Block\Adminhtml\Email\EmailToSupplier
 */
class Total extends TotalParent
{
    protected $_template = 'Magestore_PurchaseOrderCustomization::email/email_to_supplier/total.phtml';
}
