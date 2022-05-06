<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderCustomization\Rewrite\Block\Adminhtml\Email\EmailToSupplier;

use Magestore\PurchaseOrderSuccess\Block\Adminhtml\Email\EmailToSupplier\Header as HeaderParent;

/**
 * Class Header
 *
 * @package Magestore\PurchaseOrderCustomization\Rewrite\Block\Adminhtml\Email\EmailToSupplier
 */
class Header extends HeaderParent
{
    protected $_template = 'Magestore_PurchaseOrderCustomization::email/email_to_supplier/header.phtml';
}
