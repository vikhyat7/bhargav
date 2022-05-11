<?php
/**
 * @category Mageants MaintenanceMode
 * @package Mageants_MaintenanceMode
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\MaintenanceMode\Block;

use Magento\Framework\Exception\NoSuchEntityException;
use Mageants\MaintenanceMode\Model\Config\Source\System\RedirectTo;

/**
 * Class Background
 *
 * @package Mageants\MaintenanceMode\Block
 */
class Background extends Maintenance
{
    /**
     * @return array|mixed
     */
    public function redirectTo()
    {
        return $this->_helperData->getConfigGeneral('redirect_to');
    }

    /**
     * @return mixed
     */
    public function getFullActionName()
    {
        return $this->getRequest()->getFullActionName();
    }

    /**
     * @return mixed
     */
    public function getBackgroundType()
    {
        if ($this->redirectTo() === RedirectTo::MAINTENANCE_PAGE) {
            return $this->getMaintenanceSetting('maintenance_background');
        }        
    }

    /**
     * @return array|null
     * @throws NoSuchEntityException
     */
    public function getListImagesUrl()
    {
        if ($this->redirectTo() === RedirectTo::MAINTENANCE_PAGE) {
            return $this->getMultipleImagesUrl($this->getMaintenanceSetting('maintenance_background_multi_image'));
        }
    }

    /**
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function getBgImageUrl()
    {
        if ($this->redirectTo() === RedirectTo::MAINTENANCE_PAGE) {
            $image = $this->getMaintenanceSetting('maintenance_background_image');
            if (!$image) {
                return $this->getViewFileUrl(self::DEFAULT_MAINTENANCE_BG);
            }

            return $this->getImageUrl($image);
        }
        
        return '';
    }

    /**
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function getBgVideoUrl()
    {
        if ($this->redirectTo() === RedirectTo::MAINTENANCE_PAGE) {
            return $this->getVideoUrl($this->getMaintenanceSetting('maintenance_background_video'));
        }
    }

    /**
     * @return Maintenance
     */
    public function _prepareLayout()
    {
        $redirectTo = $this->_helperData->getConfigGeneral('redirect_to');
        if ($redirectTo === RedirectTo::MAINTENANCE_PAGE) {
            $this->pageConfig->getTitle()->set($this->getMaintenanceSetting('maintenance_title'));
        }
        return parent::_prepareLayout();
    }
}
