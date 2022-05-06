<?php
/**
 * @category Mageants MaintenanceMode
 * @package Mageants_MaintenanceMode
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\MaintenanceMode\Model\Config\Backend;

use Exception;
use Magento\Framework\App\Config\Value;

/**
 * Class Validate
 * @package Mageants\MaintenanceMode\Model\Config\Backend
 */
class Validate extends Value
{
    /**
     * Check value not null Exclude and Include
     *
     * @return Value
     * @throws Exception
     */
    public function beforeSave()
    {
        $allowedIps = $this->getData('fieldset_data')['whitelist_ip'];
        $whitelistIp = explode(',', $allowedIps);
        foreach ($whitelistIp as $ips) {
            if (!empty($ips) && !filter_var($ips, FILTER_VALIDATE_IP)) {
                throw new Exception(__('Whitelist IP(s) '.$ips.' is not a valid IP address!'));
            }
        }

        return parent::beforeSave();
    }
}
