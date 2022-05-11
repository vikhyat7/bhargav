<?php

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Storepickup
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Storepickup\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Image
 *
 * Used to create image helper
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Image extends \Magento\Framework\App\Helper\AbstractHelper
{
    const SMALL_IMAGE_SIZE_WIDTH = 40;
    const SMALL_IMAGE_SIZE_HEIGHT = 30;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $_mediaDirectory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magestore\Storepickup\Model\ImageUploaderFactory
     */
    protected $_imageUploaderFactory;

    /**
     * @var \Magento\Framework\Filesystem\DriverInterface
     */
    protected $driver;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $fileIo;

    /**
     * Image constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filesystem\DriverInterface $driver
     * @param \Magento\Framework\Filesystem\Io\File $fileIo
     * @param \Magestore\Storepickup\Model\ImageUploaderFactory $imageUploaderFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\DriverInterface $driver,
        \Magento\Framework\Filesystem\Io\File $fileIo,
        \Magestore\Storepickup\Model\ImageUploaderFactory $imageUploaderFactory
    ) {
        parent::__construct($context);

        $this->_mediaDirectory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $this->_objectManager = $objectManager;
        $this->_storeManager = $storeManager;
        $this->_imageUploaderFactory = $imageUploaderFactory;
        $this->driver = $driver;
        $this->fileIo = $fileIo;
    }

    /**
     * Get media url of image.
     *
     * @param string $imagePath
     * @return string
     */
    public function getMediaUrlImage($imagePath = '')
    {
        return $this->_storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $imagePath;
    }

    /**
     * Get media upload image
     *
     * @param \Magento\Framework\Model\AbstractModel $model
     * @param string $fileId
     * @param string $relativePath
     * @param bool $makeResize
     * @throws LocalizedException
     */
    public function mediaUploadImage(
        \Magento\Framework\Model\AbstractModel $model,
        $fileId,
        $relativePath,
        $makeResize = false
    ) {
        $imageRequest = $this->_getRequest()->getFiles($fileId);
        if ($imageRequest) {
            if (isset($imageRequest['name'])) {
                $fileName = $imageRequest['name'];
            } else {
                $fileName = '';
            }
        } else {
            $fileName = '';
        }
        if ($imageRequest && strlen($fileName)) {
            try {
                /** @var \Magento\MediaStorage\Model\File\Uploader $uploader */
                $uploader = $this->_imageUploaderFactory->create(['fileId' => $fileId]);
                $mediaAbsolutePath = $this->_mediaDirectory->getAbsolutePath($relativePath);
                $uploader->save($mediaAbsolutePath);

                /*
                 * resize to small image
                 */
                if ($makeResize) {
                    $this->_resizeImage(
                        $mediaAbsolutePath . $uploader->getUploadedFileName(),
                        self::SMALL_IMAGE_SIZE_WIDTH
                    );
                    $imagePath = $this->_getResizeImageFileName(
                        $relativePath . $uploader->getUploadedFileName()
                    );
                } else {
                    $imagePath = $relativePath . $uploader->getUploadedFileName();
                }

                $model->setData($fileId, $imagePath);
            } catch (\Exception $e) {
                throw new LocalizedException(
                    __($e->getMessage())
                );
            }
        } else {
            if ($model->getData($fileId) && empty($model->getData($fileId . '/delete'))) {
                $model->setData($fileId, $model->getData($fileId . '/value'));
            } else {
                $model->setData($fileId, '');
            }
        }
    }

    /**
     * Resize image.
     *
     * @param string $fileName
     * @param int $width
     * @param int|null $height
     */
    public function _resizeImage($fileName, $width, $height = null)
    {
        /** @var \Magento\Framework\Image $image */
        $image = $this->_objectManager->create(
            \Magento\Framework\Image::class,
            ['fileName' => $fileName]
        );
        $image->constrainOnly(true);
        $image->keepAspectRatio(true);
        $image->keepFrame(false);
        $image->resize($width, $height);
        $image->save($this->_getResizeImageFileName($fileName));
    }

    /**
     * Get resize image file name
     *
     * @param string $fileName
     * @return string
     */
    public function _getResizeImageFileName($fileName)
    {
        return $this->driver->getParentDirectory($fileName)
            . '/resize/' . $this->fileIo->getPathInfo($fileName)['basename'];
    }
}
