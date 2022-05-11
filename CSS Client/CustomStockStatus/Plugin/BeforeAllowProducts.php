<?php
/**
 * @category Mageants CustomStockStatus
 * @package Mageants_CustomStockStatus
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <info@mageants.com>
 */

namespace Mageants\CustomStockStatus\Plugin;

class BeforeAllowProducts
{

    /**
     * getAllowProducts
     *
     * @param \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject
     *
     * @return array
     */
    public function beforeGetAllowProducts($subject)
    {
        if (!$subject->hasData('allow_products')) {
            $products = [];
            $allProducts = $subject->getProduct()->getTypeInstance()->getUsedProducts($subject->getProduct(), null);
            foreach ($allProducts as $product) {
                $products[] = $product;
            }
            $subject->setData('allow_products', $products);
        }
    }
}
