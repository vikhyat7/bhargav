<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderCustomization\Block\Adminhtml\Grid\Column\Renderer;

use Magento\Framework\DataObject;

/**
 * Class PriceDifference
 *
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD)
 * @package Magestore\PurchaseOrderCustomization\Block\Adminhtml\Grid\Column\Renderer
 */
class PriceDifference extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
    /**
     * Renders grid column
     *
     * @param   Object $row
     * @return  string
     */
    public function render(DataObject $row)
    {
        if((float)$this->_getValue($row) != 0){
            $result = '<div class="admin__grid-control">';
            $result .= '<span style="color: red">'. $this->_getValue($row) .'</span>';
            return $result;
        }
        return $this->_getValue($row);
    }
}
