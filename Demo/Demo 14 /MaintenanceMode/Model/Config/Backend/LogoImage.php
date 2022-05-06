<?php
namespace Mageants\MaintenanceMode\Model\Config\Backend;

class LogoImage extends \Magento\Config\Model\Config\Backend\Image
{
    /**
     * The tail part of directory path for uploading
     */
    const UPLOAD_DIR = 'mageants/maintenancemode/logo';
    
   /**
     * Return path to directory for upload file
     *
     * @return string
     * @throw \Magento\Framework\Exception\LocalizedException
     */
    protected function _getUploadDir()
    {
        return $this->_mediaDirectory->getAbsolutePath($this->_appendScopeInfo(self::UPLOAD_DIR));
    }

    /**
     * Makes a decision about whether to add info about the scope.
     *
     * @return boolean
     */
    protected function _addWhetherScopeInfo()
    {
        return true;
    }

    /**
     * Getter for allowed extensions of uploaded files.
     *
     * @return string[]
     */
    protected function _getAllowedExtensions()
    {
        return ['jpg', 'jpeg', 'gif', 'png'];
    }

    /**
     * @return string|null
     */
    protected function getTmpFileName()
    {
        $tmpName = null;
        if (isset($_FILES['groups'])) {
            $tmpName = $_FILES['groups']['tmp_name'][$this->getGroupId()]['fields'][$this->getField()]['value'];
        } else {
            $tmpName = is_array($this->getValue()) ? $this->getValue()['tmp_name'] : null;
        }
        return $tmpName;
    }

    /**
     * Save uploaded file before saving config value
     *
     * Save changes and delete file if "delete" option passed
     *
     * @return $this
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        $upload_max_filesize = ini_get('upload_max_filesize');
        $file_size = preg_replace("/[^0-9]/", "", $upload_max_filesize) * 1000000;
        $deleteFlag = is_array($value) && !empty($value['delete']);
        $fileTmpName = $this->getTmpFileName();
        
        if(($value['size'] >= $file_size || $value['size'] == '0')) {
            if ($value['error'] > '1'){
                if ($this->getOldValue() && ($fileTmpName || $deleteFlag)) {
                    $this->_mediaDirectory->delete(self::UPLOAD_DIR . '/' . $this->getOldValue());
                }
            }else{
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The file you\'re uploading exceeds the server size limit of %1.', $upload_max_filesize)
                );
            }
        }
        return parent::beforeSave();
    }
}