<?php
/**
 * @category Mageants StoreLocator
 * @package Mageants_StoreLocator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@Mageants.com>
 */
namespace Mageants\StoreLocator\Ui\Component\Listing\Column;

use Magento\Catalog\Helper\Image;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\Filesystem;

/**
 * Locator store thumbnail Image
 */
class Thumbnail extends \Magento\Ui\Component\Listing\Columns\Column
{
    const IMAGE_WIDTH = '70%'; // Thumbnail Image Width
    const IMAGE_HEIGHT = '60'; // Thumbnail Image Height
    const IMAGE_STYLE = 'display: block;margin: auto;'; // Thumbnail Image Style
    
    /**
     * Current Store Manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;
    
    /**
     * @param \Magento\Backend\Block\Template\Context
     * @param \Magento\Framework\View\Element\UiComponentFactory
     * @param \Magento\Framework\Filesystem
     * @param \Magento\Store\Model\StoreManagerInterface
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder,
        Image $imageHelper,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->_storeManager = $storeManager;
        $this->imageHelper = $imageHelper;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * prepare Data source
     *
     * @param array $dataSource
     * @return $dataSource
     */
    public function prepareDataSource(array $dataSource)
    {
        $width = $this->hasData('width') ? $this->getWidth() : self::IMAGE_WIDTH;
        $height = $this->hasData('height') ? $this->getHeight() : self::IMAGE_HEIGHT;
        $style = $this->hasData('style') ? $this->getStyle() : self::IMAGE_STYLE;
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $product = new \Magento\Framework\DataObject($item);
                $imageHelper = $this->imageHelper->init($product, 'storelocator_storelocator_listing_image');
                $item[$fieldName . '_src'] = $this->_storeManager->getStore()
                        ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $item[$this->getData('name')];
                $item[$fieldName . '_alt'] = $product['sname'];
                $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                    'storelocator/storelocator/edit',
                    ['store_id' => $product->getData('store_id'), 'store' => $this->context->getRequestParam('store')]
                );
                $item[$this->getData('name')] = sprintf(
                    '<img src='.$item[$fieldName . '_src'].'  width="%s" height="%s" style="%s"/>',
                    $width,
                    $height,
                    $style
                );
                $origImageHelper = $this->imageHelper->init(
                    $product,
                    'storelocator_storelocator_listing_image_preview'
                );
                $item[$fieldName . '_orig_src'] = $item[$fieldName . '_src'];
            }
        }

        return $dataSource;
    }
    
    /**
     * prepare Alt
     *
     * @param $row
     * @return null|string
     */
    public function getAlt($row)
    {
        $altField = $this->getData('config/altField') ?: self::ALT_FIELD;
        return isset($row[$altField]) ? $row[$altField] : null;
    }
}
