<?php
/**
 * @category Mageants CustomStockStatus
 * @package Mageants_CustomStockStatus
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <info@mageants.com>
 */

namespace Mageants\CustomStockStatus\Model\File;

/**
 *
 */
class Uploader extends \Mageants\CustomStockStatus\Model\File\Framework\File\Uploader
{
    /**
     * Flag, that defines should DB processing be skipped
     *
     * @var bool
     */
    public $skipDbProcessing = false;

    /**
     * Core file storage
     *
     * @var \Magento\MediaStorage\Helper\File\Storage
     */
    public $coreFileStorage = null;

    /**
     * Core file storage database
     *
     * @var \Magento\MediaStorage\Helper\File\Storage\Database
     */
    public $coreFileStorageDb = null;

    /**
     * @var \Magento\MediaStorage\Model\File\Validator\NotProtectedExtension
     */
    public $validator;

    /**
     * @param string $fileId
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDb
     * @param \Magento\MediaStorage\Helper\File\Storage $coreFileStorage
     * @param \Magento\MediaStorage\Model\File\Validator\NotProtectedExtension $validator
     */
    public function __construct(
        $fileId,
        \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDb,
        \Magento\MediaStorage\Helper\File\Storage $coreFileStorage,
        \Magento\MediaStorage\Model\File\Validator\NotProtectedExtension $validator
    ) {
        $this->coreFileStorageDb = $coreFileStorageDb;
        $this->coreFileStorage = $coreFileStorage;
        $this->validator = $validator;
        parent::__construct($fileId);
    }

    /**
     * Save file to storage
     *
     * @param  array $result
     * @return $this
     */
    public function _afterSave($result)
    {
        if (empty($result['path']) || empty($result['file'])) {
            return $this;
        }

        if ($this->coreFileStorage->isInternalStorage() || $this->skipDbProcessing()) {
            return $this;
        }

        $this->_result['file'] = $this->coreFileStorageDb->saveUploadedFile($result);

        return $this;
    }

    /**
     * Getter/Setter for _skipDbProcessing flag
     *
     * @param null|bool $flag
     * @return bool|\Magento\MediaStorage\Model\File\Uploader
     */
    public function skipDbProcessing($flag = null)
    {
        if ($flag === null) {
            return $this->skipDbProcessing;
        }

        $this->skipDbProcessing = (bool)$flag;
        return $this;
    }

    /**
     * Check protected/allowed extension
     *
     * @param string $extension
     * @return boolean
     */
    public function checkAllowedExtension($extension)
    {
        //validate with protected file types
        if (!$this->validator->isValid($extension)) {
            return false;
        }

        return parent::checkAllowedExtension($extension);
    }

    /**
     * Get file size
     *
     * @return int
     */
    public function getFileSize()
    {
        return $this->_file['size'];
    }

    /**
     * Validate file
     *
     * @return array
     */
    public function validateFile()
    {
        $this->_validateFile();
        return $this->_file;
    }
}
