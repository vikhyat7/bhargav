<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\GiftCertificate\Block\Adminhtml\Template\Helper\Form;

use Magento\Framework\Data\Form\Element\Image as ImageField;
use Magento\Framework\Data\Form\Element\Factory as ElementFactory;
use Magento\Framework\Data\Form\Element\CollectionFactory as ElementCollectionFactory;
use Magento\Framework\Escaper;
use Mageants\GiftCertificate\Model\Source\Image as Image;
use Magento\Framework\UrlInterface;

/**
 * Gallary class for Template Gallery
 */ 
class Gallery extends ImageField  
{    
    /**
     * @var Magento\Framework\Data\Form\Element\Image 
     */
    protected $imageModel;
    
    /**
     * @param Magento\Framework\Data\Form\Element\Factory
     * @param Magento\Framework\Data\Form\Element\CollectionFactory
     * @param Magento\Framework\Escaper;
     * @param use Mageants\GiftCertificate\Model\Source\Image
     * @param use Magento\Framework\UrlInterface;
     * @param array $data
     */
    public function __construct(
        Image $imageModel,
        ElementFactory $factoryElement,
        ElementCollectionFactory $factoryCollection,
        Escaper $escaper,
        UrlInterface $urlBuilder,
        $data = []
    )
    {
        $this->imageModel = $imageModel;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $urlBuilder, $data);
    }

    /**
     * 
     * @return string
     */  
    protected function _getUrl()
    {
        $url = false;
        if ($this->getValue()) {
            $url = $this->imageModel->getBaseUrl().$this->getValue();
        }
        return $url;
    }
}
