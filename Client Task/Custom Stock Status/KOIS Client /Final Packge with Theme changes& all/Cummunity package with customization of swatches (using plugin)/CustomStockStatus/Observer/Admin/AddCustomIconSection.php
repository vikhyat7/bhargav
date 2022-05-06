<?php
/**
 * @category Mageants CustomStockStatus
 * @package Mageants_CustomStockStatus
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <info@mageants.com>
 */

namespace Mageants\CustomStockStatus\Observer\Admin;

use Magento\Catalog\Block\Adminhtml\Product\Attribute\Edit\Tabs;
use Magento\Framework\Event\ObserverInterface;

class AddCustomIconSection implements ObserverInterface
{
    private $request;

    private $eavAttribute;

    const CUSTOM_STOCK_STATUS_ATTRIBUTE = 'mageants_custom_stock_status';

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $eavAttribute
    ) {
        $this->request = $request;
        $this->eavAttribute = $eavAttribute;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $block = $observer->getBlock();
        $attributeId = $this->request->getParam('attribute_id');
        $attr = $this->eavAttribute->load($attributeId);
        $attributeCode=$this->eavAttribute->getAttributeCode();

        if ($attributeCode == self::CUSTOM_STOCK_STATUS_ATTRIBUTE) {
            if ($block instanceof Tabs) {
                /** @var Tabs $block */
                $block->addTabAfter(
                    'mageants_customstockstock',
                    [
                    'label' => __('Manage Option Icon'),
                    'title' => __('Manage Option Icon'),
                    'content' => $block->getChildHtml('customoptionicon'),

                    ],
                    'front'
                );

                $block->addTabAfter(
                    'mageants_customstockstock_quantityrange',
                    [
                    'label' => __('Manage Quantity Range'),
                    'title' => __('Manage Quantity Range'),
                    'content' => $block->getChildHtml('quantityrange'),

                    ],
                    'front'
                );
            }
        }
    }
}
