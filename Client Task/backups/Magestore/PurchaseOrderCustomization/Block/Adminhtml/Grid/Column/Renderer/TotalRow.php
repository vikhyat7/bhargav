<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderCustomization\Block\Adminhtml\Grid\Column\Renderer;

use Magento\Framework\DataObject;

/**
 * Class TotalRow
 *
 * @package Magestore\PurchaseOrderCustomization\Block\Adminhtml\Grid\Column\Renderer
 */
class TotalRow extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
    /**
     * Render
     *
     * @param DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        $value = $this->_getValue($row);
        return number_format((float)$value, 4, '.', '');
    }
}
