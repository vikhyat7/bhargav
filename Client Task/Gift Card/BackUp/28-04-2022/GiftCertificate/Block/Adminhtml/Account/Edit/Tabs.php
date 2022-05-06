<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
 
namespace Mageants\GiftCertificate\Block\Adminhtml\Account\Edit;
/**
 * Tabs classs for order Tab
 */ 
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * _construct
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('post_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Account Information'));
    }
}
