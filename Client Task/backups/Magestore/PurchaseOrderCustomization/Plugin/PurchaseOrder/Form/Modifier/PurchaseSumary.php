<?php

namespace Magestore\PurchaseOrderCustomization\Plugin\PurchaseOrder\Form\Modifier;

/**
 * Class PurchaseSumary
 */
class PurchaseSumary
{
    /**
     * After Get Product Sumary Button
     *
     * @param mixed $sub
     * @param mixed $productSumaryButton
     * @return mixed
     * @SuppressWarnings(PHPMD)
     */
    public function afterGetProductSumaryButton(
        $sub,
        $productSumaryButton
    ) {
        if (is_array($productSumaryButton['children'])
            && isset($productSumaryButton['children']['back_order_product_button'])) {
            unset($productSumaryButton['children']['back_order_product_button']);
        }
        return $productSumaryButton;
    }
}
