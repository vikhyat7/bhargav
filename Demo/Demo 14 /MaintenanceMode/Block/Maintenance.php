<?php
/**
 * @category Mageants MaintenanceMode
 * @package Mageants_MaintenanceMode
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\MaintenanceMode\Block;

use Magento\Cms\Block\Block;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Framework\View\Element\Messages;
use Magento\Framework\View\Element\Template;
use Mageants\MaintenanceMode\Helper\Data as HelperData;
use Mageants\MaintenanceMode\Helper\Image as HelperImage;
use Mageants\MaintenanceMode\Model\Config\Source\System\RedirectTo;

/**
 * Class Maintenance
 *
 * @package Mageants\MaintenanceMode\Block
 */
class Maintenance extends Template
{
    const PROGRESS_VALUE           = 50;
    const DEFAULT_MAINTENANCE_LOGO = 'Mageants_MaintenanceMode::media/maintenance_logo.png';
    const DEFAULT_MAINTENANCE_BG   = 'Mageants_MaintenanceMode::media/maintenance_bg.jpg';
    const DEFAULT_COMING_SOON_LOGO = 'Mageants_MaintenanceMode::media/coming_soon_logo.png';
    const DEFAULT_COMING_SOON_BG   = 'Mageants_MaintenanceMode::media/coming_soon_bg.jpg';

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * @var HelperImage
     */
    protected $_helperImage;

    /**
     * @var CustomerFactory
     */
    protected $_customerFactory;

    /**
     * Maintenance constructor.
     *
     * @param HelperData $helperData
     * @param HelperImage $helperImage
     * @param Template\Context $context
     * @param CustomerFactory $customerFactory
     * @param array $data
     */
    public function __construct(
        HelperData $helperData,
        HelperImage $helperImage,
        Template\Context $context,
        CustomerFactory $customerFactory,
        array $data = []
    ) {
        $this->_helperData      = $helperData;
        $this->_helperImage     = $helperImage;
        $this->_customerFactory = $customerFactory;

        parent::__construct($context, $data);
    }

    /**
     * @param $logo
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getLogo($logo)
    {
        $redirectTo = $this->_helperData->getConfigGeneral('redirect_to');

        if ($redirectTo === RedirectTo::MAINTENANCE_PAGE && !$logo) {
            return $this->getViewFileUrl(self::DEFAULT_MAINTENANCE_LOGO);
        }

        return $this->_helperImage->getMediaUrl(
            $this->_helperImage->getMediaPath(
                $logo,
                HelperImage::TEMPLATE_MEDIA_TYPE_LOGO
            )
        );
    }

    /**
     * @param $image
     *
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function getImageUrl($image)
    {
        if (empty($image)) {
            return null;
        }

        return $this->_helperImage->getMediaUrl(
            $this->_helperImage->getMediaPath(
                $image,
                HelperImage::TEMPLATE_MEDIA_TYPE_IMAGE
            )
        );
    }

    /**
     * @param $video
     *
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function getVideoUrl($video)
    {
        if (empty($video)) {
            return null;
        }

        return $this->_helperImage->getMediaUrl(
            $this->_helperImage->getMediaPath(
                $video,
                HelperImage::TEMPLATE_MEDIA_TYPE_VIDEO
            )
        );
    }

    /**
     * @param $images
     *
     * @return array
     */
    public function getListMultipleImages($images)
    {
        $data = HelperData::jsonDecode($images);
        $list = [];

        foreach ($data as $key => $value) {
            $list[] = $value['file'];
        }

        return $list;
    }

    /**
     * @param $images
     *
     * @return array|null
     * @throws NoSuchEntityException
     */
    public function getMultipleImagesUrl($images)
    {
        $urls   = [];
        $images = $this->getListMultipleImages($images);
        if (empty($images)) {
            return null;
        }

        foreach ($images as $image) {
            $urls[] = $this->_helperImage->getMediaUrl($this->_helperImage->getMediaPath($image));
        }

        return $urls;
    }

    /**
     * @param $type
     *
     * @return mixed|string
     */
    public function getPageTitle($type)
    {
        if ($type === 'maintenance_title') {
            $title = $this->_helperData->getMaintenanceSetting($type);

            return empty($title) ? __('Under Maintenance') : $title;
        }
        return;
    }

    /**
     * @param $type
     *
     * @return mixed|string
     */
    public function getPageDescription($type)
    {
        if ($type === 'maintenance_description') {
            $des = $this->_helperData->getMaintenanceSetting($type);

            return empty($des) ? __('We\'re currently down for maintenance. Be right back!') : $des;
        }
        return;
    }

    /**
     * @return int|mixed
     */
    public function getProgressValue()
    {
        $value = $this->_helperData->getMaintenanceSetting('maintenance_progress_value');

        return empty($value) ? self::PROGRESS_VALUE : $value;
    }

    /**
     * @return mixed|string
     */
    public function getProgressLabel()
    {
        $label = $this->_helperData->getMaintenanceSetting('maintenance_progress_label');

        return isset($label) ? $label : '';
    }

    /**
     * @return array
     */
    public function getSocialList()
    {
        $list    = [
            'social_facebook',
            'social_twitter',
            'social_instagram',
            'social_google',
            'social_skype',
            'social_linkedin',            
            'social_youtube',
            'social_pinterest'
        ];
        $url     = [];
        $imgPath = 'Mageants_MaintenanceMode::media/';

        foreach ($list as $value) {
            $url[] = [
                'link' => $this->_helperData->getSocialSetting($value),
                // 'img'  => $this->getViewFileUrl($imgPath . $value . '.png')
                'img'  => substr($value, 7)
            ];
        }

        return $url;
    }

    /**
     * @param $code
     *
     * @return mixed
     */
    public function getMaintenanceSetting($code)
    {
        return $this->_helperData->getMaintenanceSetting($code);
    }

    /**
     * @param $code
     *
     * @return mixed
     */
    public function getSubscribeSetting($code)
    {
        return $this->_helperData->getSubscribeSetting($code);
    }

    /**
     * @param $code
     *
     * @return mixed
     */
    public function getSocialSetting($code)
    {
        return $this->_helperData->getSocialSetting($code);
    }

    /**
     * @param $code
     *
     * @return mixed
     */
    public function getClockSetting($code)
    {
        return $this->_helperData->getClockSetting($code);
    }

    /**
     * Copy from the Magento core.
     *
     * @param string $string
     * @param bool $escapeSingleQuote
     *
     * @return string
     */
    public function escapeHtmlAttr($string, $escapeSingleQuote = true)
    {
        return $this->_escaper->escapeHtmlAttr($string, $escapeSingleQuote);
    }

    /**
     * @return Phrase|string
     */
    public function checkRegister()
    {
        $currentTime      = strtotime($this->_localeDate->date()->format('m/d/Y H:i:s'));
        $lastRegisterTime = $this->_customerFactory->create()
            ->getCollection()
            ->getLastItem()
            ->getCreatedAt();
        $realTime         = strtotime($this->_localeDate->date($lastRegisterTime)->format('m/d/y H:i:s'));
        $compare          = $currentTime - $realTime;

        if ($compare < 3) {
            $msg = $this->_layout->createBlock(Messages::class)
                ->addSuccess(__('Thank you for registering.'))
                ->toHtml();

            return $msg;
        }

        return '';
    }
}
