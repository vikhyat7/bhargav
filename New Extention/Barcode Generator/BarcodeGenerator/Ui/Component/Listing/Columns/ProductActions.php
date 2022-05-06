<?php
/**
 * @category Mageants BarcodeGenerator
 * @package Mageants_BarcodeGenerator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\BarcodeGenerator\Ui\Component\Listing\Columns;
use \Magento\Framework\View\Element\UiComponent\ContextInterface;
use \Magento\Framework\View\Element\UiComponentFactory;
use \Magento\Ui\Component\Listing\Columns\Column;
use \Magento\Catalog\Api\ProductRepositoryInterface;
use \Magento\Framework\UrlInterface;
use \Mageants\BarcodeGenerator\Helper\Data;

class ProductActions extends Column
{
    /**
     * Url path  to edit
     *
     * @var string
     */
    const URL_PATH_EDIT = 'barcodegenerator/pdf/printpdf';

    protected $ProductRepositoryInterface;
        /**
         * URL builder
         *
         * @var \Magento\Framework\UrlInterface
         */
    protected $_urlBuilder;
    protected $Data;

        /**
         * constructor
         *
         * @param UrlInterface $urlBuilder
         * @param ContextInterface $context
         * @param UiComponentFactory $uiComponentFactory
         * @param array $components
         * @param array $data
         */
    public function __construct(
        ContextInterface $context,
        Data $Data,
        UrlInterface $urlBuilder,
        UiComponentFactory $uiComponentFactory,
        ProductRepositoryInterface $ProductRepositoryInterface,
        array $components = [],
        array $data = []
    ) {

        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->ProductRepositoryInterface = $ProductRepositoryInterface;
        $this->Data = $Data;
        $this->_urlBuilder = $urlBuilder;
    }
    /**
     * Prepare Datasource for "Print Barcode" lable of Product Grid 
     */
    public function prepareDataSource(array $dataSource)
    {
        // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // $logger = $objectManager->create(\Mageants\BarcodeGenerator\Helper\Data::class);
        // $logger->debug('Test Callled Start');

        if (isset($dataSource['data']['items'])) {
            if ($this->Data->isEnable() == 1) {
        
            
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $item['name'];
                if (isset($item['entity_id'])) {
    
                    $url = $this->_urlBuilder->getUrl(
                        static::URL_PATH_EDIT,
                        [
                                    'entity_id' => $item['entity_id']
                                ]
                    );

                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => 'javascript:myFunction('.json_encode($url).');',
                            'label' => __('Print Barcode')
                            
                        ]
                    ];
                }
            }

            }
        }
        
        return $dataSource;
    }
}
