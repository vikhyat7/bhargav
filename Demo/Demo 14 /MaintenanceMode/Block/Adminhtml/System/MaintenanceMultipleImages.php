<?php
/**
 * @category Mageants MaintenanceMode
 * @package Mageants_MaintenanceMode
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\MaintenanceMode\Block\Adminhtml\System;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class MaintenanceMultipleImages
 * @package Mageants\MaintenanceMode\Block\Adminhtml\System
 */
class MaintenanceMultipleImages extends MultipleImages
{
    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $html = '';
        $html .= $this->setMultiImgElement()->setResponse('maintenance')->toHtml();

        return $html;
    }
}
