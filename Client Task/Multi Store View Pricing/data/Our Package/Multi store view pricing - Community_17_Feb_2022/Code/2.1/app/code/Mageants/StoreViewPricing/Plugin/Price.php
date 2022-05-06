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
    public function afterGetSpecialPrice(\Magento\Catalog\Model\Product $subject, $result)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productCollection= $objectManager->create(\Magento\Catalog\Model\Product::class)->getCollection();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $parentIds = $objectManager->get(\Magento\ConfigurableProduct\Model\Product\Type\Configurable::class)
            ->getParentIdsByChild($subject->getId());
        $parentId = array_shift($parentIds);
        
        $helper= $objectManager->get(\Mageants\StoreViewPricing\Helper\Data::class);
        if ((int)$helper->priceScope()==2) {
            $storeManager = $objectManager->get(\Magento\Store\Model\StoreManagerInterface::class);
            $storeId = $storeManager->getStore()->getId();
            $joinConditions = 'u.entity_id = e.entity_id';
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
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productCollection= $objectManager->create(\Magento\Catalog\Model\Product::class)->getCollection();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $parentIds = $objectManager->get(\Magento\ConfigurableProduct\Model\Product\Type\Configurable::class)
            ->getParentIdsByChild($subject->getId());
        $parentId = array_shift($parentIds);
        
        $helper= $objectManager->get(\Mageants\StoreViewPricing\Helper\Data::class);
        if ((int)$helper->priceScope()==2) {
            $storeManager = $objectManager->get(\Magento\Store\Model\StoreManagerInterface::class);
            $storeId = $storeManager->getStore()->getId();
            $joinConditions = 'u.entity_id = e.entity_id';
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
                    if ((int)$customProducts['store_id'] === (int)$storeId) {
                        $result = $customProducts['price'];
                        break;
                    } elseif ((int)$customProducts['store_id'] === 0) {
                        $result = $customProducts['price'];
                    }
                }
            }
        }
        return $result;
    }
}
