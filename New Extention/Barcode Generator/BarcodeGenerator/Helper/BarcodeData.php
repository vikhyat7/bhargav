<?php
/**
 * @category Mageants BarcodeGenerator
 * @package Mageants_BarcodeGenerator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\BarcodeGenerator\Helper;

use Magento\Store\Model\ScopeInterface;
use Mageants\BarcodeGenerator\Helper\Data;

class BarcodeData extends \Magento\Framework\App\Helper\AbstractHelper
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
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Data $helperData
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->helperData = $helperData;
    }
     /**
     *  Get ProductID $id
     *  return Selected barcode type
     */
    public function barcodeText($id)
    {
        $barcodeType = $this->helperData->barcodeType();

        if ($barcodeType == 'ean8') {
            $barcodeLen='7';
            $ProId=$id;
            $data = $this->helperData->barcodePrefix().$this->generateBarcodeNumber($barcodeLen, $ProId);
            return $data;
        }
        if ($barcodeType == 'ean13') {
            $barcodeLen='12';
            $ProId=$id;
            $data = $this->helperData->barcodePrefix().$this->generateBarcodeNumber($barcodeLen, $ProId);
            return $data;
        }
        if ($barcodeType == 'upca') {
            $barcodeLen='11';
            $ProId=$id;
            $data = $this->helperData->barcodePrefix().$this->generateBarcodeNumber($barcodeLen, $ProId);
            return $data;
        }
        if ($barcodeType == 'upce') {
            $barcodeLen='7';
            $ProId=$id;
            $data = $this->helperData->barcodePrefix().$this->generateBarcodeNumber($barcodeLen, $ProId);
            return $data;
        }
        if ($barcodeType == 'code128') {
            $barcodeLen='8';
            $ProId=$id;
            $data = $this->helperData->barcodePrefix().$this->generateBarcodeNumber($barcodeLen, $ProId);
            return $data;
        }
        if ($barcodeType == 'code39') {
            $barcodeLen='8';
            $ProId=$id;
            $data = $this->helperData->barcodePrefix().$this->generateBarcodeNumber($barcodeLen, $ProId);
            return $data;
        }
        if ($barcodeType == 'code25interleaved') {
            $barcodeLen='8';
            $ProId=$id;
            $data = $this->helperData->barcodePrefix().$this->generateBarcodeNumber($barcodeLen, $ProId);
            return $data;
        }
        if ($barcodeType == 'code25') {
            $barcodeLen='12';
            $ProId=$id;
            $data = $this->helperData->barcodePrefix().$this->generateBarcodeNumber($barcodeLen, $ProId);
            return $data;
        }
        if ($barcodeType == 'royalmail') {
            $barcodeLen='10';
            $ProId=$id;
            $data = $this->helperData->barcodePrefix().$this->generateBarcodeNumber($barcodeLen, $ProId);
            return $data;
        }
        if ($barcodeType == 'identcode') {
            $barcodeLen='11';
            $ProId=$id;
            $data = $this->helperData->barcodePrefix().$this->generateBarcodeNumber($barcodeLen, $ProId);
            return $data;
        }
        if ($barcodeType == 'itf14') {
            $barcodeLen='13';
            $ProId=$id;
            $data = $this->helperData->barcodePrefix().$this->generateBarcodeNumber($barcodeLen, $ProId);
            return $data;
        }
        if ($barcodeType == 'postnet') {
            $barcodeLen='9';
            $ProId=$id;
            $data = $this->helperData->barcodePrefix().$this->generateBarcodeNumber($barcodeLen, $ProId);
            return $data;
        }
        if ($barcodeType == 'planet') {
            $barcodeLen='11';
            $ProId=$id;
            $data = $this->helperData->barcodePrefix().$this->generateBarcodeNumber($barcodeLen, $ProId);
            return $data;
        }
        if ($barcodeType == 'leitcode') {
            $barcodeLen='13';
            $ProId=$id;
            $data = $this->helperData->barcodePrefix().$this->generateBarcodeNumber($barcodeLen, $ProId);
            return $data;
        }
    }

    /**
     *  Get ProductID $id
     *  Get Selected barcode type
     *  return Generated Barcode Number
     */
    public function generateBarcodeNumber($barcodeLen, $ProId)
    {
        $barcodePrefix = strlen($this->helperData->barcodePrefix());

        $barcodeLength = $barcodeLen;
        $barcodeNumber = $ProId;

        $length = strlen((string)$barcodeNumber);
        for ($i = $length; $i<$barcodeLength-$barcodePrefix; $i++) {
            $barcodeNumber = '0'.$barcodeNumber;
        }

        return $barcodeNumber;
    }
}
