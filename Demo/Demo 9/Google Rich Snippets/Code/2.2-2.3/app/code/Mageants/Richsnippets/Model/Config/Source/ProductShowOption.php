<?php
/**
 * @category Mageants Richsnippets
 * @package Mageants_Richsnippets
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\Richsnippets\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ProductShowOption implements OptionSourceInterface
{
    /**
     * Get Grid row status type labels array.
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            '0' => __('Main Offer'),
            '1' => __('List Of Associated Product Offers'),
            '2' => __('Aggregate Offers')
        ];
        return $options;
    }
}
