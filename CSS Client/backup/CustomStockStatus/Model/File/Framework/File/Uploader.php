<?php
/**
 * @category Mageants CustomStockStatus
 * @package Mageants_CustomStockStatus
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <info@mageants.com>
 */
namespace Mageants\CustomStockStatus\Model\File\Framework\File;

use Magento\Framework\File\Mime;

/**
 * File upload class
 */
class Uploader
{
    /**
     * Uploaded file handle (copy of $_FILES[] element)
     *
     * @var array
     * @access protected
     */
    public $file;

    /**
     * Upload type. Used to right handle $_FILES array.
     *
     * @var \Magento\Framework\File\Uploader::SINGLE_STYLE|\Magento\Framework\File\Uploader::MULTIPLE_STYLE
     * @access protected
     */
    public $uploadType;

    /**
     * The name of uploaded file. By default it is original file name, but when
     * we will change file name, this variable will be changed too.
     *
     * @var string
     * @access protected
     */
    public $uploadedFileName;

    /**
     * The name of destination directory
     *
     * @var string
     * @access protected
     */
    public $uploadedFileDir;

    /**
     * If this variable is set to TRUE, our library will be able to automatically create
     * non-existent directories.
     *
     * @var bool
     * @access protected
     */
    public $allowCreateFolders = true;

    /**
     * If this variable is set to TRUE, uploaded file name will be changed if some file with the same
     * name already exists in the destination directory (if enabled).
     *
     * @var bool
     * @access protected
     */
    public $allowRenameFiles = false;

    /**
     * If this variable is set to TRUE, files dispertion will be supported.
     *
     * @var bool
     * @access protected
     */
    public $enableFilesDispersion = false;

    /**
     * This variable is used both with $enableFilesDispersion == true
     * It helps to avoid problems after migrating from case-insensitive file system to case-insensitive
     * (e.g. NTFS->ext or ext->NTFS)
     *
     * @var bool
     * @access protected
     */
    public $caseInsensitiveFilenames = true;

    /**
     * @var string
     * @access protected
     */
    public $dispretionPath = null;

    /**
     * @var bool
     */
    public $fileExists = false;

    /**
     * @var null|string[]
     */
    public $allowedExtensions = null;

    /**
     * Validate callbacks storage
     *
     * @var array
     * @access protected
     */
    public $validateCallbacks = [];

    /**
     * @var \Magento\Framework\File\Mime
     */
    private $fileMime;

    /**#@+
     * File upload type (multiple or single)
     */
    const SINGLE_STYLE = 0;

    const MULTIPLE_STYLE = 1;

    /**#@-*/

    /**
     * Temp file name empty code
     */
    const TMP_NAME_EMPTY = 666;

    /**
     * Maximum Image Width resolution in pixels. For image resizing on client side
     * @deprecated
     * @see \Magento\Framework\Image\Adapter\UploadConfigInterface::getMaxWidth()
     */
    const MAX_IMAGE_WIDTH = 4096;

    /**
     * Maximum Image Height resolution in pixels. For image resizing on client side
     * @deprecated
     * @see \Magento\Framework\Image\Adapter\UploadConfigInterface::getMaxHeight()
     */
    const MAX_IMAGE_HEIGHT = 2160;

    /**
     * Resulting of uploaded file
     *
     * @var array|bool      Array with file info keys: path, file. Result is
     *                      FALSE when file not uploaded
     */
    public $result;

    /**
     * Init upload
     *
     * @param string|array $fileId
     * @param \Magento\Framework\File\Mime|null $fileMime
     * @throws \Exception
     */
    public function __construct(
        $fileId,
        Mime $fileMime = null
    ) {
        $this->_setUploadFileId($fileId);
        if (!file_exists($this->file['tmp_name'])) {
            $code = empty($this->file['tmp_name']) ? self::TMP_NAME_EMPTY : 0;
            throw new \Exception('The file was not uploaded.', $code);
        } else {
            $this->fileExists = true;
        }
        $this->fileMime = $fileMime ?: \Magento\Framework\App\ObjectManager::getInstance()->get(Mime::class);
    }

    /**
     * After save logic
     *
     * @param  array $result
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function _afterSave($result)
    {
        return $this;
    }

    /**
     * Used to save uploaded file into destination folder with original or new file name (if specified).
     *
     * @param string $destinationFolder
     * @param string $newFileName
     * @return array
     * @throws \Exception
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function save($destinationFolder, $newFileName = null)
    {
        $this->_validateFile();
        $this->validateDestination($destinationFolder);

        $this->result = false;
        $destinationFile = $destinationFolder;
        $fileName = isset($newFileName) ? $newFileName : $this->file['name'];
        $fileName = static::getCorrectFileName($fileName);
        if ($this->enableFilesDispersion) {
            $fileName = $this->correctFileNameCase($fileName);
            $this->setAllowCreateFolders(true);
            $this->dispretionPath = static::getDispersionPath($fileName);
            $destinationFile .= $this->dispretionPath;
            $this->_createDestinationFolder($destinationFile);
        }

        if ($this->allowRenameFiles) {
            $fileName = static::getNewFileName(
                static::_addDirSeparator($destinationFile) . $fileName
            );
        }

        $destinationFile = static::_addDirSeparator($destinationFile) . $fileName;

        try {
            $this->result = $this->_moveFile($this->file['tmp_name'], $destinationFile);
        } catch (\Exception $e) {
            // if the file exists and we had an exception continue anyway
            if (file_exists($destinationFile)) {
                $this->result = true;
            } else {
                throw $e;
            }
        }

        if ($this->result) {
            if ($this->enableFilesDispersion) {
                $fileName = str_replace('\\', '/', self::_addDirSeparator($this->dispretionPath)) . $fileName;
            }

            $this->uploadedFileName = $fileName;
            $this->uploadedFileDir = $destinationFolder;
            $this->result = $this->file;
            $this->result['path'] = $destinationFolder;
            $this->result['file'] = $fileName;

            $this->_afterSave($this->result);
        }

        return $this->result;
    }

    /**
     * Validates destination directory to be writable
     *
     * @param string $destinationFolder
     * @return void
     * @throws \Exception
     */
    private function validateDestination($destinationFolder)
    {
        if ($this->allowCreateFolders) {
            $this->_createDestinationFolder($destinationFolder);
        }

        if (!is_writable($destinationFolder)) {
            throw new \Exception('Destination folder is not writable or does not exists.');
        }
    }

    /**
     * Set access permissions to file.
     *
     * @param string $file
     * @return void
     *
     * @deprecated 100.0.8
     */
    public function chmod($file)
    {
        chmod($file, 0777);
    }

    /**
     * Move files from TMP folder into destination folder
     *
     * @param string $tmpPath
     * @param string $destPath
     * @return bool|void
     */
    public function _moveFile($tmpPath, $destPath)
    {
        if (is_uploaded_file($tmpPath)) {
            return move_uploaded_file($tmpPath, $destPath);
        } elseif (is_file($tmpPath)) {
            return rename($tmpPath, $destPath);
        }
    }

    /**
     * Validate file before save
     *
     * @return void
     * @throws \Exception
     */
    public function _validateFile()
    {
        if ($this->fileExists === false) {
            return;
        }

        //is file extension allowed
        if (!$this->checkAllowedExtension($this->getFileExtension())) {
            throw new \Exception('Disallowed file type.');
        }

        //run validate callbacks
        foreach ($this->validateCallbacks as $params) {
            if (is_object($params['object'])
                && method_exists($params['object'], $params['method'])
                && is_callable([$params['object'], $params['method']])
            ) {
                $params['object']->{$params['method']}($this->file['tmp_name']);
            }
        }
    }

    /**
     * Returns extension of the uploaded file
     *
     * @return string
     */
    public function getFileExtension()
    {
        return $this->fileExists ? pathinfo($this->file['name'], PATHINFO_EXTENSION) : '';
    }

    /**
     * Add validation callback model for us in self::_validateFile()
     *
     * @param string $callbackName
     * @param object $callbackObject
     * @param string $callbackMethod    Method name of $callbackObject. It must
     *                                  have interface (string $tmpFilePath)
     * @return \Magento\Framework\File\Uploader
     */
    public function addValidateCallback($callbackName, $callbackObject, $callbackMethod)
    {
        $this->validateCallbacks[$callbackName] = ['object' => $callbackObject, 'method' => $callbackMethod];
        return $this;
    }

    /**
     * Delete validation callback model for us in self::_validateFile()
     *
     * @param string $callbackName
     * @access public
     * @return \Magento\Framework\File\Uploader
     */
    public function removeValidateCallback($callbackName)
    {
        if (isset($this->validateCallbacks[$callbackName])) {
            unset($this->validateCallbacks[$callbackName]);
        }

        return $this;
    }

    /**
     * Correct filename with special chars and spaces
     *
     * @param string $fileName
     * @return string
     */
    public static function getCorrectFileName($fileName)
    {
        $fileName = preg_replace('/[^a-z0-9_\\-\\.]+/i', '_', $fileName);
        $fileInfo = pathinfo($fileName);

        if (preg_match('/^_+$/', $fileInfo['filename'])) {
            $fileName = 'file.' . $fileInfo['extension'];
        }

        return $fileName;
    }

    /**
     * Convert filename to lowercase in case of case-insensitive file names
     *
     * @param string $fileName
     * @return string
     */
    public function correctFileNameCase($fileName)
    {
        if ($this->caseInsensitiveFilenames) {
            return strtolower($fileName);
        }

        return $fileName;
    }

    /**
     * Add directory separator
     *
     * @param string $dir
     * @return string
     */
    public static function _addDirSeparator($dir)
    {
        if (substr($dir, -1) != '/') {
            $dir .= '/';
        }

        return $dir;
    }

    /**
     * Used to check if uploaded file mime type is valid or not
     *
     * @param string[] $validTypes
     * @access public
     * @return bool
     */
    public function checkMimeType($validTypes = [])
    {
        if (!empty($validTypes)) {
            if (!in_array($this->_getMimeType(), $validTypes)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns a name of uploaded file
     *
     * @access public
     * @return string
     */
    public function getUploadedFileName()
    {
        return $this->uploadedFileName;
    }

    /**
     * Used to set {@link allowCreateFolders} value
     *
     * @param bool $flag
     * @access public
     * @return $this
     */
    public function setAllowCreateFolders($flag)
    {
        $this->allowCreateFolders = $flag;
        return $this;
    }

    /**
     * Used to set {@link allowRenameFiles} value
     *
     * @param bool $flag
     * @access public
     * @return $this
     */
    public function setAllowRenameFiles($flag)
    {
        $this->allowRenameFiles = $flag;
        return $this;
    }

    /**
     * Used to set {@link enableFilesDispersion} value
     *
     * @param bool $flag
     * @access public
     * @return $this
     */
    public function setFilesDispersion($flag)
    {
        $this->enableFilesDispersion = $flag;
        return $this;
    }

    /**
     * File names Case-sensitivity setter
     *
     * @param bool $flag
     * @return $this
     */
    public function setFilenamesCaseSensitivity($flag)
    {
        $this->caseInsensitiveFilenames = $flag;
        return $this;
    }

    /**
     * Set allowed extensions
     *
     * @param string[] $extensions
     * @return $this
     */
    public function setAllowedExtensions($extensions = [])
    {
        foreach ((array)$extensions as $extension) {
            $this->allowedExtensions[] = strtolower($extension);
        }

        return $this;
    }

    /**
     * Check if specified extension is allowed
     *
     * @param string $extension
     * @return boolean
     */
    public function checkAllowedExtension($extension)
    {
        if (!is_array($this->allowedExtensions) || empty($this->allowedExtensions)) {
            return true;
        }

        return in_array(strtolower($extension), $this->allowedExtensions);
    }

    /**
     * Return file mime type
     *
     * @return string
     */
    private function _getMimeType()
    {
        return $this->fileMime->getMimeType($this->file['tmp_name']);
    }

    /**
     * Set upload field id
     *
     * @param string|array $fileId
     * @return void
     * @throws \Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    
    //@codingStandardsIgnoreStart
    private function _setUploadFileId($fileId)
    {
        //@codingStandardsIgnoreStart
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\App\RequestInterface');
        //@codingStandardsIgnoreEnd
        $files = $request->getFiles();
        if (is_array($fileId)) {
            $this->uploadType = self::MULTIPLE_STYLE;
            $this->file = $fileId;
        } else {
            if (empty($files)) {
                throw new \Exception('$files array is empty');
            }

            preg_match("/^(.*?)\[(.*?)\]$/", $fileId, $file);

            if (is_array($file) && !empty($file)) {
                array_shift($file);
                $this->uploadType = self::MULTIPLE_STYLE;
                $fileAttributes = $files[$file[0]];
                $tmpVar = [];
                $newarray = [];
                foreach ($fileAttributes as $attributeid => $attributeValue) {
                    if (is_array($attributeValue)) {
                        if (!empty($attributeValue)) {
                            foreach ($attributeValue as $k => $val) {
                                $newarray[$k][$attributeid]= $val;
                            }
                        }
                    }
                }
                
                foreach ($newarray as $attributeName => $attributeValues) {
                    $tmpVar[$attributeName] = $attributeValues[$file[1]];
                }

                $fileAttributes = $tmpVar;
                $this->file = $fileAttributes;
            } elseif (!empty($fileId) && isset($files[$fileId])) {
                $this->uploadType = self::SINGLE_STYLE;
                $this->file = $files[$fileId];
            } elseif ($fileId == '') {
                throw new \Exception('Invalid parameter given. A valid $files identifier is expected.');
            }
        }
    }
    //@codingStandardsIgnoreEnd
    /**
     * Create destination folder
     *
     * @param string $destinationFolder
     * @return \Magento\Framework\File\Uploader
     * @throws \Exception
     */
    private function _createDestinationFolder($destinationFolder)
    {
        if (!$destinationFolder) {
            return $this;
        }

        if (substr($destinationFolder, -1) == '/') {
            $destinationFolder = substr($destinationFolder, 0, -1);
        }
        //@codingStandardsIgnoreStart
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $io = $objectManager->get('\Magento\Framework\Filesystem\Io\File');
        //@codingStandardsIgnoreEnd
        if (!file_exists($destinationFolder)) {
            try {
                $io->mkdir($destinationFolder, 0775);
            } catch (Exception $e) {
                throw new \Exception("Unable to create directory '{$destinationFolder}'.");
            }
        }

        return $this;
    }

    /**
     * Get new file name if the same is already exists
     *
     * @param string $destinationFile
     * @return string
     */
    public static function getNewFileName($destinationFile)
    {
        $fileInfo = pathinfo($destinationFile);
        if (file_exists($destinationFile)) {
            $index = 1;
            $baseName = $fileInfo['filename'] . '.' . $fileInfo['extension'];
            while (file_exists($fileInfo['dirname'] . '/' . $baseName)) {
                $baseName = $fileInfo['filename'] . '_' . $index . '.' . $fileInfo['extension'];
                $index++;
            }

            $destFileName = $baseName;
        } else {
            return $fileInfo['basename'];
        }

        return $destFileName;
    }

    /**
     * Get dispertion path
     *
     * @param string $fileName
     * @return string
     * @deprecated 101.0.4
     */
    public static function getDispretionPath($fileName)
    {
        return self::getDispersionPath($fileName);
    }

    /**
     * Get dispertion path
     *
     * @param string $fileName
     * @return string
     * @since 101.0.4
     */
    public static function getDispersionPath($fileName)
    {
        $char = 0;
        $dispertionPath = '';
        while ($char < 2 && $char < strlen($fileName)) {
            if (empty($dispertionPath)) {
                $dispertionPath = '/' . ('.' == $fileName[$char] ? '_' : $fileName[$char]);
            } else {
                $dispertionPath = self::_addDirSeparator(
                    $dispertionPath
                ) . ('.' == $fileName[$char] ? '_' : $fileName[$char]);
            }

            $char++;
        }

        return $dispertionPath;
    }
}
