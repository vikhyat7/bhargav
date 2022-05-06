<?php
/**
 * @category Mageants StoreViewPricing
 * @package Mageants_StoreViewPricing
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\StoreViewPricing\Plugin;

class Initialization
{
    public function __construct(
        \Mageants\StoreViewPricing\Helper\Data $storeViewPricingHelper,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->storeViewPricingHelper = $storeViewPricingHelper;
        $this->request = $request;
    }

    public function beforeInitializeFromData($subject, $product, $data)
    {
        $storeId = $this->request->getParam('store', 0);
        if ($this->storeViewPricingHelper->priceScope() == 2 && $storeId != 0) {
            if (isset($data['price'])) {
                unset($data['price']);
            }
            if (isset($data['special_price'])) {
                unset($data['special_price']);
            }
            if (isset($data['cost'])) {
                unset($data['cost']);
            }
            if (isset($data['msrp'])) {
                unset($data['msrp']);
            }
            if (isset($data['tier_price'])) {
                unset($data['tier_price']);
            }
            if (isset($data['msrp_display_actual_price_type'])) {
                unset($data['msrp_display_actual_price_type']);
            }
            if (isset($data['special_from_date'])) {
                unset($data['special_from_date']);
            }
            if (isset($data['special_to_date'])) {
                unset($data['special_to_date']);
            }
        }
        return [$product,$data];
    }
}
