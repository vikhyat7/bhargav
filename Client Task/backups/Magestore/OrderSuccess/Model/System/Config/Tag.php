<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Model\System\Config;

/**
 * System config Tag
 */
class Tag extends \Magento\Framework\App\Config\Value
{
    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $directoryHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serialized;

    /**
     * Tag constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->directoryHelper = $directoryHelper;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
        $this->_storeManager = $storeManager;
        $this->serialized = \Magento\Framework\App\ObjectManager::getInstance()
            ->create(\Magento\Framework\Serialize\Serializer\Serialize::class);
    }

    /**
     * Before Save
     *
     * @return Tag|void
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        if (is_array($value)) {
            unset($value['__empty']);
        }
        $this->setValue($value);
        if (is_array($this->getValue())) {
            $this->setValue($this->serialized->serialize($this->getValue()));
        }
        parent::beforeSave();
    }

    /**
     * After Load
     *
     * @return Tag|void
     */
    public function afterLoad()
    {
        if (!is_array($this->getValue())) {
            $value = $this->getValue();
            $this->setValue(empty($value) ? false : $this->serialized->unserialize($value));
        }
    }
}
