<?php
/**
 * @category Mageants Richsnippets
 * @package Mageants_Richsnippets
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\Richsnippets\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Description implements OptionSourceInterface
{
    /**
     * Get Grid row status type labels array.
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            '0' => __('None'),
            '1' => __('Product Short Description'),
            '2' => __('Product Full Description'),
            '3' => __('Page Meta Description')
        ];
        return $options;
    }
}
