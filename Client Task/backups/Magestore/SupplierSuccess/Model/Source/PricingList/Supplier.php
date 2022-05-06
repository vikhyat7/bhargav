<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Model\Source\PricingList;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class IsActive
 */
class Supplier extends \Magestore\SupplierSuccess\Model\Source\AbstractSource implements OptionSourceInterface
{

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = $this->pricingListService->getSupplierOptions();
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
