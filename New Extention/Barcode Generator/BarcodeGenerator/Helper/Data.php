<?php
/**
 * @category Mageants BarcodeGenerator
 * @package Mageants_BarcodeGenerator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\BarcodeGenerator\Helper;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
     /**
      * scope config
      *
      * @var \Magento\Framework\App\Config\ScopeConfigInterface
      */
    protected $_scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

     /**
      * @var array
      */
    protected $_pdftemplatesConfig;
    
    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
    }
    
    public function isEnable()
    {
        $modulestates = $this->_scopeConfig->getValue(
            'mageants_barcodegenerator/barcodegenerator/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $modulestates;
    }

    public function isEnableQr()
    {

        $modulestates = $this->_scopeConfig->getValue(
            'mageants_barcodegenerator/barcodegenerator_setting/enable_qr',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        // echo $modulestates;exit();
        return $modulestates;
    }

    public function barcodeType()
    {
        $modulestates = $this->_scopeConfig->getValue(
            'mageants_barcodegenerator/barcodegenerator_setting/btype',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $modulestates;
    }

    public function barcodePrefix()
    {
        $modulestates = $this->_scopeConfig->getValue(
            'mageants_barcodegenerator/barcodegenerator_setting/barcode_prefix',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $modulestates;
    }
    
    public function barcodeAtrribute()
    {
        $modulestates = $this->_scopeConfig->getValue(
            'mageants_barcodegenerator/barcodegenerator_setting/pro_atr',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $modulestates;
    }
    
    public function pdfPageWidth()
    {
        $modulestates = $this->_scopeConfig->getValue(
            'mageants_barcodegenerator/barcodegenerator_setting/page_width',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $modulestates;
    }

    public function pdfPageHeight()
    {
        $modulestates = $this->_scopeConfig->getValue(
            'mageants_barcodegenerator/barcodegenerator_setting/page_height',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $modulestates;
    }

    public function isLogoEnble()
    {
        $modulestates = $this->_scopeConfig->getValue(
            'mageants_barcodegenerator/barcodegenerator_setting/logo_enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $modulestates;
    }

    public function logoImage()
    {
        $modulestates = $this->_scopeConfig->getValue(
            'mageants_barcodegenerator/barcodegenerator_setting/company_logo',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $modulestates;
    }
    
    public function descriptionAtrr()
    {
        $modulestates = $this->_scopeConfig->getValue(
            'mageants_barcodegenerator/barcodegenerator_setting/desc_attribute',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $descriptionAtrr = explode(",", $modulestates);
        return $descriptionAtrr;
    }
}
