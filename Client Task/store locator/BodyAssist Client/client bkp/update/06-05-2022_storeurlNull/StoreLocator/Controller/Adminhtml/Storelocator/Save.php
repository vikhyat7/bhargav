<?php
/**
 * @category Mageants StoreLocator
 * @package Mageants_StoreLocator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@Mageants.com>
 */
namespace Mageants\StoreLocator\Controller\Adminhtml\Storelocator;

use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Backend\Model\Session;

/**
 * save store Action
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * Adapter Factory
     *
     * @var \Magento\Framework\Image\AdapterFactory
     */
    public $adapterFactory;
    
    /**
     * Upload Factory
     *
     * @var Magento\MediaStorage\Model\File\UploaderFactory
     */
    public $uploaderFactory;
    
    /**
     * File System
     *
     * @var \Magento\Framework\Filesystem
     */
    public $filesystem;
    
    /**
     * Field Id
     *
     * @var $string='image'
     */
    public $fileId = 'image';
    
    /**
     * Js Helper
     *
     * @var \Magento\Backend\Helper\Js
     */
    public $jsHelper;

    /**
     * Managet Store
     *
     * @var Mageants\StoreLocator\Model\ManageStore
     */
    public $manageStore;
    public $blockData;
    
    /**
     * @param \Magento\Backend\Block\Template\Context
     * @param \Magento\Backend\Helper\Js
     * @param \Magento\Framework\Image\AdapterFactory
     * @param Magento\MediaStorage\Model\File\UploaderFactory
     * @param \Magento\Framework\App\Filesystem\DirectoryList
     * @param \Magento\Framework\Filesystem
     * @param Mageants\StoreLocator\Model\ManageStore
     */
    public function __construct(
        Action\Context $context,
        \Magento\Backend\Helper\Js $jsHelper,
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        UploaderFactory $uploaderFactory,
        \Magento\Framework\App\Filesystem\DirectoryList $directory_list,
        \Magento\Framework\Filesystem $filesystem,
        \Mageants\StoreLocator\Model\ManageStore $manageStore,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Mageants\StoreLocator\Helper\Data $dataHelper,
        \Magento\Customer\Model\Customer $customers
    ) {
        $this->_jsHelper = $jsHelper;
        $this->adapterFactory = $adapterFactory;
        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem;
        $this->directory_list = $directory_list;
        $this->_manageStore = $manageStore;
        $this->resourceConnection = $resourceConnection;
        $this->session = $context->getSession();
        $this->dataHelper = $dataHelper;
        $this->customers = $customers;
        parent::__construct($context);
    }
    
    /**
     * {@inheritdoc}
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mageants_StoreLocator::save');
    }

    /**
     * perform execute method for save Action
     *
     * @return $resultRedirect
     */
    //@codingStandardsIgnoreLine
    public function execute()
    {
        $store_id = $this->getRequest()->getParam('id');
        $type_status='';
        $collection = $this->_manageStore->getCollection()->addFieldToFilter('store_id', $store_id);
        foreach ($collection as $key => $value) {
            $storeData =$value->getData();
            $status = $storeData['sstatus'];
            $type_status =$storeData['store_type_status'];
        }
        $data =$this->getRequest()->getPostValue();
        if ($data['sstatus']=="Enabled") {
            $data['is_active'] = 1;
        } else {
            $data['is_active'] = 0;
        }
        $files= $this->getRequest()->getFiles();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $collection =  $this->customers->getCollection()->getData();
            $email ='';
            $userID=0;
            $userData ='';
            foreach ($collection as $key => $value) {
                if ($data['email'] == $value['email']) {
                    $email = $value['email'];
                }
            }
            if ($email) {
                $collection =  $this->customers->getCollection()->addFieldToFilter('email', $email);
                $userData = $collection->getData();
                foreach ($userData as $UserId => $UserIdValue) {
                    $userID = $UserIdValue['entity_id'];
                    $userName = $UserIdValue['firstname'];
                }
            }

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

            if (isset($data['storeId'])) {
                if (in_array('0', $data['storeId'])) {
                    $data['storeId'] = '0';
                } else {
                    $data['storeId'] = implode(",", $data['storeId']);
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
            if ($data['type'] == '') {
                $data['type'] = 'Owner';
            }
            if ($type_status == '') {
                $data['store_type_status']="Owner";
            }
            if ($type_status == 'Dealer') {
                $data['store_type_status']="Dealer";
            }
            if ($userID) {
                $data["user_id"] = $userID;
            }
            $model->setData($data);
            if (isset($data['id'])) {
                $model->setId($data['id']);
            }
            try {

                // var_dump($model);exit();
                $model->save();
                $PageUrl = $this->getRequest()->getServer('HTTP_REFERER');
                if (strpos($PageUrl, "edit")) {
                    if ($this->getRequest()->getParam('sstatus') == 'Enabled' && $status == 'Disabled') {
                        $this->saveProducts($model, $data);
                        $this->dataHelper->sendEditMailAdmin();
                    } else {
                        $this->saveProducts($model, $data);
                        $this->messageManager->addSuccess(__('You saved this Record.'));
                    }
                } elseif (strpos($PageUrl, "new")) {
                    $this->saveProducts($model, $data);
                    $this->dataHelper->sendAddMailAdmin();
                }
                /*$this->saveProducts($model, $data);
                $this->messageManager->addSuccess(__('You saved this Record.'));*/
                $this->session->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit/', ['store_id' => $model->getId(), '_current' => true]);
                }
                $this->dataHelper->cachePrograme();
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __($e->getMessage()));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['store_id' => $this->getRequest()->getParam('store_id')]);
        }
        $this->dataHelper->cachePrograme();
        return $resultRedirect->setPath('*/*/');
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

    /**
     * save product for store
     *
     * @return $void
     */
    public function saveProducts($model, $post)
    {
        if (isset($post['products'])) {
            $productIds = $this->_jsHelper->decodeGridSerializedInput($post['products']);
            try {
                $oldProducts = (array) $model->getProducts($model);
                $newProducts = (array) $productIds;
                
                $this->_resources = $this->resourceConnection;
                $connection = $this->_resources->getConnection();
                $table = $this->_resources->getTableName(
                    \Mageants\StoreLocator\Model\ResourceModel\ManageStore::TBL_ATT_PRODUCT
                );
                $insert = array_diff($newProducts, $oldProducts);
                $delete = array_diff($oldProducts, $newProducts);
                if ($delete) {
                    $where = ['store_id = ?' => (int)$model->getId(), 'product_id IN (?)' => $delete];
                    $connection->delete($table, $where);
                }

                if ($insert) {
                    $data = [];
                    foreach ($insert as $product_id) {
                        $data[] = ['store_id' => (int)$model->getId(), 'product_id' => (int)$product_id];
                    }
                    $connection->insertMultiple($table, $data);
                }
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Store.'));
            }
        }
    }
}
