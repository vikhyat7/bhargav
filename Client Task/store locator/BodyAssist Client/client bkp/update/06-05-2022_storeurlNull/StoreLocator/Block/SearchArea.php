<?php
/**
 * @category Mageants StoreLocator
 * @package Mageants_StoreLocator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@Mageants.com>
 */
namespace Mageants\StoreLocator\Block;

use Magento\Store\Model\StoreManagerInterface;

/**
 * Locator GoogleMap
 */
class SearchArea extends \Magento\Framework\View\Element\Template
{
    /**
     * Current Store Collection
     *
     * @var \Mageants\StoreLocator\Model\ManageStore
     */
    public $storeCollection;

    public $objectManager;

    /**
     * File System
     *
     * @var \Magento\Framework\Filesystem
     */
    public $filesystem ;
    
    /**
     * Image Factory
     *
     * @var \Magento\Framework\Image\AdapterFactory
     */
    public $imageFactory;
    
    /**
     * @param \Magento\Backend\Block\Template\Context
     * @param \Mageants\StoreLocator\Helper\Data
     * @param \Mageants\StoreLocator\Model\ManageStore
     * @param \Magento\Framework\Filesystem
     * @param \Magento\Framework\Image\AdapterFactory
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Mageants\StoreLocator\Helper\Data $helper,
        \Mageants\StoreLocator\Model\ManageStore $storeCollection,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Framework\ObjectManagerInterface $objectmanager
    ) {
        $this->_storeCollection = $storeCollection;
        $this->_helper = $helper;
        $this->_filesystem = $context->getFilesystem();
        $this->_imageFactory = $imageFactory;
        $this->_objectManager = $objectmanager;
        $this->_storeManager = $context->getStoreManager();
        parent::__construct($context);
    }
    
    /**
     * Prepare Layout
     *
     * @return Parent::_prepareLayout
     */
    //@codingStandardsIgnoreStart
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    //@codingStandardsIgnoreEnd
    /**
     * Dispatch request
     *
     * @param RequestInterface $request
     * @return $dispatch
     */
    //@codingStandardsIgnoreStart
    public function dispatch(RequestInterface $request)
    {
        return parent::dispatch($request);
    }
    //@codingStandardsIgnoreEnd
    /**
     * Get Api key for GoogleMap
     *
     * @return $this
     */
    public function getApiKey()
    {
        return $this->_scopeConfig->getValue(
            'StoreLocator/map/map_key',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get store market template
     *
     * @return $this
     */
    public function getStoreMarkerTemplate()
    {
        return $this->_scopeConfig->getValue(
            'StoreLocator/general/mark_template',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * get Store Collection
     *
     * @return $collection
     */
    public function getStoreCollection($storename, $countryid, $state, $city, $zipcode)
    {
        $storeId=$this->getStoreId();
        $storeIds=['0' =>'0','1'=>$storeId];
        $storenameCollection="";
        $collection='';
        $collection = $this->_storeCollection->getCollection()
                    ->addFieldToFilter('sstatus', 'Enabled')
                    ->addFieldToFilter('storeId', ['in'=>$storeIds])
                    ->setOrder('position', 'ASC');

        if ($storename!='') {
            $collection = $collection
                ->addFieldToFilter('sname', ['like' => '%'.$storename.'%']);
        }

        if ($countryid!='') {
            $collection = $collection
                ->addFieldToFilter('country', $countryid);
        }

        if ($state!='') {
            $collection = $collection
                ->addFieldToFilter('region', ['like' => '%'.$state.'%']);
        }

        if ($city!='') {
            $collection = $collection
                ->addFieldToFilter('city', ['like' => '%'.$city.'%']);
        }

        if ($zipcode!='') {
            $collection = $collection
                ->addFieldToFilter('postcode', ['like' => '%'.$zipcode.'%']);
        }
        return $collection;
    }
    
    public function getCountryLatLong($countryid)
    {
        //@codingStandardsIgnoreStart
        $geocodeFrom=file_get_contents(
            'https://maps.googleapis.com/maps/api/geocode/json?address='.$countryid.'&key='.$this->getApiKey()
        );
        //@codingStandardsIgnoreStart
        $outputFrom = json_decode($geocodeFrom);
        
        $latitudeFrom = $outputFrom->results[0]->geometry->location->lat;
        $longitudeFrom = $outputFrom->results[0]->geometry->location->lng;

        return $latitudeFrom.",".$longitudeFrom;
    }

    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }
}
