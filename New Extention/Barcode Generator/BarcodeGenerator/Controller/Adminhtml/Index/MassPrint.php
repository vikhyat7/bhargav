<?php

namespace Mageants\BarcodeGenerator\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
// use Magento\Framework\App\Action\Action;
// use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Mageants\BarcodeGenerator\Helper\Data;
use Mageants\BarcodeGenerator\Helper\BarcodeData;
use Mageants\BarcodeGenerator\Helper\Qrcodeprocess;
use Magento\Catalog\Model\ProductRepositoryFactory;

use Magento\Catalog\Model\ResourceModel\ProductFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Laminas\Config\Config;
use Laminas\Barcode\Barcode;

class MassPrint extends Action
{

    /**
     * @var Filter
     */
    protected $filter;
    protected $PrintPdf;

    /**
     * @var CollectionFactory
     */
    protected $prodCollFactory;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;




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
     * @param Context  $context
     * @param Filter   $filter
     * @param CollectionFactory $prodCollFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
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
        Qrcodeprocess $barcodeQrHelper,


        
        Filter $filter,

        
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository)
    {
        $this->filter = $filter;
        // $this->PrintPdf = $PrintPdf;
        
        $this->productRepository = $productRepository;
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
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException | \Exception
     */
    public function execute()
    {
        
      

        $pages = (int)$this->getRequest()->getParam('pages');
        if(!$pages){
            $pages=5;
        }

            $this->getPdf($pages);
    }

    public function getPdf($no)
    {

         $this->filter->applySelectionOnTargetProvider(); // compatibility with Mass Actions on Magento 2.1.0
        
        $collection = $this->filter->getCollection($this->collectionFactory);

        
        
        $ids = $collection->getAllIds();
        
        $pdf = new \Zend_Pdf();
            

            

            // count($ids);exit();
            $i=1;

            $x = 30;
            $this->y = 850 - 100;

          foreach($ids as $id){
            $page = new \Zend_Pdf_Page(\Zend_Pdf_Page::SIZE_A4);
            $font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_TIMES);

            $ProdData = $this->_productloader->create()->load($id);
            $productName = $ProdData;
        
            $barcodeType = $this->helperData->barcodeType();
            $BarAttr =  $this->helperData->barcodeAtrribute();
            $barcodeNumber = $this->barcodeHelper->barcodeText($id);

            for($i=1;$i<=6;$i++){


        

            if ($BarAttr == 'SKU') {
                $page->setFont($font, 14) ->drawText('*'.$productName->getSku().'*', $x +10, $this->y+40, 'UTF-8');
            }
            if ($BarAttr == 'ProductName') {
                $page->setFont($font, 14) ->drawText('*'.$productName->getName().'*', $x + 10, $this->y+40, 'UTF-8');
            }

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

            $page->drawImage($image, $x-10, $this->y-30, $x+160, $this->y+30);
            $page->setFont($font, 14) ->drawText('*'.$productName->getSku().'*', $x + 35, $this->y-45, 'UTF-8');
            $page->setFont($font, 14) ->drawText('*'.$productName->getFinalPrice().'*', $x + 55, $this->y-60, 'UTF-8');



            // $page->setFont($font, 24) ->drawText($id, 72, 720);
            
                $this->y=$this->y-100;
            continue;
            }
            // if($i==2){
                // }
            // $i++;
                $pdf->pages[] = $page;
            }
            
        

                 
        // set Pdf name
        $fileName =" Barcode.pdf";

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
