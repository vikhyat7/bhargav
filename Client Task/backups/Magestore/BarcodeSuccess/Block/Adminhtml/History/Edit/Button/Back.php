<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\BarcodeSuccess\Block\Adminhtml\History\Edit\Button;

/**
 * Class Back
 */
class Back extends Generic
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getUrl('*/*/')),
            'class' => 'back',
            'sort_order' => 10
        ];
    }
}
