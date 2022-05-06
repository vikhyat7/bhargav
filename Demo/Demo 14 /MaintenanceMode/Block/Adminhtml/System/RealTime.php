<?php
/**
 * @category Mageants MaintenanceMode
 * @package Mageants_MaintenanceMode
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\MaintenanceMode\Block\Adminhtml\System;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class RealTime
 *
 * @package Mageants\MaintenanceMode\Block\Adminhtml\System
 */
class RealTime extends Field
{
    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $html = '';
        $html .= $this->_localeDate->date()->format('F j, Y');
        $html .= '<br>';
        $html .= $this->_localeDate->date()->format('g:i A');

        return $html;
    }
}
