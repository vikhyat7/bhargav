<?php
/**
 * @category Mageants MaintenanceMode
 * @package Mageants_MaintenanceMode
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\MaintenanceMode\Model\Config\Source\System;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class LayoutOptions
 *
 * @package Mageants\MaintenanceMode\Model\Config\Source\System
 */
class LayoutOptions implements ArrayInterface
{
    const SINGLE_COLUMN      = 'single';
    const DOUBLE_COLUMN      = 'double';
    const DOUBLE_LEFT_COLUMN = 'double_left';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::SINGLE_COLUMN, 'label' => __('Single-column')],
            ['value' => self::DOUBLE_COLUMN, 'label' => __('Double-columns')],
            ['value' => self::DOUBLE_LEFT_COLUMN, 'label' => __('Double-columns with Left-side content')]
        ];
    }
}
