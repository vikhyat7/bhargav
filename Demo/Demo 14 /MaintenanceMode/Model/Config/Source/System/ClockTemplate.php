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
 * Class ClockTemplate
 *
 * @package Mageants\MaintenanceMode\Model\Config\Source\System
 */
class ClockTemplate implements ArrayInterface
{    
    const SIMPLE = 'simple';
    const CIRCLE = 'circle';
    const SQUARE = 'square';
    const STACK  = 'stack';
    const MODERN = 'modern';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [            
            ['value' => self::CIRCLE, 'label' => __('Circle')],
            ['value' => self::SQUARE, 'label' => __('Square')],
            ['value' => self::STACK, 'label' => __('Stack')],
            ['value' => self::MODERN, 'label' => __('Modern')]
        ];
    }
}
