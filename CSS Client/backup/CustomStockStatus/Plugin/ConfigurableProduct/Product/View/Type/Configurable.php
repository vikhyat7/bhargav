<?php

namespace Mageants\CustomStockStatus\Plugin\ConfigurableProduct\Product\View\Type;

use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Json\DecoderInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;

class Configurable
{
    protected $jsonEncoder;
    protected $jsonDecoder;
    protected $stockRegistry;

    public function __construct(
        EncoderInterface $jsonEncoder,
        DecoderInterface $jsonDecoder,
        StockRegistryInterface $stockRegistry,
        \Mageants\CustomStockStatus\Helper\Data $helper
    ) {
        $this->jsonDecoder = $jsonDecoder;
        $this->jsonEncoder = $jsonEncoder;
        $this->stockRegistry = $stockRegistry;
        $this->helper = $helper;
    }

    public function aroundGetJsonConfig(
        \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject,
        \Closure $proceed
    ) {
        $quantities = [];
        $config = $proceed();
        $config = $this->jsonDecoder->decode($config);
        foreach ($subject->getAllowProducts() as $product) {
            if ($product->getCustomAttribute('mageants_custom_stock_status') != null) {
                $productOptionId = $product->getCustomAttribute('mageants_custom_stock_status')->getValue();
            } else {
                $productOptionId = '';
            }

            if ($product->getCustomAttribute('mageants_custom_stock_rule') != null) {
                $productOptionRule = $product->getCustomAttribute('mageants_custom_stock_rule')->getValue();
            } else {
                $productOptionRule = '';
            }
            
            if ($product->getCustomAttribute('mageants_qty_base_rule_status') != null) {
                $productOptionQtyRule = $product->getCustomAttribute('mageants_qty_base_rule_status')->getValue();
            } else {
                $productOptionQtyRule = 0;
            }
            

            $productId = $product->getId();
            
            $customLable = $this->helper->getCustomStockLabel($productOptionId, $productOptionRule, $productOptionQtyRule, $productId);
            if (!empty($customLable)) {
                $labelAndIcons[$product->getId()]['icon'] = $customLable['icon'];
                $labelAndIcons[$product->getId()]['label'] = $customLable['label'];
            }
        }

        if (!empty($labelAndIcons)) {
            $config['customlable'] = $labelAndIcons;
        } else {
            $config['customlable'] = [];
        }

        $config['changeConfigProduct'] = $this->helper->getchangeConfigProduct();
        return $this->jsonEncoder->encode($config);
    }
}
