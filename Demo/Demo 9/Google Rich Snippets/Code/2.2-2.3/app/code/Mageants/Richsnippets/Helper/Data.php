<?php
/**
 * @category Mageants Richsnippets
 * @package Mageants_Richsnippets
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\Richsnippets\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Get Store code
     *
     * @return string
     */
    public function getStoreCode()
    {
        return $this->_storeManager->getStore()->getCode();
    }

    /**
     * @return bool|string
     */
    public function getConfig($config_path)
    {
        $storeCode=$this->getStoreCode();
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeCode
        );
    }

    /**
     * @return bool
     */
    public function isBreadcrumsEnable()
    {
        return $this->getConfig('richsnippets/breadcrumbs/enable');
    }

    /**
     * @return bool
     */
    public function getCategoryPath()
    {
        return $this->getConfig('richsnippets/breadcrumbs/categorypath');
    }

    /**
     * @return bool
     */
    public function getCategoryType()
    {
        return $this->getConfig('richsnippets/breadcrumbs/categorytype');
    }

    /**
     * @return bool
     */
    public function isSiteIncludeInSearch()
    {
        return $this->getConfig('richsnippets/includesiteinsearch/enable');
    }

    /**
     * @return bool
     */
    public function getWebsiteName()
    {
        return $this->getConfig('richsnippets/includesiteinsearch/websitename');
    }

    /**
     * @return bool
     */
    public function isOrganizationEnable()
    {
        return $this->getConfig('richsnippets/organizations/enable');
    }

    /**
     * @return bool
     */
    public function getOrganizationName()
    {
        return $this->getConfig('richsnippets/organizations/organization');
    }

    /**
     * @return bool
     */
    public function getOrganizationLogoUrl()
    {
        return $this->getConfig('richsnippets/organizations/logourl');
    }

    /**
     * @return bool
     */
    public function getOrganizationdescription()
    {
        return $this->getConfig('richsnippets/organizations/description');
    }

    /**
     * @return bool
     */
    public function getOrganizationDescLength()
    {
        return $this->getConfig('richsnippets/organizations/descriptionlength');
    }

    /**
     * @return bool
     */
    public function getOrganizationCountry()
    {
        return $this->getConfig('richsnippets/organizations/country');
    }

    /**
     * @return bool
     */
    public function getOrganizationState()
    {
        return $this->getConfig('richsnippets/organizations/state');
    }

    /**
     * @return bool
     */
    public function getOrganizationZipCode()
    {
        return $this->getConfig('richsnippets/organizations/zipcode');
    }

    /**
     * @return bool
     */
    public function getOrganizationCity()
    {
        return $this->getConfig('richsnippets/organizations/city');
    }

    /**
     * @return bool
     */
    public function getOrganizationSales()
    {
        return $this->getConfig('richsnippets/organizations/sales');
    }

    /**
     * @return bool
     */
    public function getOrganizationTechSupport()
    {
        return $this->getConfig('richsnippets/organizations/technicalsupport');
    }

    /**
     * @return bool
     */
    public function getOrganizationCustomerService()
    {
        return $this->getConfig('richsnippets/organizations/customerservice');
    }

    /**
     * @return bool
     */
    public function isSocialProfileEnable()
    {
        return $this->getConfig('richsnippets/socialprofile/enable');
    }

    /**
     * @return bool
     */
    public function getFacebookLink()
    {
        return $this->getConfig('richsnippets/socialprofile/facebook');
    }

    /**
     * @return bool
     */
    public function getTwitterLink()
    {
        return $this->getConfig('richsnippets/socialprofile/twitter');
    }

    /**
     * @return bool
     */
    public function getGooglePlusLink()
    {
        return $this->getConfig('richsnippets/socialprofile/google');
    }

    /**
     * @return bool
     */
    public function getInstagramLink()
    {
        return $this->getConfig('richsnippets/socialprofile/instagram');
    }

    /**
     * @return bool
     */
    public function getYouTubeLink()
    {
        return $this->getConfig('richsnippets/socialprofile/youtube');
    }

    /**
     * @return bool
     */
    public function getLinkedInLink()
    {
        return $this->getConfig('richsnippets/socialprofile/linkedin');
    }

    /**
     * @return bool
     */
    public function getMySpaceLink()
    {
        return $this->getConfig('richsnippets/socialprofile/myspace');
    }

    /**
     * @return bool
     */
    public function getPinterestLink()
    {
        return $this->getConfig('richsnippets/socialprofile/pinterest');
    }

    /**
     * @return bool
     */
    public function getSoundCloudLink()
    {
        return $this->getConfig('richsnippets/socialprofile/soundcloud');
    }

    /**
     * @return bool
     */
    public function getTumblrLink()
    {
        return $this->getConfig('richsnippets/socialprofile/tumblr');
    }

    /**
     * @return bool
     */
    public function isSearchBoxEnable()
    {
        return $this->getConfig('richsnippets/searchbox/enable');
    }

    /**
     * @return bool
     */
    public function enabledonCategoryPage()
    {
        return $this->getConfig('richsnippets/enableoncategorypage/enable');
    }

    /**
     * @return bool
     */
    public function isProductRichDataEnable()
    {
        return $this->getConfig('richsnippets/productrichdata/enable');
    }

    /**
     * @return bool
     */
    public function getProductAvaiblityStatus()
    {
        return $this->getConfig('richsnippets/productrichdata/showavailability');
    }

    /**
     * @return bool
     */
    public function getShowConditionStatus()
    {
        return $this->getConfig('richsnippets/productrichdata/showcondition');
    }

    /**
     * @return bool
     */
    public function getConfigrableProductAs()
    {
        return $this->getConfig('richsnippets/productrichdata/configproductas');
    }

    /**
     * @return bool
     */
    public function getGroupedProductAs()
    {
        return $this->getConfig('richsnippets/productrichdata/groupedproductas');
    }

    /**
     * @return bool
     */
    public function getDescriptionType()
    {
        return $this->getConfig('richsnippets/productrichdata/description');
    }

    /**
     * @return bool
     */
    public function getRatingStatus()
    {
        return $this->getConfig('richsnippets/productrichdata/showrating');
    }

    /**
     * @return bool
     */
    public function getBrandName()
    {
        return $this->getConfig('richsnippets/productrichdata/brand');
    }

    /**
     * @return bool
     */
    public function getManufacturer()
    {
        return $this->getConfig('richsnippets/productrichdata/manufacturer');
    }
}
