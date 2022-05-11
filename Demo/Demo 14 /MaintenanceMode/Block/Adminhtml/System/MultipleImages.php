<?php
/**
 * @category Mageants MaintenanceMode
 * @package Mageants_MaintenanceMode
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\MaintenanceMode\Block\Adminhtml\System;

use Magento\Config\Block\System\Config\Form\Field;
use Mageants\MaintenanceMode\Block\Adminhtml\System\Renderer\Images;

/**
 * Class MultipleImages
 * @package Mageants\MaintenanceMode\Block\Adminhtml\System
 */
abstract class MultipleImages extends Field
{
    /**
     * @return mixed
     */
    public function setMultiImgElement()
    {
        return $this->_layout
            ->createBlock(Images::class)
            ->setTemplate('Mageants_MaintenanceMode::system/config/gallery.phtml')
            ->setId('media_gallery_content')
            ->setElement($this)
            ->setFormName('edit_form');
    }
}
