<?php
/**
 * @category Mageants BarcodeGenerator
 * @package Mageants_BarcodeGenerator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\BarcodeGenerator\Controller\Adminhtml\Pdf;
 
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Mageants\BarcodeGenerator\Helper\Data;
use Mageants\BarcodeGenerator\Helper\BarcodeData;
use Mageants\BarcodeGenerator\Helper\Qrcodeprocess;
use Magento\Catalog\Model\ProductRepositoryFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\ProductFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Laminas\Config\Config;
use Laminas\Barcode\Barcode;

class PrintPdf extends Action
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     * @var ProductFactory
     */
    protected $productFactory;
    protected $resourceModel;
    protected $_storeManager;
    protected $_filesystem;
    protected $_currency;
    protected $fileFactory;
    protected $ProductRepositoryFactory;
    protected $collectionFactory;
    protected $_productloader;
    protected $_messageManager;
 /**
  * @param ProductRepositoryFactory $ProductRepositoryFactory;
  * @param ProductFactory $productFactory;
  * @param ResourceModel\Product $resourceModel;
  * @param collectionFactory $collectionFactory;
  */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product $resourceModel,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        Context $context,
        ProductFactory $productFactory,
        ProductRepositoryFactory $ProductRepositoryFactory,
        Collection $collectionFactory,
        FileFactory $fileFactory,
        Data $helperData,
        BarcodeData $barcodeHelper,
        Qrcodeprocess $barcodeQrHelper
    ) {
        $this->_filesystem = $filesystem;
        $this->_storeManager = $storeManager;
        $this->_currency = $currency;
        $this->fileFactory = $fileFactory;
        $this->ProductRepositoryFactory = $ProductRepositoryFactory;
        $this->productFactory = $productFactory;
        $this->_productloader = $_productloader;
        $this->datetime = $datetime;
        $this->_stockItemRepository = $stockItemRepository;
        $this->resourceModel = $resourceModel;
         $this->_messageManager = $messageManager;
        $this->collectionFactory = $collectionFactory;
        $this->helperData = $helperData;
        $this->barcodeHelper = $barcodeHelper;
        $this->barcodeQrHelper = $barcodeQrHelper;
        parent::__construct($context);
    }

    /**
     *  Get Page Count and return PDF with Data.
     */
    public function execute()
    {
        $pages = (int)$this->getRequest()->getParam('pages');
        $this->getPdf($pages);
    }

    /**
     *  Get Product Data from ID
     */
    public function productData()
    {
        $entityId = (int)$this->getRequest()->getParam('entity_id');
        if (!$entityId) {
            $entityId=1;
        }
        $ProdData = $this->_productloader->create()->load($entityId);

        return $ProdData;
    }


    /**
     *  Make Pdf configuration and generate pdf with data.
     */
    public function getPdf($no)
    {
        $num = $no;
        $productName = $this->productData();
        $qty = $this->_stockItemRepository->get($productName->getId())->getQty();
        $price = $this->_currency->getCurrencySymbol().$productName->getFinalPrice();
        $ProductStatus = $productName->isSaleable();
        if ($ProductStatus==1) {
            $ProductStatus = "In Stock";
        } else {
            $ProductStatus = "Out Of Stock";
        }
        
// PDF Pages configurations
        $i;
        $pdf = new \Zend_Pdf();
        $barcodeNumber = $this->barcodeHelper->barcodeText($productName->getId());
        for ($i=1; $i<=$num; $i++) {
            $pdf->pages[$i] = $pdf->newPage(\Zend_Pdf_Page::SIZE_A4);
            $page = $pdf->pages[$i]; // this will get reference to the first page.
            $style = new \Zend_Pdf_Style();
            $page->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));
            $font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_TIMES);
            $style->setFont($font, 13);
            $page->setStyle($style);
            $width = $this->helperData->pdfPageWidth();
            $hight = $this->helperData->pdfPageHeight();
            $x = 30;
            $pageTopalign = 1850;
            $this->y = 850 - 100;
        
            $style->setFont($font, 13);
            $page->setStyle($style);

// Print Brand logo in PDF
            
            $LogoConfig   = $this->helperData->isLogoEnble();
            $logoImageConfig = $this->helperData->logoImage();
            // $message = 'Choose logo image';

            if ($LogoConfig == 1) {
               if (!empty($logoImageConfig)) {
              
                    $LogoUrl= 'mageants/barcodegenerator/logo/' . $this->helperData->logoImage();
                    $pdfImage = \Zend_Pdf_Image::imageWithPath($this->_filesystem->getDirectoryRead(
                        DirectoryList::MEDIA
                    )->getAbsolutePath($LogoUrl));                
                   $page->drawImage($pdfImage, $x, $this->y+10, $x+120, $this->y+78);    
                }
            }

         
            $list    = $this->helperData->descriptionAtrr();
            foreach ($list as $value) {
                if ($value == 'ProductName') {
                    $page->drawText('Name : '.$productName->getName(), $x + 10, $this->y-40, 'UTF-8');
                }
                if ($value == 'Sku') {
                    $page->drawText('SKU : '.$productName->getSku(), $x + 10, $this->y-60, 'UTF-8');
                }
                if ($value == 'Price') {
                    $page->drawText('Price: '.$price, $x + 10, $this->y-80, 'UTF-8');
                }
                if ($value == 'Qty') {
                    $page->drawText('Quantity : '.$qty, $x + 10, $this->y-100, 'UTF-8');
                }
                if ($value == 'Status') {
                    $page->drawText('Status : '.$ProductStatus, $x + 10, $this->y-120, 'UTF-8');
                }
            
                // if ($value == 'URL') {
                //     $page->drawText($productName->getProductUrl(), $x+10, $this->y-250, 'UTF-8');
                // }
            }
            
            //Draw image to PDF

            $product = $this->ProductRepositoryFactory->create()->getById($productName->getId());
            $productImage = '/catalog/product' . $product->getData('thumbnail');
            
            $pdfImage = \Zend_Pdf_Image::imageWithPath($this->_filesystem->getDirectoryRead(
                DirectoryList::MEDIA
            )->getAbsolutePath($productImage));

            $page->drawImage($pdfImage, $x+300, $this->y-140, $x+520, $this->y+40);
            
    // =========== QR Code Start ================

            $id = $productName->getId();
            $store = $this->_storeManager->getStore()->getId();
            // $path = '/qr/img/'.$store.$id.'.png';
            $path = '/mageants/barcodegenerator/qr/img/'.$store.'/'.$id.'.png';

            $ProSku = $productName->getSku();
            $url = $productName->getProductUrl();

            $QrConfig   = $this->helperData->isEnableQr();
            
            if ($QrConfig == 1) {
                
                $barcodeQr = $this->barcodeQrHelper->generateQrCode($id);
                
                $QR_DIR = \Zend_Pdf_Image::imageWithPath($this->_filesystem->getDirectoryRead(
                    DirectoryList::MEDIA
                    )->getAbsolutePath($path));

                $page->drawText(' Scan To Open => ', $x+250, $this->y-235, 'UTF-8');
                 // $page->drawImage($QR_DIR, $x+350, $this->y-355, $x+450, $this->y-255);
                 $page->drawImage($QR_DIR, $x+350, $this->y-285, $x+450, $this->y-185);
            }
            
    // =========== QR Code End ================

//=========================Draw Barcode To PDF============================

            $barcodeType = $this->helperData->barcodeType();
            $BarAttr =  $this->helperData->barcodeAtrribute();
            
            // $id = $productName->getId();
            // $barcodeNumber = $this->barcodeHelper->barcodeText($id);
                       
            if ($BarAttr == 'SKU') {
                $page->drawText('*'.$productName->getSku().'*', $x + 30, $this->y-200, 'UTF-8');
            }
            if ($BarAttr == 'ProductName') {
                $page->drawText('*'.$productName->getName().'*', $x + 30, $this->y-200, 'UTF-8');
            }
          
//=======================Laminas Barcode image to PDF==========================
            
            $config = new Config([
                    'barcode'        =>  $barcodeType,
                    'barcodeParams' => [
                         'text' => $barcodeNumber,
                         'drawText' => true
                         ],
                    'renderer'       => 'image',
                    'rendererParams' => ['imageType' => 'png'],
                ]);

            $renderer = Barcode::factory($config);
            $imageResource = $renderer->draw();
            ob_start();// @codingStandardsIgnoreLine
            imagepng($imageResource);// @codingStandardsIgnoreLine
            $barcodeImage = ob_get_clean();
            $image = new \Zend_Pdf_Resource_Image_Png('data:image/png;base64,'.base64_encode($barcodeImage));

            $page->drawImage($image, $x-10, $this->y-273, $x+160, $this->y-213);
        }
        
        // set Pdf name
        $fileName = $productName->getName() ." Barcode ". $this->datetime->date('Y-m-d_H-i-s').".pdf";

        // Generate(render) Pdf
        $this->fileFactory->create(
            $fileName,
            $pdf->render(),
            \Magento\Framework\App\Filesystem\DirectoryList::MEDIA,
            // this pdf will be saved in var directory with the name (product_name)(date)(time).pdf
            'application/pdf'
        );
    }
}
