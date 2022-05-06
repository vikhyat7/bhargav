<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\GiftCertificate\Controller\Adminhtml\Gcimages;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Magento\MediaStorage\Model\File\UploaderFactory;

/**
 * Save Image Template
 */ 
class Save extends Action
{
    /**
     * Upload factory
     *
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $uploaderFactory;

    /**
     * Result factory
     *
     * @var Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;
    
    /**
     * Image Id
     *
     * @var String
     */
    protected $fileId = 'image';
    /**
     * allowed extensions
     *
     * @var array
     */
     protected $allowedExtensions = ['jpg','jpeg','gif','png'];

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directory_list
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $uploadFactory
     * @param  \Magento\Framework\Controller\ResultFactory $resultFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\App\Filesystem\DirectoryList $directory_list,
        UploaderFactory $uploaderFactory
    ) {
        $this->resultFactory=$context;
        $this->uploaderFactory = $uploaderFactory;
        $this->directory_list = $directory_list;
        parent::__construct($context);
    }

    /**
     * Save Template for GiftCertificate
     */
    public function execute()
    {
       $data=$this->getRequest()->getPostValue();
       $urlkey=$this->getRequest()->getParam('back');
        if (!$data) {
                $this->_redirect('giftcertificate/gcimages/index');
                return;
         }
         if($data['image_title_upoad']){                    
                 $imagename=$this->uploadFile();
                 $data['image'] = 'templates/'.$imagename;
                    // $imagename=$data['image_title_upoad'];
         }
        else
        {
            if (isset($data['image']) && $data['image']['value']) 
            {
                $img = explode("/", $data['image']['value']);
                $imagename = $img[1];
            
                if($imagename!=null || $imagename!='')
                {
                    if(isset($data['image']['delete']))
                    {
                        $data['image']='';
                    }
                    else
                    {
                        if($imagename !='')
                        {
                           $data['image']="templates/".$imagename; 
                        }
                        else
                        {
                            if(isset($data['image']))
                            {
                                $imagevalue=$data['image']['value'];
                                $data['image']=$imagevalue;
                            }
                        }     
                    }    
                }
            }
        }
        try{
                        if($data['positiontop'] > 96){
                            $data['positiontop'] = 96;
                        }
                        if($data['positiontop'] < 0){
                            $data['positiontop'] = 0;
                        }
                        if($data['positionleft'] > 346){
                            $data['positionleft'] = 346;
                        }
                        if($data['positionleft'] < 0){
                            $data['positionleft'] = 0;
                        }
                        $templateData=$this->_objectManager->create('Mageants\GiftCertificate\Model\Templates');    
                        $templateData->setData($data);
                        if (isset($data['image_id'])) {
                                $templateData->setImageId($data['image_id']);
                            }
                            $templateData->save();
                            $this->messageManager->addSuccess(__('Template has been successfully saved.'));
                   }
                   catch(Exception $e){
                       $this->messageManager->addError(__($e->getMessage()));
                   }     
       

        $this->_redirect('giftcertificate/gcimages/index');

    }
    
    /**
     * Upload Image for Template
     */
    public function uploadFile(){
         $destinationPath = $this->getDestinationPath();
        
        try {
            $uploader = $this->uploaderFactory->create(['fileId' => $this->fileId])
                ->setAllowCreateFolders(true)
                ->setAllowedExtensions($this->allowedExtensions);
                $result=$uploader->save($destinationPath);

            if (!$result) {
                throw new LocalizedException(
                    __('File cannot be saved to path: $1', $destinationPath)
                );
            }
            return $result['file'];  
            
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
            var_dump($e->getMessage());
            exit();
        }
    }

    /**
     * @return String
     */
    public function getDestinationPath()
    {
       return $this->directory_list->getPath('media')."/templates/";
      
    }
}