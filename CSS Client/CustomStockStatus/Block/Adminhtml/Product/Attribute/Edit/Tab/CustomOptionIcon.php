<?php
/**
 * @category Mageants CustomStockStatus
 * @package Mageants_CustomStockStatus
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <info@mageants.com>
 */

namespace Mageants\CustomStockStatus\Block\Adminhtml\Product\Attribute\Edit\Tab;

use Magento\Framework\App\Filesystem\DirectoryList;

class CustomOptionIcon extends \Magento\Framework\View\Element\Template
{
    /**
     * Path to template file.
     */
    public $_template = 'CustomOptionIcon.phtml';

    public $eavAttribute;

    public $request;

    public $eavConfig;

    public $customIconManage;

    public $objectManager;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $eavAttribute,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Mageants\CustomStockStatus\Model\CustomStockStFactory $customIconManage,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->request = $request;
        $this->eavAttribute = $eavAttribute;
        $this->eavConfig = $eavConfig;
        $this->objectManager = $objectManager;
        $this->customIconManage = $customIconManage;
    }

    public function getAttributeOptionCollection()
    {
        $attributeId = $this->request->getParam('attribute_id');
        $attr = $this->eavAttribute->load($attributeId);
        $attributeCode= $this->eavAttribute->getAttributeCode();
        $attributeDetails = $this->eavConfig->getAttribute("catalog_product", $attributeCode);
        $alloptions = $attributeDetails->getSource()->getAllOptions();
        return $alloptions;
    }

    public function getMediaImagePath()
    {
        $mediapath = $this->objectManager->get('Magento\Store\Model\StoreManagerInterface')
                ->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        return $mediapath;
    }

    public function getIconImage($optionId)
    {
        $customIcon = $this->customIconManage->create();
        $optionIconCollection = $customIcon->getCollection()->addFieldToFilter('option_id', $optionId)->getLastItem();

        return $optionIconCollection->getData('icon');
    }
}
