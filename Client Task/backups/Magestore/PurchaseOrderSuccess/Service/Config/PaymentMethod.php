<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Service\Config;

use Magestore\PurchaseOrderSuccess\Api\Data\PaymentInterface;
use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\PaymentMethod as PaymentMethodOption;

/**
 * Service PaymentMethod Config
 */
class PaymentMethod
{
    const PURCHASE_ORDER_CONFIG_PATH = 'purchaseordersuccess/payment_method/payment_method';

    /**
     * @var \Magento\Config\Model\ConfigFactory
     */
    protected $configFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * PaymentMethod constructor.
     *
     * @param \Magento\Config\Model\ConfigFactory $configFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Config\Model\ConfigFactory $configFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->configFactory = $configFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Save new payment method
     *
     * @param array $params
     * @return array
     */
    public function saveConfig($params = [])
    {
        if (!$params[PaymentInterface::PAYMENT_METHOD]
            || $params[PaymentInterface::PAYMENT_METHOD] == PaymentMethodOption::OPTION_NEW_VALUE) {
            $params = $this->initNewConfig($params);
            $this->initAllConfigValue($params);
        }
        return $params;
    }

    /**
     * Init New Config
     *
     * @param array $params
     * @return array
     */
    public function initNewConfig($params = [])
    {
        $params[PaymentInterface::PAYMENT_METHOD] = $params['new_' . PaymentInterface::PAYMENT_METHOD];
        return $params;
    }

    /**
     * Init All Config Value
     *
     * @param array $params
     * @throws \Exception
     */
    public function initAllConfigValue($params = [])
    {
        $configValue = $this->scopeConfig->getValue(static::PURCHASE_ORDER_CONFIG_PATH);
        if (!is_array($configValue)) {
            /** @var \Magento\Framework\Serialize\SerializerInterface $serializer */
            $serializer = \Magento\Framework\App\ObjectManager::getInstance()
                ->create(\Magento\Framework\Serialize\SerializerInterface::class);
            try {
                $configValue = !$configValue ? [] : $serializer->unserialize($configValue);
            } catch (\exception $e) {
                $serializer = \Magento\Framework\App\ObjectManager::getInstance()
                    ->get(\Magento\Framework\Serialize\Serializer\Serialize::class);
                $configValue = !$configValue ? [] : $serializer->unserialize($configValue);
            }
            $currentConfig = $this->searchSubArray($configValue, 'name', $params[PaymentInterface::PAYMENT_METHOD]);
            if (!is_array($currentConfig)) {
                $this->saveNewConfig($configValue, $params[PaymentInterface::PAYMENT_METHOD]);
            }
        }
    }

    /**
     * Save New Config
     *
     * @param array $configValue
     * @param string $newConfig
     * @throws \Exception
     */
    public function saveNewConfig($configValue, $newConfig)
    {
        $date = new \DateTime();
        $configValue[$date->getTimestamp()] = $this->generateNewConfig($newConfig);
        $config = $this->configFactory->create();
        $config->setDataByPath(
            static::PURCHASE_ORDER_CONFIG_PATH,
            $configValue
        );
        try {
            $config->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Generate new element config.
     *
     * @param string $newConfig
     * @return array
     */
    public function generateNewConfig($newConfig)
    {
        return [
            'name' => $newConfig,
            'status' => \Magestore\PurchaseOrderSuccess\Block\Adminhtml\Form\Field\Status::ENABLE_VALUE
        ];
    }

    /**
     * Search an subarray with key and value itself
     *
     * @param array $array
     * @param string $key
     * @param string $value
     * @return array|null
     */
    public function searchSubArray($array, $key, $value)
    {
        foreach ($array as $subarray) {
            if (isset($subarray[$key]) && $subarray[$key] == $value) {
                return $subarray;
            }
        }
        return [];
    }
}
