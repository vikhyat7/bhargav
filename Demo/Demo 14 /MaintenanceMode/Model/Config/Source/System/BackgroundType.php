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
 * Class BackgroundType
 *
 * @package Mageants\MaintenanceMode\Model\Config\Source\System
 */
class BackgroundType implements ArrayInterface
{
    const VIDEO           = 'video';
    const IMAGE           = 'image';
    const MULTIPLE_IMAGES = 'multiple_images';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::VIDEO, 'label' => __('Video')],
            ['value' => self::IMAGE, 'label' => __('Image')],
            ['value' => self::MULTIPLE_IMAGES, 'label' => __('Multiple Images')]
        ];
    }
}
