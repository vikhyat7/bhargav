<?php
/**
 * @category Mageants MaintenanceMode
 * @package Mageants_MaintenanceMode
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\MaintenanceMode\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageants\MaintenanceMode\Helper\AbstractData;
use Magento\Customer\Model\Customer;

/**
 * Class Data
 *
 * @package Mageants\MaintenanceMode\Helper
 */
class Data extends AbstractData
{
    const CONFIG_MODULE_PATH = 'maintenancemode';
    const MAINTENANCE_ROUTE  = 'mpmaintenance';

    /**
     * @var Customer
     */
    protected $_customer;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param Customer $customer
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        Customer $customer
    ) {
        $this->_customer = $customer;
        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @param string $code
     * @param null $storeId
     *
     * @return array|mixed
     */
    public function getConfigGeneral($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(static::CONFIG_MODULE_PATH . '/general' . $code, $storeId);
    }

    /**
     * @param $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getClockSetting($code, $storeId = null)
    {
        return $this->getModuleConfig('display_setting/clock_setting/' . $code, $storeId);
    }

    /**
     * @param $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getSubscribeSetting($code, $storeId = null)
    {
        return $this->getModuleConfig('display_setting/subscribe_setting/' . $code, $storeId);
    }

    /**
     * @param $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getSocialSetting($code, $storeId = null)
    {
        return $this->getModuleConfig('display_setting/social_contact/' . $code, $storeId);
    }

    /**
     * @param $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getMaintenanceSetting($code, $storeId = null)
    {
        return $this->getModuleConfig('maintenance_setting/' . $code, $storeId);
    }

    /**
     * @param $code
     * @param null $storeId
     *
     * @return mixed
     */

    /**
     * @return mixed|string
     */
    public function getMaintenanceRoute()
    {
        $maintenanceRoute = $this->getMaintenanceSetting('maintenance_route');

        return isset($maintenanceRoute) ? $maintenanceRoute : self::MAINTENANCE_ROUTE;
    }

    /**
     * @return mixed|string
     */
    
    /**
     * @param string $ip
     * @param string $range
     *
     * @return bool
     */
    public function checkIp($ip, $range)
    {
        if (strpos($range, '*') !== false) {
            $high = $range;
            $low  = $high;
            if (strpos($range, '-') !== false) {
                // [$low, $high] = explode('-', $range, 2);
                $low = explode('-', $range, 2);
                $high = explode('-', $range, 2);
            }
            $low   = str_replace('*', '0', $low);
            $high  = str_replace('*', '255', $high);
            $range = $low . '-' . $high;
        }
        if (strpos($range, '-') !== false) {
            // [$low, $high] = explode('-', $range, 2);
            $low = explode('-', $range, 2);
            $high = explode('-', $range, 2);

            return $this->ipCompare($ip, $low, 1) && $this->ipcompare($ip, $high, -1);
        }

        return $this->ipCompare($ip, $range);
    }

    /**
     * @param $ip1
     * @param $ip2
     * @param int $op
     *
     * @return bool
     */
    private function ipCompare($ip1, $ip2, $op = 0)
    {
        $ip1Arr = explode('.', $ip1);
        $ip2Arr = explode('.', $ip2);

        for ($i = 0; $i < 4; $i++) {
            if ($ip1Arr[$i] < $ip2Arr[$i]) {
                return ($op === -1);
            }
            if ($ip1Arr[$i] > $ip2Arr[$i]) {
                return ($op === 1);
            }
        }

        return ($op === 0);
    }

    /**
     * @return array|mixed
     */
    public function getWhitelistIp()
    {
        return $this->getConfigGeneral('whitelist_ip');
    }

    /**
     * @return array|mixed
     */
    public function getStartTime()
    {
        return $this->getConfigGeneral('start_time');
    }

    /**
     * @return array|mixed
     */
    public function getEndTime()
    {
        return $this->getConfigGeneral('end_time');
    }

    /**
     * @return array|mixed
     */
    public function getWhitelistPage()
    {
        return $this->getConfigGeneral('whitelist_page');
    }

    /**
     * @return mixed
     */
    public function getClientIp()
    {
        $addressPath = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_REAL_IP',
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'REMOTE_ADDR'
        ];
        foreach ($addressPath as $path) {
            $ip = $this->_request->getServer($path);
            if ($ip) {
                if (strpos($ip, ',') !== false) {
                    $addresses = explode(',', $ip);
                    foreach ($addresses as $address) {
                        if (trim($address) !== '127.0.0.1') {
                            return trim($address);
                        }
                    }
                } elseif ($ip !== '127.0.0.1') {
                    return $ip;
                }
            }
        }

        return $this->_request->getClientIp();
    }

    /**
     * @return array
     */
    public function getCustomerEmail(){
        $emails = [];
        $customers = $this->_customer->getCollection()->addAttributeToSelect("*")->load();

        foreach ($customers as $customer){
            $emails[] = $customer->getEmail();
        }

        return $emails;
    }
}
