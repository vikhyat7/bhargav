<?php
/**
 * @category Mageants StoreViewPricing
 * @package Mageants_StoreViewPricing
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\StoreViewPricing\Plugin;

class Price
{
    public function __construct(
        \Magento\Catalog\Model\Product $productCollection,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable,
        \Mageants\StoreViewPricing\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
    ) {
        $this->productCollection = $productCollection;
        $this->configurable = $configurable;
        $this->helper = $helper;
        $this->storeManagerInterface = $storeManagerInterface;
    }

    public function afterGetSpecialPrice(\Magento\Catalog\Model\Product $subject, $result)
    {
        $productCollection= $this->productCollection->getCollection();
        $parentIds = $this->configurable->getParentIdsByChild($subject->getId());
        $parentId = array_shift($parentIds);

        $helper= $this->helper;
        if ((int)$helper->priceScope()==2) {
            $storeManager = $this->storeManagerInterface;
            $storeId = $storeManager->getStore()->getId();
            $joinConditions = 'u.entity_id = e.entity_id';
            // @codingStandardsIgnoreLine
            $productCollection->getSelect()->join(
                ['u' => $productCollection->getTable('store_view_pricing')],
                $joinConditions,
                ['*']
            );
            if ($parentId) {
                $productCollection->addFieldToFilter('entity_id', [ // conditions
                [ // conditions for field_1
                    ['in' => [$subject->getId(), $parentId]],
                ]]);
            } else {
                $productCollection->addFieldToFilter('entity_id', $subject->getId());
            }
            if ($productCollection) {
                foreach ($productCollection->getData() as $customProducts) {
                    if ($customProducts['store_id'] == $storeId) {
                        if ($customProducts['special_price']) {
                            $result = $customProducts['special_price'];
                        }
                    }
                }
            }
        }
        return $result;
    }

    public function afterGetPrice(\Magento\Catalog\Model\Product $subject, $result)
    {
        $productCollection= $this->productCollection->getCollection();
        $parentIds = $this->configurable->getParentIdsByChild($subject->getId());
        $parentId = array_shift($parentIds);
         
        $helper= $this->helper;
        if ((int)$helper->priceScope()==2) {
            $storeManager = $this->storeManagerInterface;
            $storeId = $storeManager->getStore()->getId();
            $joinConditions = 'u.entity_id = e.entity_id';
            // @codingStandardsIgnoreLine
            $productCollection->getSelect()->join(
                ['u' => $productCollection->getTable('store_view_pricing')],
                $joinConditions,
                ['*']
            );
            if ($parentId) {
                $productCollection->addFieldToFilter('entity_id', [ // conditions
                [ // conditions for field_1
                    ['in' => [$subject->getId(), $parentId]],
                ]]);
            } else {
                $productCollection->addFieldToFilter('entity_id', $subject->getId());
            }
            if ($productCollection) {
                foreach ($productCollection->getData() as $customProducts) {
                    if($customProducts['price'] != 0){
                        if ((int)$customProducts['store_id'] === (int)$storeId) {
                            $result = $customProducts['price'];
                            break;
                        }
                        else if ((int)$customProducts['store_id'] !== (int)$storeId) {
                            $result = $customProducts['old_price'];
                        } 
                        elseif ((int)$customProducts['store_id'] === 0) {
                            $result = $customProducts['price'];
                        }
                    }
                }
            }
        }
        return $result;
    }
}
