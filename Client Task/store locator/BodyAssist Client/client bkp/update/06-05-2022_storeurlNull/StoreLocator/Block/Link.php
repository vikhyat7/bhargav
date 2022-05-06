<?php
namespace Mageants\StoreLocator\Block;

class Link extends \Magento\Framework\View\Element\Html\Link\Current
{
    public function _toHtml()
    {
        if (!$this->_scopeConfig->isSetFlag('StoreLocator/module/storelocator') ||
            !$this->_scopeConfig->isSetFlag('StoreLocator/dealer/dealer_store_enable')
        ) {
            return '';
        }
        return parent::_toHtml();
    }
}
