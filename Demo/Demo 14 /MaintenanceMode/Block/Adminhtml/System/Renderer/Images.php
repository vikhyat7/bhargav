<?php
/**
 * @category Mageants MaintenanceMode
 * @package Mageants_MaintenanceMode
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\MaintenanceMode\Block\Adminhtml\System\Renderer;

use Exception;
use Magento\Backend\Block\DataProviders\ImageUploadConfig;
use Magento\Backend\Block\Media\Uploader;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Mageants\MaintenanceMode\Helper\Data as HelperData;
use Mageants\MaintenanceMode\Helper\Image as HelperImage;

/**
 * Class Images
 *
 * @package Mageants\MaintenanceMode\Block\Adminhtml\System\Renderer
 */
class Images extends Widget
{
    /**
     * @var HelperImage
     */
    protected $_imageHelper;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    /**
     * Images constructor.
     *
     * @param Context $context
     * @param HelperImage $imageHelper
     * @param HelperData $helperData
     * @param ObjectManagerInterface $objectManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        HelperImage $imageHelper,
        HelperData $helperData,
        ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_imageHelper   = $imageHelper;
        $this->_helperData    = $helperData;
        $this->_objectManager = $objectManager;

        parent::__construct($context, $data);
    }

    /**
     * @return Widget
     */
    protected function _prepareLayout()
    {
        if ($this->_helperData->versionCompare('2.3.1')) {
            $uploadConfig = $this->_objectManager->get(ImageUploadConfig::class);
            $this->addChild(
                'uploader',
                Uploader::class,
                ['image_upload_config_data' => $uploadConfig]
            );
        } else {
            $this->addChild('uploader', Uploader::class);
        }

        $this->getUploader()->getConfig()->setUrl(
            $this->_urlBuilder->getUrl('maintenancemode/multiimages/upload')
        )->setFileField(
            'image'
        )->setFilters(
            [
                'images' => [
                    'label' => __('Images (.gif, .jpg, .png)'),
                    'files' => ['*.gif', '*.jpg', '*.jpeg', '*.png'],
                ],
            ]
        );

        return parent::_prepareLayout();
    }

    /**
     * @return bool|AbstractBlock
     */
    public function getUploader()
    {
        return $this->getChildBlock('uploader');
    }

    /**
     * @return string
     */
    public function getUploaderHtml()
    {
        return $this->getChildHtml('uploader');
    }

    /**
     * @return string
     */
    public function getJsObjectName()
    {
        return $this->getHtmlId() . 'JsObject';
    }

    /**
     * @param $code
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getImagesJson($code)
    {
        $data = $this->getElement()->getData('config_data');
        if (array_key_exists($code, $data)) {
            $value = HelperData::jsonDecode($data[$code]);
            if (is_array($value) && !empty($value)) {
                $mediaDir = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);
                $images   = $this->sortImagesByPosition($value);
                foreach ($images as $key => &$image) {
                    $image['url'] = $this->_imageHelper
                            ->getBaseMediaUrl() . '/' . $this->_imageHelper->getMediaPath($image['file']);
                    try {
                        $fileHandler   = $mediaDir->stat($this->_imageHelper->getMediaPath($image['file']));
                        $image['size'] = $fileHandler['size'];
                    } catch (Exception $e) {
                        $this->_logger->warning($e);
                        unset($images[$key]);
                    }
                }

                return HelperData::jsonEncode($images);
            }
        }

        return '[]';
    }

    /**
     * @param $images
     *
     * @return array
     */
    private function sortImagesByPosition($images)
    {
        if (is_array($images)) {
            usort(
                $images,
                function ($imageA, $imageB) {
                    return ($imageA['position'] < $imageB['position']) ? -1 : 1;
                }
            );
        }

        return $images;
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
     * @return string
     */
    public function getFieldName()
    {
        $response = $this->getResponse();

        return $response === 'maintenance'
            ? 'groups[maintenance_setting][fields][maintenance_background_multi_image]'
            : 'groups[comingsoon_setting][fields][comingsoon_background_multi_image]';
    }

    /**
     * @return string
     */
    public function getImageJson()
    {
        $response = $this->getResponse();

        return $response === 'maintenance'
            ? 'maintenancemode/maintenance_setting/maintenance_background_multi_image'
            : 'maintenancemode/comingsoon_setting/comingsoon_background_multi_image';
    }
}
