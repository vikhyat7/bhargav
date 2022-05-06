<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageants\BarcodeGenerator\Helper;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Directory\WriteFactory;
use Magento\Framework\Filesystem\Io\File;

/**
 * Class Qrcodeprocess
 */
class Qrcodeprocess  extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * LoggerInterface
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Order Model
     *
     * @var \Magento\Sales\Model\Order $order
     */
    protected $order;

     /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $currencyFactory;
    protected $_productloader;


    /**
     * @param CustomerAddress $customerAddressHelper
     */
    public function __construct(
        LoggerInterface $logger,
        \Magento\Sales\Model\Order $order,
        DirectoryList $directoryList, 
        WriteFactory $WriteFactory, 
        File $io, 
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory
    )
    {
        $this->logger = $logger;
        $this->order = $order;
        $this->_directoryList = $directoryList;
        $this->_fileWriteFactory = $WriteFactory;
        $this->_io = $io;
        $this->_curl = $curl;
        $this->storeManager = $storeManager;
        $this->_productloader = $_productloader;
        $this->currencyFactory = $currencyFactory;
    }


    public function generateQrCode($productId) {

        try{
            $sku = "";
            
             $ProdData = $this->_productloader->create()->load($productId);
            
            $sku = $ProdData->getSku();
            $proId = $ProdData->getId();

            
            $url = $ProdData->getProductUrl();
            // $url = 'https://terradev.doubleip.com/au-kiri.html#';
            $store = $this->storeManager->getStore()->getId();
            $QR_DIR = $this->_directoryList->getPath('media').'/mageants/barcodegenerator/qr/img/'.$store."/";
    
            /* Check and create directory */
            $this->_io->checkAndCreateFolder($QR_DIR);
    
            $size = '250x250';
            //$content = $url;
            $content = urlencode($url);
            $correction = 'L';
            $encoding = 'UTF-8';
            $filename = $proId.'.png';
    
            //Generate QR Code Using Google Api
            $rootUrl = "http://chart.googleapis.com/chart?cht=qr&chs=$size&chl=$content&choe=$encoding&chld=$correction";
    
            //Function to write Image files in Specified Directory
            if (function_exists("curl_init")) {
                $this->_curl->setOptions(array(CURLOPT_CONNECTTIMEOUT => 30, CURLOPT_SSL_VERIFYPEER => 0, CURLOPT_RETURNTRANSFER => 1));
                $this->_curl->get($rootUrl);
                $get_image = $this->_curl->getBody();
                $image_to_fetch = $get_image;
                $image_path_qr = $QR_DIR . $filename;
    
                $local_image_file = fopen($image_path_qr, 'w');
                $fp = fwrite($local_image_file, $image_to_fetch);
                fclose($local_image_file);            
            }
            return $filename;
        }catch(\Exception $e){
            $this->logger->info($e->getMessage());
        }
    }
}