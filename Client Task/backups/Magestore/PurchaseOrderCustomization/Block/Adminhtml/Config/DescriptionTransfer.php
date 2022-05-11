<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderCustomization\Block\Adminhtml\Config;

/**
 * Class DescriptionTransfer
 *
 * @package Magestore\PurchaseOrderCustomization\Block\Adminhtml\Config
 */
class DescriptionTransfer extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{

    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'title',
            [
                'label' => __('Description list'),
                'style' => 'width:240px'
            ]
        );

        $this->_addAfter = false;
    }
}
