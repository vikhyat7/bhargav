<?php
/**
 * @category Mageants MaintenanceMode
 * @package Mageants_MaintenanceMode
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\MaintenanceMode\Block\Preview;

/**
 * Class Comingsoon
 *
 * @package Mageants\MaintenanceMode\Block\Preview
 */
// class Background extends Comingsoon
class Background extends \Magento\Framework\View\Element\Template
{
    const BG_TYPE = 'image';

    /**
     * @return mixed
     */
    public function getFullActionName()
    {
        return $this->getRequest()->getFullActionName();
    }

    /**
     * @return string
     */
    public function getBgType()
    {
        $code = $this->getFullActionName() === 'maintenancemode_preview_maintenance'
            ? '[maintenance_background]'
            : '[comingsoon_background]';

        $type = $this->getFormData()[$code];

        return $type === '1' ? self::BG_TYPE : $type;
    }

    /**
     * @return string
     */
    public function getListImagesUrls()
    {
        $code = $this->getFullActionName() === 'maintenancemode_preview_maintenance'
            ? '[maintenance_background_multi_image]'
            : '[comingsoon_background_multi_image]';

        return implode(',', $this->getMultipleImagesUrl($code));
    }

    /**
     * @return string
     */
    public function getPage()
    {
        return $this->getFullActionName() === 'maintenancemode_preview_maintenance' ? 'maintenance' : 'comingsoon';
    }

    /**
     * @return string
     */
    public function getImageCode()
    {
        return $this->getFullActionName() === 'maintenancemode_preview_maintenance'
            ? '[maintenance_background_image]'
            : '[comingsoon_background_image]';
    }

    /**
     * @return string
     */
    public function getVideoCode()
    {
        return $this->getFullActionName() === 'maintenancemode_preview_maintenance'
            ? '[maintenance_background_video]'
            : '[comingsoon_background_video]';
    }
}
