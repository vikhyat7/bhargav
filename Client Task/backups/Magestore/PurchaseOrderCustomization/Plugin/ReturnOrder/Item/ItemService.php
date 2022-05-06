<?php

namespace Magestore\PurchaseOrderCustomization\Plugin\ReturnOrder\Item;

use Magestore\SupplierSuccess\Api\Data\SupplierProductInterface;

/**
 * Class ItemService
 *
 * @package Magestore\PurchaseOrderCustomization\Plugin\ReturnOrder\Item
 */
class ItemService
{
    /**
     * After process product data
     *
     * @param \Magestore\PurchaseOrderSuccess\Service\ReturnOrder\Item\ItemService $subject
     * @param mixed $resultPlugin
     * @param mixed $result
     * @param mixed $productId
     * @param mixed $productData
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterProcessProductData(
        \Magestore\PurchaseOrderSuccess\Service\ReturnOrder\Item\ItemService $subject,
        $resultPlugin,
        $result,
        $productId,
        $productData
    ) {
        if (is_string($productData)) {
            $productData = json_decode($productData, true);
        }
        if (isset($productData['cost'])) {
            if ($productData['cost'] != $productData['cost_old']) {
                $resultPlugin[$productId] = $productData;
            }
        }
        return $resultPlugin;
    }

    /**
     * After prepare product data to return order
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param \Magestore\PurchaseOrderSuccess\Service\ReturnOrder\Item\ItemService $subject
     * @param mixed $result
     * @param mixed $returnId
     * @param array $productsData
     * @param array $returnProductIds
     * @param array $updateData
     * @return mixed
     */
    public function afterPrepareProductDataToReturnOrder(
        \Magestore\PurchaseOrderSuccess\Service\ReturnOrder\Item\ItemService $subject,
        $result,
        $returnId,
        $productsData = [],
        $returnProductIds = [],
        $updateData = []
    ) {
        foreach ($productsData as $productData) {
            $productId = $productData[SupplierProductInterface::PRODUCT_ID];
            if (isset($result[$productId])) {
                $result[$productId]['cost'] = $productData['cost'];
                if (isset($updateData[$productId]) && isset($updateData[$productId]['cost'])) {
                    $result[$productId]['cost'] = $updateData[$productId]['cost'];
                }
            }
        }
        return $result;
    }
}
