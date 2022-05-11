<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCertificate\Model;
use Magento\Framework\Exception\LocalizedException as CoreException;
/**
 * Templates Model class
 */
class Templates extends \Magento\Framework\Model\AbstractModel
{
    /**
     * init Templates Model class
     */
    protected function _construct()
    {
        $this->_init('Mageants\GiftCertificate\Model\ResourceModel\Templates');
    }

    /**
     * Save file to temparary Directory
     */
    public function saveFileToTmpDir($input)
    {
        try {
                $uploader = $this->uploaderFactory->create(array('fileId' => $input));
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(true);
                $uploader->setAllowCreateFolders(true);
                $result = $uploader->save($this->directory_list->getPath('media')."/templates/");
                return $result['file'];

        } catch (\Exception $e) {             
            if ($e->getCode() != \Magento\Framework\File\Uploader::TMP_NAME_EMPTY) {
          		echo $e->getMessage();
            }
        }
        return '';
    }
}
