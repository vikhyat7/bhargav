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
class Map extends \Magento\Framework\View\Element\Template
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
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Directory\Block\Data $directoryBlock
    ) {
        $this->_storeCollection = $storeCollection;
        $this->_helper = $helper;
        $this->_filesystem = $context->getFilesystem();
        $this->_imageFactory = $imageFactory;
        $this->_objectManager = $objectmanager;
        $this->directoryBlock = $directoryBlock;
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
     * Get store location title
     *
     * @return $this
     */
    public function getStoreLocatorTitle()
    {
        return $this->_scopeConfig->getValue(
            'StoreLocator/general/title',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get store location title
     *
     * @return $this
     */
    public function getMaxRadius()
    {
        return $this->_scopeConfig->getValue(
            'StoreLocator/general/maxradius',
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
    public function getStoreCollection()
    {
        $storeId=$this->getStoreId();
        $storeIds=['0' =>'0','1'=>$storeId];
        $collection = $this->_storeCollection->getCollection()
                    ->addFieldToFilter('sstatus', 'Enabled')
                    ->addFieldToFilter('storeId', ['in'=>$storeIds])
                    ->setOrder('position', 'ASC');
        
        return $collection;
    }
    
    /**
     * get Store Collection
     *
     * @return $collection
     */
    public function getAreaStoreCollection($storename, $countryid, $state, $city, $zipcode)
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
            $collection = $collection->addFieldToFilter('sname', ['like' => '%'.$storename.'%']);
        }

        if ($countryid!='') {
            $collection = $collection->addFieldToFilter('country', $countryid);
        }

        if ($state!='') {
            $collection = $collection->addFieldToFilter('region', ['like' => '%'.$state.'%']);
        }

        if ($city!='') {
            $collection = $collection->addFieldToFilter('city', ['like' => '%'.$city.'%']);
        }

        if ($zipcode!='') {
            $collection = $collection->addFieldToFilter('postcode', ['like' => '%'.$zipcode.'%']);
        }
        return $collection;
    }

    /**
     * Get store collection within range
     *
     * @return $collection
     */
    public function getRangeStoreCollection($current, $distance)
    {
        unset($collection);
        unset($StoreCollection);
        $storeId=$this->getStoreId();
        $storeIds=['0' =>'0','1'=>$storeId];
        $StoreCollection = $this->_storeCollection->getCollection()
                        ->addFieldToFilter('storeId', ['in'=>$storeIds])
                        ->setOrder('position', 'ASC');
        $point = [];
        foreach ($StoreCollection as $store) {
            $point1='1';
            $point1=$this->getDistance($current, $store['latitude'], $store['longitude'], "K", $distance);
            if ($point1!='') {
                $point[]=$store['store_id'];
            }
        }
        if ($point instanceof Countable > 0) {
            $collection = $this->_storeCollection->getCollection()
                        ->addFieldToFilter('storeId', ['in'=>$storeIds])
                        ->addFieldToFilter('sstatus', 'Enabled')
                        ->addFieldToFilter('store_id', ['in' => [$point]])
                        ->setOrder('position', 'ASC');
            return $collection;
        } else {
            $point[] = 0;
            $collection = $this->_storeCollection->getCollection()
                    ->addFieldToFilter('sstatus', 'Enabled')
                    ->addFieldToFilter('storeId', ['in'=>$storeIds])
                    ->addFieldToFilter('store_id', ['in' => [$point]])
                    ->setOrder('position', 'ASC');
            return $collection;
        }
    }

    public function getStoreById($id)
    {
        $storeId=$this->getStoreId();
        $storeIds=['0' =>'0','1'=>$storeId];
        $collection = $this->_storeCollection->getCollection()
        ->addFieldToFilter('sstatus', 'Enabled')
        ->addFieldToFilter('storeId', ['in'=>$storeIds])
        ->addFieldToFilter('store_id', ['in'=>$id])->setOrder('position', 'ASC');
        return $collection;
    }
    
    public function getStoreNameById($id)
    {
        $storeId=$this->getStoreId();
        $storeIds=['0' =>'0','1'=>$storeId];
        $model = $this->_storeCollection->getCollection()
                ->addFieldToFilter('sstatus', 'Enabled')
                ->addFieldToFilter('storeId', ['in'=>$storeIds])
                ->addFieldToFilter('store_id', ['in'=>$id])
                ->setOrder('position', 'ASC');
        $storeurls=$model->getData();
        foreach ($storeurls as $storeurl) {
            return $storeurl['sname'];
        }
        return '';
    }
    
    public function getCountries()
    {
        $country = $this->directoryBlock->getCountryHtmlSelect();
        return $country;
    }

    public function getRegion()
    {
        $region = $this->directoryBlock->getRegionHtmlSelect();
        return $region;
    }

    /**
     * return distance from current location to store location
     *
     * @return $km
     */
    public function getDistance($addressFrom, $latitudeTo, $longitudeTo, $unit, $distance)
    {
        unset($geocodeFrom);
        unset($formattedAddrFrom);
        //Change address format
        $formattedAddrFrom = str_replace(' ', '+', $addressFrom);
       
        //Send request and receive json data
        //@codingStandardsIgnoreStart
        $geocodeFrom=file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.$formattedAddrFrom.'&key='.$this->getApiKey());
        //@codingStandardsIgnoreEnd
        $outputFrom = json_decode($geocodeFrom);
        
        $latitudeFrom = $outputFrom->results[0]->geometry->location->lat;
        $longitudeFrom = $outputFrom->results[0]->geometry->location->lng;
        
        //Calculate distance from latitude and longitude
        $theta = $longitudeFrom - $longitudeTo;
        $dist = sin(
            deg2rad($latitudeFrom)
        )
            * sin(deg2rad($latitudeTo))
            +  cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo))
            * cos(
                deg2rad($theta)
            );
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);
        $km=($miles * 1.609344);
        if ($km<$distance) {
            return round($km);
        }
    }

    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }
}
