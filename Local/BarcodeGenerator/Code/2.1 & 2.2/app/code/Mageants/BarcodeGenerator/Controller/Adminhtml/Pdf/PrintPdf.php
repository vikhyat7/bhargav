<?php
namespace Mageants\BarcodeGenerator\Controller\Adminhtml\Pdf;
 
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Mageants\BarcodeGenerator\Helper\Data;
use Mageants\BarcodeGenerator\Helper\BarcodeData;
use Magento\Catalog\Model\ProductRepositoryFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\ProductFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Zend\Config\Config;
use Zend\Barcode\Barcode;

class PrintPdf extends Action
{

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    /**
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
        Context $context,
        ProductFactory $productFactory,
        ProductRepositoryFactory $ProductRepositoryFactory,
        Collection $collectionFactory,
        FileFactory $fileFactory,
        Data $helperData,
        BarcodeData $barcodeHelper
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
        $this->collectionFactory = $collectionFactory;
        $this->helperData = $helperData;
        $this->barcodeHelper = $barcodeHelper;
        parent::__construct($context);
    }
    public function execute()
    {
        $pages = (int)$this->getRequest()->getParam('pages');
        $this->getPdf($pages);
    }
    public function productData()
    {
        $entityId = (int)$this->getRequest()->getParam('entity_id');
        if (!$entityId) {
            $entityId=1;
        }
        $ProdData = $this->_productloader->create()->load($entityId);
        // echo "<pre>";
        // print_r(get_class_methods($ProdData));
        // exit;
        return $ProdData;
    }
   
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
        
        // PDF configurations
        $i;
        $pdf = new \Zend_Pdf();
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
            $pdfImage = \Zend_Pdf_Image::imageWithPath("/var/www/html/logo.png");
        
        //Logo Enable/Disable

            if ($this->helperData->isLogoEnble()==1) {
                $page->drawImage($pdfImage, $x+10, $this->y, $x+120, $this->y+55);
            }
            // $page->drawText('Name : '.$productName->getName(), $x + 10, $this->y-20, 'UTF-8');
            // $page->drawText('SKU : '.$productName->getSku(), $x + 10, $this->y-40, 'UTF-8');
            // $page->drawText('Price: '.$price, $x + 10, $this->y-60, 'UTF-8');
            // $page->drawText('Quantity : '.$qty, $x + 10, $this->y-80, 'UTF-8');
            // $page->drawText('Status : '.$ProductStatus, $x + 10, $this->y-100, 'UTF-8');
            // $page->drawText('URL : '.$productName->getProductUrl(), $x + 10, $this->y-120, 'UTF-8');
        
            $list    = $this->helperData->descriptionAtrr();
            foreach ($list as $value) {
                if ($value == 'ProductName') {
                    $page->drawText('Name : '.$productName->getName(), $x + 10, $this->y-20, 'UTF-8');
                }
                if ($value == 'Sku') {
                    $page->drawText('SKU : '.$productName->getSku(), $x + 10, $this->y-40, 'UTF-8');
                }
                if ($value == 'Price') {
                    $page->drawText('Price: '.$price, $x + 10, $this->y-60, 'UTF-8');
                }
                if ($value == 'Qty') {
                    $page->drawText('Quantity : '.$qty, $x + 10, $this->y-80, 'UTF-8');
                }
                if ($value == 'Status') {
                    $page->drawText('Status : '.$ProductStatus, $x + 10, $this->y-100, 'UTF-8');
                }
                // if($value == 'Barcode'){
                //     $page->drawText('Status : '.$ProductStatus, $x + 10, $this->y-100, 'UTF-8');
                // }
                // if($value == 'ProductImage'){
                //     $page->drawText('Status : '.$ProductStatus, $x + 10, $this->y-100, 'UTF-8');
                // }
                if ($value == 'URL') {
                    $page->drawText('URL : '.$productName->getProductUrl(), $x + 100, $this->y-250, 'UTF-8');
                }
            }

            $product = $this->ProductRepositoryFactory->create()->getById($productName->getId());
            $productImage = '/catalog/product' . $product->getData('thumbnail');
            // $productImage = '/catalog/product' . $product->getData('small');
            $pdfImage = \Zend_Pdf_Image::imageWithPath($this->_filesystem->getDirectoryRead(
                DirectoryList::MEDIA
            )->getAbsolutePath($productImage));

            //Draw image to PDF

            $page->drawImage($pdfImage, $x+300, $this->y-140, $x+520, $this->y+40);

            $barcodeType = $this->helperData->barcodeType();
            $BarAttr =  $this->helperData->barcodeAtrribute();

            $id = $productName->getId();
            $barcodeNumber = $this->barcodeHelper->barcodeText($id);

            // echo $barcodeNumber;
            // exit();
                              
            if ($BarAttr == 'SKU') {
                $page->drawText('*'.$productName->getSku().'*', $x + 143, $this->y-130, 'UTF-8');
            }
            if ($BarAttr == 'ProductName') {
                $page->drawText('*'.$productName->getName().'*', $x + 143, $this->y-130, 'UTF-8');
            }
            
            $config = new \Zend_Config([
             'barcode' => $barcodeType,
             'barcodeParams' => [
             'text' => $barcodeNumber,
             'drawText' => true
             ],
             'renderer' => 'image',
             'rendererParams' => ['imageType' => 'png']
             ]);
         
             $barcodeResource = \Zend_Barcode::factory($config)->draw();
         
             ob_start();// @codingStandardsIgnoreLine
             imagepng($barcodeResource);// @codingStandardsIgnoreLine
             $barcodeImage = ob_get_clean();
 
            $image = new \Zend_Pdf_Resource_Image_Png('data:image/png;base64,'.base64_encode($barcodeImage));
 
            $page->drawImage($image, $x+130, $this->y-200, $x+250, $this->y-140);

        }
        
        $fileName = $productName->getName() ." Barcode ". $this->datetime->date('Y-m-d_H-i-s').".pdf";

        $this->fileFactory->create(
            $fileName,
            $pdf->render(),
            \Magento\Framework\App\Filesystem\DirectoryList::MEDIA,
            // this pdf will be saved in var directory with the name Barcodes.pdf
            'application/pdf'
        );
    }
}
