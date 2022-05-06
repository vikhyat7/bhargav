<?php
/**
 * @category Mageants Richsnippets
 * @package Mageants_Richsnippets
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\Richsnippets\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Categorytype implements OptionSourceInterface
{
    /**
     * Get Grid row status type labels array.
     * @return array
     */
    public function toOptionArray()
    {
        $options = ['1' => __('Default(Long)'),'0' => __('Short')];
        return $options;
    }
}
