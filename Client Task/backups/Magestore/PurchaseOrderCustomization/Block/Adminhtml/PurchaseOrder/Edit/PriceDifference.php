<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderCustomization\Block\Adminhtml\PurchaseOrder\Edit;

/**
 * Class PriceDifference
 *
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD)
 * @package Magestore\PurchaseOrderCustomization\Block\Adminhtml\PurchaseOrder\Edit
 */
class PriceDifference extends \Magento\Backend\Block\Template
{
    protected $_template = 'Magestore_PurchaseOrderCustomization::purchaseorder/edit/difference_price.phtml';

    /**
     * Check Difference Price
     * @return bool
     */
    public function checkDifferencePrice()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $purchaseOrderId = $this->getRequest()->getParam('id');
        if ($purchaseOrderId) {
            $itemCollection = $objectManager->create(
                \Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item\Collection::class
            );
            $itemCollection->setPurchaseOrderToFilter($purchaseOrderId);
            $itemCollection->addFieldToFilter('price_difference', ['neq' => 0]);
            if (count($itemCollection) > 0) {
                return true;
            }
        }
        return false;
    }
}
