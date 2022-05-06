<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Model\Source;

/**
 * Class GiftType
 *
 * Source - Gift type model
 */
class GiftType extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    const GIFT_TYPE_NONE = '';
    const GIFT_TYPE_FIX = 1;
    const GIFT_TYPE_RANGE = 2;
    const GIFT_TYPE_DROPDOWN = 3;

    /**
     * Get model option as array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                [
                    'label' => __('-- Please Select --'),
                    'value' => self::GIFT_TYPE_NONE
                ],
                [
                    'label' => __('Fixed value'),
                    'value' => self::GIFT_TYPE_FIX
                ],
                [
                    'label' => __('Range of values'),
                    'value' => self::GIFT_TYPE_RANGE
                ],
                [
                    'label' => __('Dropdown values'),
                    'value' => self::GIFT_TYPE_DROPDOWN
                ],
            ];
        }
        return $this->_options;
    }
}
