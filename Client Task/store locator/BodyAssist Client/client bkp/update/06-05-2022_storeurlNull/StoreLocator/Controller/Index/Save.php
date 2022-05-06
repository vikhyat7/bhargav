<?php
/**
 * @category Mageants StoreLocator
 * @package Mageants_StoreLocator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@Mageants.com>
 */
namespace Mageants\StoreLocator\Controller\Index;

use Magento\Framework\View\Result\PageFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;

/**
 * Index for Store frontend
 */
class Save extends \Magento\Framework\App\Action\Action
{
    /**
     * Result PageFactory
     *
     * @var Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory;
    
    /**
     * @param \Magento\Backend\Block\Template\Context
     * @param Magento\Framework\View\Result\PageFactory
     */
    public function __construct(
        PageFactory $resultPageFactory,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Mageants\StoreLocator\Model\ManageStore $manageStore,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Filesystem\DirectoryList $directory_list,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Customer\Model\SessionFactory $customerSession,
        UploaderFactory $uploaderFactory,
        \Mageants\StoreLocator\Helper\Data $dataHelper
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->request = $request;
        $this->_manageStore = $manageStore;
        $this->directory_list = $directory_list;
        $this->_storeManager = $storeManager;
        $this->uploaderFactory = $uploaderFactory;
        $this->date=$date;
        $this->dataHelper = $dataHelper;
        $this->_customerSession = $customerSession->create();
        parent::__construct($context);
    }
    
    /**
     * perform execute method for Index
     *
     * @return $void
     */
    public function execute()
    {
        $model=$this->_manageStore;
        $data = $this->getRequest()->getPostValue();
        $files = $this->request->getFiles();
        if ($data) {
            $model=$this->_manageStore;
            
            if (isset($data['image']['delete'])) {
                $data['image']="";
            } else {
                if (isset($files['image']['name']) && $files['image']['name'] != '') {
                    $imagename=$this->uploadFile();
                    if ($imagename!="") {
                        $data['image']="Mageants".$imagename;
                    }
                } else {
                    if (isset($data['image'])) {
                        $data['image'] = $data['image']['value'];
                    }
                }
            }
            if (isset($data['icon']['delete'])) {
                $data['icon']="";
            } else {
                if (isset($files['icon']['name']) && $files['icon']['name'] != '') {
                    $imagename=$this->uploadIcon();
                    if ($imagename!="") {
                        $data['icon']="Mageants/Icon".$imagename;
                    }
                } else {
                    if (isset($data['icon'])) {
                        $data['icon'] = $data['icon']['value'];
                    }
                }
            }

            if (isset($data['mon_otime'])) {
                $data['mon_otime']=implode(",", $data['mon_otime']);
            }
            if (isset($data['mon_bstime'])) {
                $data['mon_bstime']=implode(",", $data['mon_bstime']);
            }
            if (isset($data['mon_betime'])) {
                $data['mon_betime']=implode(",", $data['mon_betime']);
            }
            if (isset($data['mon_ctime'])) {
                $data['mon_ctime']=implode(",", $data['mon_ctime']);
            }

            if (isset($data['tue_otime'])) {
                $data['tue_otime']=implode(",", $data['tue_otime']);
            }
            if (isset($data['tue_bstime'])) {
                $data['tue_bstime']=implode(",", $data['tue_bstime']);
            }
            if (isset($data['tue_betime'])) {
                $data['tue_betime']=implode(",", $data['tue_betime']);
            }
            if (isset($data['tue_ctime'])) {
                $data['tue_ctime']=implode(",", $data['tue_ctime']);
            }

            if (isset($data['wed_otime'])) {
                $data['wed_otime']=implode(",", $data['wed_otime']);
            }
            if (isset($data['wed_bstime'])) {
                $data['wed_bstime']=implode(",", $data['wed_bstime']);
            }
            if (isset($data['wed_betime'])) {
                $data['wed_betime']=implode(",", $data['wed_betime']);
            }
            if (isset($data['wed_ctime'])) {
                $data['wed_ctime']=implode(",", $data['wed_ctime']);
            }

            if (isset($data['thu_otime'])) {
                $data['thu_otime']=implode(",", $data['thu_otime']);
            }
            if (isset($data['thu_bstime'])) {
                $data['thu_bstime']=implode(",", $data['thu_bstime']);
            }
            if (isset($data['thu_betime'])) {
                $data['thu_betime']=implode(",", $data['thu_betime']);
            }
            if (isset($data['thu_ctime'])) {
                $data['thu_ctime']=implode(",", $data['thu_ctime']);
            }

            if (isset($data['fri_otime'])) {
                $data['fri_otime']=implode(",", $data['fri_otime']);
            }
            if (isset($data['fri_bstime'])) {
                $data['fri_bstime']=implode(",", $data['fri_bstime']);
            }
            if (isset($data['fri_betime'])) {
                $data['fri_betime']=implode(",", $data['fri_betime']);
            }
            if (isset($data['fri_ctime'])) {
                $data['fri_ctime']=implode(",", $data['fri_ctime']);
            }

            if (isset($data['sat_otime'])) {
                $data['sat_otime']=implode(",", $data['sat_otime']);
            }
            if (isset($data['sat_bstime'])) {
                $data['sat_bstime']=implode(",", $data['sat_bstime']);
            }
            if (isset($data['sat_betime'])) {
                $data['sat_betime']=implode(",", $data['sat_betime']);
            }
            if (isset($data['sat_ctime'])) {
                $data['sat_ctime']=implode(",", $data['sat_ctime']);
            }

            if (isset($data['sun_otime'])) {
                $data['sun_otime']=implode(",", $data['sun_otime']);
            }
            if (isset($data['sun_bstime'])) {
                $data['sun_bstime']=implode(",", $data['sun_bstime']);
            }
            if (isset($data['sun_betime'])) {
                $data['sun_betime']=implode(",", $data['sun_betime']);
            }
            if (isset($data['sun_ctime'])) {
                $data['sun_ctime']=implode(",", $data['sun_ctime']);
            }
            $data['country'] = $data['country_id'];
            $data['type'] = 'Dealer';
            $data['storeId'] = $this->_storeManager->getStore()->getId();
            
            if (isset($data['id'])) {
                $data["updated_at"] = $this->date->gmtDate();
            } else {
                $data["created_at"] = $this->date->gmtDate();
                $data['sstatus'] = 'Disabled';
            }
            $data["store_type_status"]="Dealer";
            $data["user_id"] = $this->_customerSession->getId();
            $resultRedirect = $this->resultRedirectFactory->create();
            $model->setData($data);
           
            if (isset($data['id'])) {
                $model->setId($data['id']);
            }
            try {
                $model->save();
                $PageUrl = $this->request->getServer('HTTP_REFERER');
                $PageUrl = strstr($PageUrl, "storelocator");
                if ($PageUrl == 'storelocator/index/addstore/') {
                    $this->dataHelper->sendMail();
                } elseif ($PageUrl == 'storelocator/index/editstore/store_id/'.$data['id'].'/') {
                    $this->dataHelper->sendEditMail();
                }
                $this->dataHelper->cachePrograme();
                return $resultRedirect->setPath('storelocator/index/dealer');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __($e->getMessage()));
            }
            $this->dataHelper->cachePrograme();
            return $resultRedirect->setPath('storelocator/index/dealer', ['store_id' => $this->getRequest()->getParam('store_id')]);
        }
    }

    /**
     * upload imege file
     *
     * @return $void
     */
    public function uploadFile()
    {
        $destinationPath = $this->getDestinationPath();
        
        try {
            $uploader = $this->uploaderFactory->create(['fileId' => 'image'])
                ->setAllowCreateFolders(true);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $result=$uploader->save($destinationPath);
                
            return $result['file'];
        } catch (\Exception $e) {
            $this->messageManager->addError(
                __($e->getMessage())
            );
        }
    }
    
    /**
     * upload imege file
     *
     * @return $void
     */
    public function uploadIcon()
    {
        $destinationPath = $this->getIconPath();
        try {
            $uploader = $this->uploaderFactory->create(['fileId' => 'icon'])
                ->setAllowCreateFolders(true);
            $uploader->setAllowedExtensions(['icon','ico','png']);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $result=$uploader->save($destinationPath);
                
            return $result['file'];
        } catch (\Exception $e) {
            $this->messageManager->addError(
                __($e->getMessage())
            );
        }
    }

    /**
     * Get Destination Path
     *
     * @return $directory_list
     */
    public function getDestinationPath()
    {
        return $this->directory_list->getPath('media')."/Mageants/";
    }
    
    /**
     * Get Destination Path
     *
     * @return $directory_list
     */
    public function getIconPath()
    {
        return $this->directory_list->getPath('media')."/Mageants/Icon";
    }
}
