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
 * Class DatePicker
 *
 * @package Mageants\MaintenanceMode\Block\Adminhtml\System
 */
class DatePicker extends Field
{
    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $html = $element->getElementHtml();

        $html .= '<script type="text/javascript">
        require(["jquery", "jquery/ui", "mage/calendar"], function ($) {
            $(document).ready(function () {
                $("#' . $element->getHtmlId() . '").datetimepicker({dateFormat: "m/d/y", ampm: true});
                var picker = $(".ui-datepicker-trigger");
                picker.removeAttr("style");
                picker.click(function(){
                    $("#' . $element->getHtmlId() . '").focus();
                });
            });
        });
        </script>';

        return $html;
    }
}
