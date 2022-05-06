<?php

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Storepickup
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Storepickup\Model;

use Magento\Framework\Exception\LocalizedException;
use Magestore\Storepickup\Model\Schedule\Option\WeekdayStatus;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Store
 *
 * Used to create model store
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Store extends AbstractModel
{
    const MAX_COUNT_TIME_CHECK_URL_REWRITE = 100;

    const MARKER_ICON_RELATIVE_PATH = 'magestore/storepickup/images/store/marker';

    /**
     * number of seconds in a day.
     */
    const TIME_DAY = 86400;

    /**
     * method id getter.
     */
    const METHOD_GET_TAG_ID = 1;
    const METHOD_GET_HOLIDAY_ID = 2;
    const METHOD_GET_SPECIALDAY_ID = 3;
    const METHOD_GET_ORDER_ID = 4;

    const STOREPICUP_PENDING = 0;
    const STOREPICUP_PROCESSION = 1;
    const STOREPICUP_COMPLETED = 2;

    /**
     * mapping method builder.
     *
     * @var array
     */
    protected $_methodGetters = [
        self::METHOD_GET_TAG_ID => 'getTagIds',
        self::METHOD_GET_HOLIDAY_ID => 'getHolidayIds',
        self::METHOD_GET_SPECIALDAY_ID => 'getSpecialdayIds',
        self::METHOD_GET_ORDER_ID => 'getHolidayIds',
    ];

    /* @var $_storeId Support Multiple Store */

    protected $_storeId = null;

    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $_regionFactory;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonHelper;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $_countryFactory;

    /**
     * @var \Magestore\Storepickup\Model\ResourceModel\Store\CollectionFactory
     */
    protected $_storeCollectionFactory;

    /**
     * @var \Magestore\Storepickup\Model\ResourceModel\Specialday\CollectionFactory
     */
    protected $_specialdayCollectionFactory;

    /**
     * @var \Magestore\Storepickup\Model\ResourceModel\Holiday\CollectionFactory
     */
    protected $_holidayCollectionFactory;

    /**
     * @var \Magestore\Storepickup\Model\ResourceModel\Image\CollectionFactory
     */
    protected $_imageCollectionFactory;

    /**
     * @var \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory
     */
    protected $_urlRewriteCollectionFactory;

    /**
     * @var \Magestore\Storepickup\Model\SystemConfig
     */
    protected $_systemConfig;

    /**
     * @var StoreUrlPathGeneratorInterface
     */
    protected $_storeUrlPathGenerator;

    /**
     * @var StoreUrlRewriteGeneratorInterface
     */
    protected $_storeUrlRewriteGenerator;

    /**
     * @var UrlPersistInterface
     */
    protected $_urlPersist;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magestore\Storepickup\Helper\Url
     */
    protected $_storepickupHelperUrl;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magestore\Storepickup\Helper\Data
     */
    protected $storePickupHelper;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\Filesystem\DriverInterface
     */
    protected $driver;

    /**
     * Store constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param ResourceModel\Store\CollectionFactory $storeCollectionFactory
     * @param ResourceModel\Specialday\CollectionFactory $specialdayCollectionFactory
     * @param ResourceModel\Holiday\CollectionFactory $holidayCollectionFactory
     * @param ResourceModel\Image\CollectionFactory $imageCollectionFactory
     * @param \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory $urlRewriteCollectionFactory
     * @param SystemConfig $systemConfig
     * @param StoreUrlPathGeneratorInterface $storeUrlPathGenerator
     * @param StoreUrlRewriteGeneratorInterface $storeUrlRewriteGenerator
     * @param UrlPersistInterface $urlPersist
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magestore\Storepickup\Helper\Url $storepickupHelperUrl
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magestore\Storepickup\Helper\Data $storePickupHelper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Filesystem\DriverInterface $driver
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magestore\Storepickup\Model\ResourceModel\Store\CollectionFactory $storeCollectionFactory,
        \Magestore\Storepickup\Model\ResourceModel\Specialday\CollectionFactory $specialdayCollectionFactory,
        \Magestore\Storepickup\Model\ResourceModel\Holiday\CollectionFactory $holidayCollectionFactory,
        \Magestore\Storepickup\Model\ResourceModel\Image\CollectionFactory $imageCollectionFactory,
        \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory $urlRewriteCollectionFactory,
        \Magestore\Storepickup\Model\SystemConfig $systemConfig,
        StoreUrlPathGeneratorInterface $storeUrlPathGenerator,
        StoreUrlRewriteGeneratorInterface $storeUrlRewriteGenerator,
        UrlPersistInterface $urlPersist,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magestore\Storepickup\Helper\Url $storepickupHelperUrl,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magestore\Storepickup\Helper\Data $storePickupHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Filesystem\DriverInterface $driver,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_regionFactory = $regionFactory;
        $this->_jsonHelper = $jsonHelper;
        $this->_countryFactory = $countryFactory;

        $this->_storeCollectionFactory = $storeCollectionFactory;
        $this->_specialdayCollectionFactory = $specialdayCollectionFactory;
        $this->_holidayCollectionFactory = $holidayCollectionFactory;
        $this->_imageCollectionFactory = $imageCollectionFactory;
        $this->_urlRewriteCollectionFactory = $urlRewriteCollectionFactory;

        $this->_systemConfig = $systemConfig;
        $this->_storeUrlPathGenerator = $storeUrlPathGenerator;
        $this->_storeUrlRewriteGenerator = $storeUrlRewriteGenerator;
        $this->_urlPersist = $urlPersist;

        $this->_storeManager = $storeManager;
        $this->_storepickupHelperUrl = $storepickupHelperUrl;
        $this->_scopeConfig = $scopeConfig;
        $this->storePickupHelper = $storePickupHelper;
        $this->objectManager = $objectManager;
        $this->driver = $driver;
    }

    /**
     * Model construct that should be used for object initialization.
     */
    protected function _construct()
    {
        $this->_init(
            \Magestore\Storepickup\Model\ResourceModel\Store::class
        );
    }

    /**
     * Processing object before save data.
     */
    public function beforeSave()
    {
        $this->_prepareStateValue()
            ->_prepareScheduleIdValue()
            ->_prepareRewriteRequestPath()
            ->createMSISource();

        return parent::beforeSave();
    }

    /**
     * Prepare rewrite request path
     *
     * @return $this
     * @throws LocalizedException
     */
    public function _prepareRewriteRequestPath()
    {
        $urlKey = $this->getData('rewrite_request_path');
        if ($urlKey === '' || $urlKey === null) {
            $this->setData(
                'rewrite_request_path',
                $this->_storeUrlPathGenerator->generateUrlKey($this)
            );
        }

        /**
         * check exists rewrite request path with limit the time by MAX_COUNT_TIME_CHECK_URL_REWRITE
         */
        $checkNth = 0;
        while ($this->_checkExistsRewriteRequestPath($this->getData('rewrite_request_path'))
            && $checkNth++ < self::MAX_COUNT_TIME_CHECK_URL_REWRITE
        ) {
            $this->setData('rewrite_request_path', $this->getData('rewrite_request_path') . '-' . $this->getId());
        }

        if ($checkNth > self::MAX_COUNT_TIME_CHECK_URL_REWRITE) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __(
                    'The url key is already exists and system can not generate it automatic. '
                    .'You need to specified an other url key!'
                )
            );
        }

        return $this;
    }

    /**
     * Check existing rewrite request path.
     *
     * @param string $rewriteRequestPath
     *
     * @return bool
     */
    public function _checkExistsRewriteRequestPath($rewriteRequestPath)
    {
        /** @var \Magestore\Storepickup\Model\ResourceModel\Store\Collection $storeCollection */
        $storeCollection = $this->_storeCollectionFactory->create();
        $storeCollection->addFieldToFilter('storepickup_id', ['neq' => $this->getId()])
            ->addFieldToFilter('rewrite_request_path', $rewriteRequestPath);
        if ($storeCollection->getSize()) {
            return true;
        }

        /** @var \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection $urlRewriteCollection */
        $urlRewriteCollection = $this->_urlRewriteCollectionFactory->create();
        $urlRewriteCollection->addFieldToFilter('request_path', $rewriteRequestPath)
            ->addFieldToFilter('target_path', ['neq' => $this->_storeUrlPathGenerator->getCanonicalUrlPath($this)]);
        return $urlRewriteCollection->getSize();
    }

    /**
     * Prepare schedule value before save.
     *
     * @return $this
     */
    public function _prepareScheduleIdValue()
    {
        if ($this->hasData('schedule_id') && !$this->getData('schedule_id')) {
            $this->setData('schedule_id', new \Zend_Db_Expr('NULL'));
        }

        return $this;
    }

    /**
     * Prepare state value before save.
     *
     * @return $this
     */
    public function _prepareStateValue()
    {
        if ($this->hasData('state_id')) {
            $region = $this->_regionFactory->create()->load($this->getData('state_id'));
            if ($region->getId()) {
                $this->setData('state', $region->getName());
            }
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function afterSave()
    {
        parent::afterSave();

        $this->_saveSerializeData()
            ->_saveImageData()
            ->_makeUrlRewrite();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        parent::afterDelete();

        $this->_getResource()->deleteUrlRewrite($this);

        return $this;
    }

    /**
     * Make url rewrite after save store.
     *
     * @return $this
     */
    public function _makeUrlRewrite()
    {
        if ($this->dataHasChangedFor('rewrite_request_path')) {
            $urls = $this->_storeUrlRewriteGenerator->generate($this);
            $this->_urlPersist->replace($urls);
        }

        return $this;
    }

    /**
     * Prepare data image.
     *
     * @return $this
     */
    public function _saveImageData()
    {
        if ($this->hasData('arrayImages') && is_array($this->getData('arrayImages'))) {
            $this->_getResource()->saveImagesData(
                $this,
                $this->decodeImageJsonData($this->getData('arrayImages'))
            );
        }

        return $this;
    }

    /**
     * Decode image json data.
     *
     * @param array $imagesJsonData
     * @return array
     */
    public function decodeImageJsonData(array $imagesJsonData = [])
    {
        if (!$this->getId()) {
            return [];
        }

        $deleteImages = [];
        $baseImage = null;
        $insertImages = [];

        foreach ($imagesJsonData as $imageJson) {
            $imageData = $this->_jsonHelper->jsonDecode($imageJson);
            if (isset($imageData['image_id'])) {
                // the images are existed in database
                if (isset($imageData['remove'])) {
                    // the images need to remove
                    $deleteImages[] = $imageData['image_id'];
                } elseif (isset($imageData['base'])) {
                    // the image need to make is base image.
                    $baseImage = $imageData['file'];
                }
            } elseif (!isset($imageData['remove'])) {
                // the new images need to insert to database
                $path = \Magestore\Storepickup\Model\Image::IMAGE_GALLERY_PATH . $imageData['file'];
                $imageItem = [
                    'path' => $path,
                    'pickup_id' => $this->getId(),
                ];

                if (isset($imageData['base'])) {
                    $baseImage = $path;
                }

                $insertImages[] = $imageItem;
            }
        }

        return [
            'deleteImages' => $deleteImages,
            'baseImage' => $baseImage,
            'insertImages' => $insertImages,
        ];
    }

    /**
     * Save serialize data.
     *
     * @return $this
     */
    public function _saveSerializeData()
    {
        if ($this->hasData('in_tag_ids')) {
            $this->assignTags($this->getData('in_tag_ids'));
        }

        if ($this->hasData('in_holiday_ids')) {
            $this->assignHolidays($this->getData('in_holiday_ids'));
        }

        if ($this->hasData('in_specialday_ids')) {
            $this->assignSpecialdays($this->getData('in_specialday_ids'));
        }

        return $this;
    }

    /**
     * Assign Tags to Store.
     *
     * @param array $tagIds
     * @return $this
     * @throws LocalizedException
     */
    public function assignTags(array $tagIds = [])
    {
        $this->_getResource()->assignTags($this, $tagIds);

        return $this;
    }

    /**
     * Assign Holidays to Store.
     *
     * @param array $holidayIds
     * @return $this
     * @throws LocalizedException
     */
    public function assignHolidays(array $holidayIds = [])
    {
        $this->_getResource()->assignHolidays($this, $holidayIds);

        return $this;
    }

    /**
     * Assign Specialdays to Store.
     *
     * @param array $specialdayIds
     * @return $this
     * @throws LocalizedException
     */
    public function assignSpecialdays(array $specialdayIds = [])
    {
        $this->_getResource()->assignSpecialdays($this, $specialdayIds);

        return $this;
    }

    /**
     * Get state id by name.
     *
     * @return mixed
     */
    public function getStateId()
    {
        $region = $this->_regionFactory->create()
            ->loadByName($this->getData('state'), $this->getData('country_id'));

        return $region->getId();
    }

    /**
     * Get Country Name by code.
     *
     * @return string
     */
    public function getCountryName()
    {
        $country = $this->_countryFactory->create()->loadByCode($this->getCountryId());

        return $country->getName();
    }

    /**
     * Run build method.
     *
     * @param int $methodId
     * @return mixed
     * @throws LocalizedException
     */
    public function runGetterMethod($methodId)
    {
        if (!isset($this->_methodGetters[$methodId])) {
            throw new LocalizedException(__('Method of %1 is not exists !', get_class($this)));
        }

        $getterMethod = $this->_methodGetters[$methodId];

        return $this->$getterMethod();
    }

    /**
     * Get tag ids in Store.
     */
    public function getTagIds()
    {
        return $this->_getResource()->getTagIds($this);
    }

    /**
     * Load base image of store.
     *
     * @return $this
     * @throws LocalizedException
     */
    public function loadBaseImage()
    {
        $this->_getResource()->loadBaseImage($this);

        return $this;
    }

    /**
     * Get specialday data.
     *
     * @return array
     */
    public function getSpecialdaysData()
    {
        /** @var \Magestore\Storepickup\Model\ResourceModel\Specialday\Collection $collection */
        $collection = $this->_filterDays($this->getSpecialdays());

        $days = [];
        $key = 0;
        $timeDay = self::TIME_DAY;

        foreach ($collection as $item) {
            $days[$key]['name'] = $item->getSpecialdayName();
            $days[$key]['time_open'] = $item->getTimeOpen();
            $days[$key]['time_close'] = $item->getTimeClose();
            $dateFrom = strtotime($item->getDateFrom());
            $dateTo = strtotime($item->getDateTo());

            while ($dateFrom <= $dateTo) {
                $days[$key]['date'][] = date('Y-m-d', $dateFrom);
                $dateFrom += $timeDay;
            }

            ++$key;
        }

        return $days;
    }

    /**
     * Filter specialdays, holidays.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection
     */
    public function _filterDays(\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection)
    {
        $dayShow = $this->_systemConfig->getLimitStoreDays();
        $dateStart = date('Y-m-d');
        $dateEnd = date('Y-m-d', strtotime(date('Y-m-d')) + $dayShow * self::TIME_DAY);

        $collection->getSelect()->where('date_from <= date_to');
        $collection->addFieldToFilter('date_to', ['gteq' => $dateStart])
            ->addFieldToFilter('date_from', ['lteq' => $dateEnd]);

        return $collection;
    }

    /**
     * Get Specialday Collection  of Store.
     *
     * @return \Magestore\Storepickup\Model\ResourceModel\Specialday\Collection
     */
    public function getSpecialdays()
    {
        /** @var \Magestore\Storepickup\Model\ResourceModel\Specialday\Collection $collection */
        $collection = $this->_specialdayCollectionFactory->create();
        $collection->addFieldToFilter('specialday_id', ['in' => $this->getSpecialdayIds()]);

        return $collection;
    }

    /**
     * Get holiday ids of store.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     */
    public function getSpecialdayIds()
    {
        return $this->_getResource()->getSpecialdayIds($this);
    }

    /**
     * Get holidays data.
     *
     * @return array
     */
    public function getHolidaysData()
    {
        /** @var \Magestore\Storepickup\Model\ResourceModel\Holiday\Collection $collection */
        $collection = $this->_filterDays($this->getHolidays());

        $days = [];
        $key = 0;
        $timeDay = self::TIME_DAY;

        foreach ($collection as $item) {
            $days[$key]['name'] = $item->getHolidayName();
            $dateFrom = strtotime($item->getDateFrom());
            $dateTo = strtotime($item->getDateTo());

            while ($dateFrom <= $dateTo) {
                $days[$key]['date'][] = date('Y-m-d', $dateFrom);
                $dateFrom += $timeDay;
            }

            ++$key;
        }

        return $days;
    }

    /**
     * Get Holiday collection of Store.
     *
     * @return \Magestore\Storepickup\Model\ResourceModel\Specialday\Collection
     */
    public function getHolidays()
    {
        /** @var \Magestore\Storepickup\Model\ResourceModel\Holiday\Collection $collection */
        $collection = $this->_holidayCollectionFactory->create();
        $collection->addFieldToFilter('holiday_id', ['in' => $this->getHolidayIds()]);

        return $collection;
    }

    /**
     * Get holiday ids of store.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     */
    public function getHolidayIds()
    {
        return $this->_getResource()->getHolidayIds($this);
    }

    /**
     * Has break time
     *
     * @param string $day
     * @return bool
     */
    public function hasBreakTime($day)
    {
        return $this->isOpenday($day)
        && $this->getData($day . '_open') < $this->getData($day . '_open_break')
        && $this->getData($day . '_open_break') < $this->getData($day . '_close_break')
        && $this->getData($day . '_close_break') < $this->getData($day . '_close');
    }

    /**
     * Check store is open in a day.
     *
     * @param string $day
     * @return bool
     */
    public function isOpenday($day)
    {
        return $this->getScheduleId()
        && $this->isEnabled()
        && $this->getData($day . '_status') == WeekdayStatus::WEEKDAY_STATUS_OPEN
        && $this->getData($day . '_open') < $this->getData($day . '_close');
    }

    /**
     * Check store is enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getStatus() == \Magestore\Storepickup\Model\Status::STATUS_ENABLED ? true : false;
    }

    /**
     * Check store has holiday.
     *
     * @return int
     */
    public function countHolidays()
    {
        return $this->_getResource()->countHolidays($this);
    }

    /**
     * Check store has specialday.
     *
     * @return int
     */
    public function countSpecialdays()
    {
        return $this->_getResource()->countSpecialdays($this);
    }

    /**
     * Get first Image of store.
     *
     * @return \Magento\Framework\DataObject
     */
    public function getFirstImage()
    {
        return $this->getImages()->setPageSize(1)->setCurPage(1)->getFirstItem();
    }

    /**
     * Get Image collection of store.
     *
     * @return \Magestore\Storepickup\Model\ResourceModel\Image\Collection
     */
    public function getImages()
    {
        /** @var  \Magestore\Storepickup\Model\ResourceModel\Image\Collection $collection */
        $collection = $this->_imageCollectionFactory->create();
        $collection->addFieldToFilter('pickup_id', $this->getId());

        return $collection;
    }

    /**
     * Get meta title.
     *
     * @return mixed
     */
    public function getMetaTitle()
    {
        return $this->getData('meta_title') ? $this->getData('meta_title') : $this->getStoreName();
    }

    /**
     * Get meta description.
     *
     * @return mixed
     */
    public function getMetaDescription()
    {
        return $this->getData('meta_description') ? $this->getData('meta_description') : $this->getStoreName();
    }

    /**
     * Get meta keywords.
     *
     * @return mixed
     */
    public function getMetaKeywords()
    {
        return $this->getData('meta_keywords') ? $this->getData('meta_keywords') : $this->getStoreName();
    }

    /**
     * Import
     *
     * @return bool|mixed
     * @throws \Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function import()
    {
        $data = $this->getData();

        //prepare status
        $data['status'] = 1;
        //check exited store
        $collection = $this->getCollection()
            ->addFieldToFilter('store_name', $data['store_name'])
            ->addFieldToFilter('address', $data['address']);

        if (count($collection)) {
            return false;
        }

        if (!isset($data['store_name']) || $data['store_name'] == '') {
            return false;
        }
        if (!isset($data['address']) || $data['address'] == '') {
            return false;
        }
        if (!isset($data['city']) || $data['city'] == '') {
            return false;
        }
        if (!isset($data['country_id']) || $data['country_id'] == '') {
            return false;
        }
        if (!isset($data['zipcode']) || $data['zipcode'] == '') {
            return false;
        }

        $storeName = strtolower(trim($data['store_name'], ' '));

        $storeName = $this->_storepickupHelperUrl->characterSpecial($storeName);
        $check = 1;
        while ($check == 0) {
            $stores = $this->getCollection()
                ->addFieldToFilter('url_id_path', $storeName)
                ->getFirstItem();

            if ($stores->getId()) {
                $storeName = $storeName . '-1';
            } else {
                $check = 0;
            }
        }

        $data['url_id_path'] = $storeName;

        $this->setData($data);
        $this->save();

        $allstores = $this->_storeManager->getStores();
        foreach ($allstores as $store) {
            $this->setStoreId($store->getStoreId())
                ->updateUrlKey();
        }

        return $this->getId();
    }

    /**
     * Set store id
     *
     * @param int $value
     * @return $this
     */
    public function setStoreId($value)
    {
        $this->_storeId = $value;
        return $this;
    }

    /**
     * Get store id
     *
     * @return Support|null
     */
    public function getStoreId()
    {
        return $this->_storeId;
    }

    /**
     * Update url key
     *
     * @param string $rewriteRequestPath
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function updateUrlKey($rewriteRequestPath = '')
    {
        $id = $this->getId();
        $storeId = $this->_storeId;
        if (!$storeId) {
            $storeId = 0;
        }

        $url_key = $rewriteRequestPath ? $rewriteRequestPath : $this->getData('url_id_path');
        $url_suffix = $this->_scopeConfig->getValue(
            'catalog/seo/product_url_suffix',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->_storeManager->getStore()->getStoreId()
        );
        $urlrewrite = $this->loadByIdpath($url_key, $storeId);

        // $urlrewrite->setData('id_path', $url_key);
        $urlrewrite->setData('request_path', 'storepickup/' . $url_key . $url_suffix);
        $urlrewrite->setData('target_path', 'storepickup/index/index/viewstore/' . $id);
        $urlrewrite->setData('store_id', $storeId);

        try {
            $urlrewrite->save();
        } catch (\Exception $e) {
            return $this;
        }
    }

    /**
     * Load by id path
     *
     * @param string $idPath
     * @param int $storeId
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function loadByIdpath($idPath, $storeId)
    {
        $model = $this->_urlRewriteCollectionFactory->create()
            ->addFieldToFilter('store_id', $storeId)
            ->getFirstItem();

        return $model;
    }

    /**
     * Reindex
     *
     * @return $this
     */
    public function reindex()
    {
        $address = $this->getData('address') . ', '
            . $this->getData('city') . ' ' . $this->getData('state')
            . ' ' . $this->getData('country') . ' ' . $this->getData('zipcode');
        $location = $this->geocode($address);
        if ($location) {
            $this->setData('latitude', $location[0]);
            $this->setData('longitude', $location[1]);
        }
        return $this;
    }

    /**
     * Geo code
     *
     * @param string $address
     * @return array|bool
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function geocode($address)
    {

        // url encode the address
        $address = urlencode($address);

        // google map geocode api url
        $url = "http://maps.google.com/maps/api/geocode/json?address={$address}";

        // get the json response
        $resp_json = $this->driver->fileGetContents($url);

        // decode the json
        $resp = json_decode($resp_json, true);

        // response status will be 'OK', if able to geocode given address
        if ($resp['status'] == 'OK') {

            // get the important data
            $lati = $resp['results'][0]['geometry']['location']['lat'];
            $longi = $resp['results'][0]['geometry']['location']['lng'];
            $formatted_address = $resp['results'][0]['formatted_address'];

            // verify if data is complete
            if ($lati && $longi && $formatted_address) {

                // put the data in the array
                $data_arr = [];

                array_push(
                    $data_arr,
                    $lati,
                    $longi,
                    $formatted_address
                );

                return $data_arr;

            } else {
                return false;
            }

        } else {
            return false;
        }
    }

    /**
     * Create MSI source
     *
     * @return $this
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function createMSISource()
    {
        if ($this->storePickupHelper->isMSISourceEnable()) {
            /** @var \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository */
            $sourceRepository = $this->objectManager->create(
                \Magento\InventoryApi\Api\SourceRepositoryInterface::class
            );
            if (!$this->getId() && $this->getSourceCode() === 'Create a new one') {
                $newSourceCode = $this->getNewSourceCode();
                if (!$newSourceCode) {
                    throw new LocalizedException(__('Please enter source code if you want to create a new one'));
                }
                if (strpos($newSourceCode, ' ') !== false) {
                    throw new LocalizedException(__('Source code does not allow white space'));
                }
                try {
                    $sourceRepository->get($newSourceCode);
                } catch (\Exception $checkSourceCodeException) {
                    /** @var \Magento\InventoryApi\Api\Data\SourceInterface $source */
                    $source = $this->objectManager->create(\Magento\InventoryApi\Api\Data\SourceInterface::class);
                    $source->setName($this->getStoreName());
                    $source->setSourceCode($newSourceCode);
                    $source->setContactName($this->getContactName());
                    $source->setEmail($this->getEmail());
                    $source->setPhone($this->getPhone());
                    $source->setFax($this->getFax());
                    $source->setCountryId($this->getCountryId());
                    $source->setRegionId($this->getStateId());
                    $source->setRegion($this->getState());
                    $source->setCity($this->getCity());
                    $source->setStreet($this->getAddress());
                    $source->setPostcode($this->getZipcode());
                    try {
                        $sourceRepository->save($source);
                    } catch (\Exception $saveSourceException) {
                        try {
                            $sourceRepository->get($newSourceCode);
                        } catch (\Exception $getSourceException) {
                            throw $saveSourceException;
                        }
                        throw new LocalizedException(
                            __('There is already a Source with this code exist, please create a different code.')
                        );
                    }
                    $source->getSourceCode();
                    $this->setSourceCode($source->getSourceCode());
                    $this->getNewSourceCode(null);
                    if ($this->getSourceCode()) {
                        $sourceRepository->get($this->getSourceCode());
                    }
                    return $this;
                }
                throw new LocalizedException(
                    __('There is already a Source with this code exist, please create a different code.')
                );
            }
        }
        return $this;
    }
}
