<?php
/**
 * @category Mageants MaintenanceMode
 * @package Mageants_MaintenanceMode
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\MaintenanceMode\Model\Config\Backend;

use Magento\Framework\App\Config\Value;
use Mageants\MaintenanceMode\Helper\Data as HelperData;

/**
 * Class MultipleImages
 *
 * @package Mageants\MaintenanceMode\Model\Config\Backend
 */
class MultipleImages extends Value
{
    /**
     * @return Value
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        $files = [];

        /* looping through array */
        /**
         * @var array $value
         */
        foreach ($value as $key => $item) {
            if (!empty($files) && in_array($item['file'], $files, true)) {
                unset($value[$key]);
            }
            $files[] = $item['file'];  // creating username array to compare with main array values
        }
        foreach ($value as $key => $item) {
            if ($item['removed'] === '1') {
                unset($value[$key]);
            }
        }

        if (is_array($value)) {
            unset($value['__empty']);
            $this->setValue(HelperData::jsonEncode($value));
        }

        return parent::beforeSave();
    }
}
