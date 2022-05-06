<?php
/**
 * @category Mageants StoreViewPricing
 * @package Mageants_StoreViewPricing
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
/**
 * Backend model for base currency
 */
namespace Mageants\StoreViewPricing\Block\Adminhtml\Frontend\Currency;

class Base extends \Magento\Directory\Block\Adminhtml\Frontend\Currency\Base
{
    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if ($this->getRequest()->getParam('website') != '') {
            $priceScope = $this->_scopeConfig->getValue(
                \Magento\Store\Model\Store::XML_PATH_PRICE_SCOPE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            if ($priceScope == \Magento\Store\Model\Store::PRICE_SCOPE_GLOBAL) {
                return '';
            }
        }
        if ($this->getRequest()->getParam('store') != '') {
            $priceScope = $this->_scopeConfig->getValue(
                \Magento\Store\Model\Store::XML_PATH_PRICE_SCOPE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            if ($priceScope == \Magento\Store\Model\Store::PRICE_SCOPE_GLOBAL
                || $priceScope == \Magento\Store\Model\Store::PRICE_SCOPE_WEBSITE) {
                return '';
            }
        }
        return parent::render($element);
    }
}
