<?php
/**
 * @category Mageants MaintenanceMode
 * @package Mageants_MaintenanceMode
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\MaintenanceMode\Helper;

use Exception;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\MediaStorage\Model\File\Uploader;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Mageants\MaintenanceMode\Helper\Media;

/**
 * Class Image
 *
 * @package Mageants\MaintenanceMode\Helper
 */
class Image extends Media
{
    const TEMPLATE_MEDIA_PATH       = 'mageants/maintenancemode';
    const TEMPLATE_MEDIA_TYPE_IMAGE = 'image';
    const TEMPLATE_MEDIA_TYPE_LOGO  = 'logo';
    const TEMPLATE_MEDIA_TYPE_VIDEO = 'video';

    /**
     * @var File
     */
    protected $_file;

    /**
     * Image constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param Filesystem $filesystem
     * @param UploaderFactory $uploaderFactory
     * @param AdapterFactory $imageFactory
     * @param File $file
     *
     * @throws FileSystemException
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        Filesystem $filesystem,
        UploaderFactory $uploaderFactory,
        AdapterFactory $imageFactory,
        File $file
    ) {
        $this->_file = $file;

        parent::__construct($context, $objectManager, $storeManager, $filesystem, $uploaderFactory, $imageFactory);
    }

    /**
     * @param $fileName
     * @param $descriptionPath
     *
     * @return string
     */
    public function getNotDuplicatedFilename($fileName, $descriptionPath)
    {
        $fileMediaName = $descriptionPath . '/'
            . Uploader::getNewFileName($this->mediaDirectory->getAbsolutePath($this->getMediaPath($fileName)));

        if ($fileMediaName !== $fileName) {
            return $this->getNotDuplicatedFilename($fileMediaName, $descriptionPath);
        }

        return $fileMediaName;
    }

    /**
     * @return string
     */
    public function getBaseTmpMediaPath()
    {
        return self::TEMPLATE_MEDIA_PATH . '/tmp';
    }

    /**
     * @param $file
     *
     * @return string
     */
    public function getTmpMediaPath($file)
    {
        return $this->getBaseTmpMediaPath() . '/' . $this->_prepareFile($file);
    }

    /**
     * @param $file
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getTmpMediaUrl($file)
    {
        return $this->getBaseTmpMediaUrl() . '/' . $this->_prepareFile($file);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getBaseTmpMediaUrl()
    {
        return $this->storeManager->getStore()
                ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $this->getBaseTmpMediaPath();
    }

    /**
     * @param $data
     *
     * @return $this
     * @throws LocalizedException
     */
    public function uploadImages(&$data)
    {
        if (isset($data['images']) && !empty($data['images'])) {
            $data['mp_bpr_images'] = self::jsonEncode($this->processImagesGallery($data['images']));
        }

        return $this;
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getBaseStaticUrl()
    {
        return $this->storeManager->getStore()
            ->getBaseUrl(UrlInterface::URL_TYPE_STATIC);
    }

    /**
     * @param $imageEntries
     *
     * @return array
     * @throws LocalizedException
     * @throws FileSystemException
     */
    protected function processImagesGallery($imageEntries)
    {
        foreach ($imageEntries as $key => &$image) {
            if (!isset($image['file']) || !$image['file']) {
                unset($imageEntries[$key]);
                continue;
            }

            $fileName = $image['file'];
            $pos      = strpos($fileName, '.tmp');

            if (isset($image['removed'])) {
                /**
                 * Remove image
                 */
                unset($imageEntries[$key]);

                if ($pos === false) {
                    $filePath = $this->getMediaPath($image['file']);
                    $file     = $this->mediaDirectory->getRelativePath($filePath);
                    if ($this->mediaDirectory->isFile($file)) {
                        $this->mediaDirectory->delete($filePath);
                    }
                }
            } elseif ($pos !== false) {
                /**
                 * Move image from tmp folder
                 */
                $fileName = substr($fileName, 0, $pos);
                $filePath = $this->getTmpMediaPath($fileName);
                $file     = $this->mediaDirectory->getRelativePath($filePath);
                if (!$this->mediaDirectory->isFile($file)) {
                    unset($imageEntries[$key]);
                    continue;
                }

                $pathInfo = $this->_file->getPathInfo($file);
                if (!isset($pathInfo['extension'])
                    || !in_array(strtolower($pathInfo['extension']), ['jpg', 'jpeg', 'gif', 'png'], true)
                ) {
                    unset($imageEntries[$key]);
                    continue;
                }

                $fileName       = Uploader::getCorrectFileName($pathInfo['basename']);
                $dispretionPath = Uploader::getDispretionPath($fileName);
                $fileName       = $dispretionPath . '/' . $fileName;

                $fileName        = $this->getNotDuplicatedFilename($fileName, $dispretionPath);
                $destinationFile = $this->getMediaPath($fileName);

                try {
                    $this->mediaDirectory->renameFile($file, $destinationFile);
                    $image['file'] = str_replace('\\', '/', $fileName);
                } catch (Exception $e) {
                    throw new LocalizedException(__('We couldn\'t move this file: %1.', $e->getMessage()));
                }
            }

            if (isset($image['removed'])) {
                unset($image['removed']);
            }
        }

        return array_values($imageEntries);
    }
}
