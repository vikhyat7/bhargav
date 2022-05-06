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
use Laminas\Config\Config;
use Laminas\Barcode\Barcode;

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
        
// PDF Pages configurations
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

// Print Brand logo in PDF

            $LogoUrl= 'mageants/barcodegenerator/logo/' . $this->helperData->logoImage();
            $pdfImage = \Zend_Pdf_Image::imageWithPath($this->_filesystem->getDirectoryRead(
                DirectoryList::MEDIA
            )->getAbsolutePath($LogoUrl));
        
//Logo Enable/Disable

            if ($this->helperData->isLogoEnble()==1) {
                $page->drawImage($pdfImage, $x+10, $this->y, $x+120, $this->y+55);
            }
            
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
            
                if ($value == 'URL') {
                    $page->drawText('URL : '.$productName->getProductUrl(), $x+50, $this->y-250, 'UTF-8');
                }
            }

            //Draw image to PDF

            $product = $this->ProductRepositoryFactory->create()->getById($productName->getId());
            $productImage = '/catalog/product' . $product->getData('thumbnail');
            
            $pdfImage = \Zend_Pdf_Image::imageWithPath($this->_filesystem->getDirectoryRead(
                DirectoryList::MEDIA
            )->getAbsolutePath($productImage));

            $page->drawImage($pdfImage, $x+300, $this->y-140, $x+520, $this->y+40);

//=========================Draw Barcode To PDF============================

            $barcodeType = $this->helperData->barcodeType();
            $BarAttr =  $this->helperData->barcodeAtrribute();
            
            $id = $productName->getId();
            $barcodeNumber = $this->barcodeHelper->barcodeText($id);
                       
            if ($BarAttr == 'SKU') {
                $page->drawText('*'.$productName->getSku().'*', $x + 143, $this->y-130, 'UTF-8');
            }
            if ($BarAttr == 'ProductName') {
                $page->drawText('*'.$productName->getName().'*', $x + 143, $this->y-130, 'UTF-8');
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

            $page->drawImage($image, $x+130, $this->y-200, $x+250, $this->y-140);
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
