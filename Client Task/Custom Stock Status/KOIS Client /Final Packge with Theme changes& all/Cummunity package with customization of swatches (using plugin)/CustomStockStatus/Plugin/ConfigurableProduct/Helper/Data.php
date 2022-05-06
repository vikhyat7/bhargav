<?php
namespace Mageants\CustomStockStatus\Plugin\ConfigurableProduct\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
* Plugin for making visible swatches that related to  out of stock products
* depend on the setting admin->Stores->configuration->Catalog->Inventory->Stock Options->Out of Stock Products
*/
class Data
{
    const SHOW_OUT_OF_STOCK_CONFIG_PATH = "cataloginventory/options/show_out_of_stock";
    protected ScopeConfigInterface $scopeConfig;

    /** 
    *@param ScopeConfigInterface $scopeConfig
    */    
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param \Magento\ConfigurableProduct\Helper\Data $subject
     * @param \Closure $proceed
     * @param $currentProduct
     * @param $allowedProducts
     * @return array
     */
public function aroundGetOptions(
    \Magento\ConfigurableProduct\Helper\Data $subject,
    \Closure                                 $proceed,
                                             $currentProduct,
                                             $allowedProducts

) {
    $result = $proceed($currentProduct, $allowedProducts);

    $show_out_of_stock = $this->scopeConfig->getValue(self::SHOW_OUT_OF_STOCK_CONFIG_PATH,
        \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    $options = [];
    $allowAttributes = $subject->getAllowAttributes($currentProduct);

    foreach ($allowedProducts as $product) {
        $productId = $product->getId();
        foreach ($allowAttributes as $attribute) {
            $productAttribute = $attribute->getProductAttribute();
            $productAttributeId = $productAttribute->getId();
            $attributeValue = $product->getData($productAttribute->getAttributeCode());
            if ($show_out_of_stock || $product->isSalable()) {
                $options[$productAttributeId][$attributeValue][] = $productId;
            }
            $options['index'][$productId][$productAttributeId] = $attributeValue;
        }
    }

    return $options;
  }
}