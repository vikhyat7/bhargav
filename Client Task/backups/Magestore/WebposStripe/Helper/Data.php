<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposStripe\Helper;

/**
 * Helper Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $_objectManager;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        parent::__construct($context);
    }

    /**
     * Get store
     *
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getStore()
    {
        return $this->_storeManager->getStore();
    }

    /**
     * Get store config
     *
     * @param string $path
     * @return string
     */
    public function getStoreConfig($path)
    {
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Stripe Config
     *
     * @return array
     */
    public function getStripeConfig()
    {
        $configData = [];
        $configItems = [
            'enable',
            'sort_order',
            'publishable_key',
            'api_key'
        ];
        foreach ($configItems as $configItem) {
            $configData[$configItem] = $this->getStoreConfig('webpos/payment/stripe/' . $configItem);
        }
        return $configData;
    }

    /**
     * Is Enable Stripe
     *
     * @return bool
     */
    public function isEnableStripe()
    {
        $enable = $this->getStoreConfig('webpos/payment/stripe/enable');
        return ($enable == 1) ? true : false;
    }

    /**
     * Is Zero Decimal
     *
     * @param string $currency
     * @return bool
     */
    public function isZeroDecimal($currency)
    {
        return in_array(
            strtolower($currency),
            [
                'bif',
                'djf',
                'jpy',
                'krw',
                'pyg',
                'vnd',
                'xaf',
                'xpf',
                'clp',
                'gnf',
                'kmf',
                'mga',
                'rwf',
                'vuv',
                'xof'
            ]
        );
    }

    /**
     * Get Secret Key
     *
     * @return string|null
     */
    public function getSecretKey()
    {
        return $this->getStoreConfig('webpos/payment/stripe/api_key');
    }

    /**
     * Get object manager
     *
     * @return \Magento\Framework\App\ObjectManager
     */
    public function getObjectManager()
    {
        return $this->_objectManager;
    }
}
