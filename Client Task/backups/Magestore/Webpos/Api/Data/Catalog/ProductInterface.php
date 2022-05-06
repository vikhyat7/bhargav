<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Catalog;

/**
 * Product data interface
 */
interface ProductInterface extends ProductOriginalInterface
{
    /**
     * Get list of product options
     *
     * @return \Magento\Catalog\Api\Data\ProductCustomOptionInterface[]|null
     */
    public function getCustomOptions();

    /**
     * Get list of product config options
     *
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\ConfigOptionsInterface[]|void
     */
    public function getConfigOption();

    /**
     * Get is options
     *
     * @return int
     */
    public function getIsOptions();

//    /**
//     * Get list of product bundle options
//     *
//     * @return \Magestore\Webpos\Api\Data\Catalog\Product\BundleOptionsInterface[]|null
//     */
//    public function getBundleOptions();
//
//    /**
//     * Get list of product grouped options
//     *
//     * @return \Magestore\Webpos\Api\Data\Catalog\Product\GroupedOptionsInterface[]|null
//     */
//    public function getGroupedOptions();

    /**
     * Composes configuration for js
     *
     * @return string
     */
    public function getJsonConfig();

    /**
     * Get JSON encoded configuration array which can be used for JS dynamic price
     *
     * Calculation depending on product options
     *
     * @return string
     */
    public function getPriceConfig();

    /**
     * Get gift card price config
     *
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardPriceOptionsInterface
     */
    public function getGiftCardPriceConfig();

//    /**
//     * Get barcode options
//     *
//     * @return \Magestore\Webpos\Api\Data\Catalog\Product\BarcodeOptionsInterface[]|null
//     */
//    public function getBarcodeOptions();

    /**
     * Get stocks data by product
     *
     * @return \Magestore\Webpos\Api\Data\Inventory\StockItemInterface[]|null
     */
    public function getStocks();

    /**
     * Get data of children product
     *
     * @return \Magestore\Webpos\Api\Data\Catalog\ProductOriginalInterface[]|null
     */
    public function getChildrenProducts();

    /**
     * Get qty in online mode
     *
     * @return float|null
     */
   /* public function getQtyOnline();*/

    /**
     * Get pos barcode of config webpos/product_search/barcod
     *
     * @return string|null
     */
    public function getPosBarcode();
}
