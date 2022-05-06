<?php
/**
 * @category   Mageants CMSImportExport
 * @package    Mageants_CMSImportExport
 * @copyright  Copyright (c) 2017 Mageants
 * @author     Mageants Team <support@mageants.com>
 */
namespace Mageants\StoreLocator\Controller\Adminhtml\Storelocator;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class AjaxImport extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    public $request;

    /**
     * result page Factory
     *
     * @var Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory;

    /**
     * @var \Magento\Cms\Model\Block
     */
    public $storeModel;
    public $productModel;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Cms\Model\Block $cmsBlockModel
     */
    //@codingStandardsIgnoreStart
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\App\Request\Http $request,
        \Mageants\StoreLocator\Model\ManageStore $storeModel,
        \Mageants\StoreLocator\Model\StoreProduct $productModel
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->request=$request;
        $this->storeModel=$storeModel;
        $this->productModel=$productModel;
    }
    //@codingStandardsIgnoreEnd
    /**
     * {@inheritdoc}
     */
    public function _isAllowed()
    {
        return true;
    }
    
    /**
     * Execute method for Attachment index action
     *
     * @return $resultPage
     */
    //@codingStandardsIgnoreLine
    public function execute()
    {
        $files= $this->getRequest()->getFiles();
        if ($files["import_store"]["name"]=="") {
            $responce=["message"=>"Please select file for upload","html"=>""];
            //@codingStandardsIgnoreLine
            echo json_encode($responce);
            return;
        }
        //@codingStandardsIgnoreLine
        $storeIds = [];
        $fh = fopen($files['import_store']['tmp_name'], 'r+');
        $ext=explode(".", $files['import_store']["name"]);
        $storeModel=$this->storeModel;
        $productModel=$this->productModel;
        $ext=end($ext);
        $lines = [];
        $replace=$this->getRequest()->getParam('replace');
        $i=0;
        $skip=0;
        $incremental = 0;
        $checkIfRowInserted = 0;
        //@codingStandardsIgnoreLine
        while (($row = fgetcsv($fh)) !== false) {
            $replaceWithId=0;
            if ($ext!="csv") {
                $responce=["message"=>"invalid file","html"=>""];
                //@codingStandardsIgnoreLine
                echo json_encode($responce);
                break;
            }

            if ($i==0) {
                //@codingStandardsIgnoreLine
                if (isset($row[55])=="region_id") {
                    if (trim($row[0])!="store_id" || trim($row[1])!="sname" || trim($row[2])!="storeId" || trim($row[3])!="position" || trim($row[4])!="address" || trim($row[5])!="city" || trim($row[6])!="country" || trim($row[7])!="postcode" || trim($row[8])!="region" || trim($row[9])!="email" || trim($row[10])!="phone" || trim($row[11])!="link" || trim($row[12])!="storeurl" || trim($row[13])!="image" || trim($row[14])!="icon" || trim($row[15])!="latitude" || trim($row[16])!="longitude" || trim($row[17])!="sstatus" || trim($row[18])!="updated_at" || trim($row[19])!="created_at" || trim($row[20])!="mon_open" || trim($row[21])!="mon_otime" || trim($row[22])!="mon_bstime" || trim($row[23])!="mon_betime" || trim($row[24])!="mon_ctime" || trim($row[25])!="tue_open" || trim($row[26])!="tue_otime" || trim($row[27])!="tue_bstime" || trim($row[28])!="tue_betime" || trim($row[29])!="tue_ctime" || trim($row[30])!="wed_open" || trim($row[31])!="wed_otime" || trim($row[32])!="wed_bstime" || trim($row[33])!="wed_betime" || trim($row[34])!="wed_ctime" || trim($row[35])!="thu_open" || trim($row[36])!="thu_otime" || trim($row[37])!="thu_bstime" || trim($row[38])!="thu_betime" || trim($row[39])!="thu_ctime" || trim($row[40])!="fri_open" || trim($row[41])!="fri_otime" || trim($row[42])!="fri_bstime" || trim($row[43])!="fri_betime" || trim($row[44])!="fri_ctime" || trim($row[45])!="sat_open" || trim($row[46])!="sat_otime" || trim($row[47])!="sat_bstime" || trim($row[48])!="sat_betime" || trim($row[49])!="sat_ctime" || trim($row[50])!="sun_open" || trim($row[51])!="sun_otime" || trim($row[52])!="sun_bstime" || trim($row[53])!="sun_betime" || trim($row[54])!="sun_ctime" || trim($row[55])!="region_id" ||trim($row[56])!="product_id") {
                        $responce=["message"=>"invalid file format","html"=>""];
                        //@codingStandardsIgnoreLine
                        echo json_encode($responce);
                        break;
                    }
                } else {
                    if (trim($row[0])!="store_id" || trim($row[1])!="sname" || trim($row[2])!="storeId" || trim($row[3])!="position" || trim($row[4])!="address" || trim($row[5])!="city" || trim($row[6])!="country" || trim($row[7])!="postcode" || trim($row[8])!="region" || trim($row[9])!="email" || trim($row[10])!="phone" || trim($row[11])!="link" || trim($row[12])!="storeurl" || trim($row[13])!="image" || trim($row[14])!="icon" || trim($row[15])!="latitude" || trim($row[16])!="longitude" || trim($row[17])!="sstatus" || trim($row[18])!="updated_at" || trim($row[19])!="created_at" || trim($row[20])!="mon_open" || trim($row[21])!="mon_otime" || trim($row[22])!="mon_bstime" || trim($row[23])!="mon_betime" || trim($row[24])!="mon_ctime" || trim($row[25])!="tue_open" || trim($row[26])!="tue_otime" || trim($row[27])!="tue_bstime" || trim($row[28])!="tue_betime" || trim($row[29])!="tue_ctime" || trim($row[30])!="wed_open" || trim($row[31])!="wed_otime" || trim($row[32])!="wed_bstime" || trim($row[33])!="wed_betime" || trim($row[34])!="wed_ctime" || trim($row[35])!="thu_open" || trim($row[36])!="thu_otime" || trim($row[37])!="thu_bstime" || trim($row[38])!="thu_betime" || trim($row[39])!="thu_ctime" || trim($row[40])!="fri_open" || trim($row[41])!="fri_otime" || trim($row[42])!="fri_bstime" || trim($row[43])!="fri_betime" || trim($row[44])!="fri_ctime" || trim($row[45])!="sat_open" || trim($row[46])!="sat_otime" || trim($row[47])!="sat_bstime" || trim($row[48])!="sat_betime" || trim($row[49])!="sat_ctime" || trim($row[50])!="sun_open" || trim($row[51])!="sun_otime" || trim($row[52])!="sun_bstime" || trim($row[53])!="sun_betime" || trim($row[54])!="sun_ctime" || trim($row[55])!="product_id") {
                        $responce=["message"=>"invalid file format","html"=>""];
                        //@codingStandardsIgnoreLine
                        echo json_encode($responce);
                        break;
                    }
                }

                $i++;
            } else {
                if ($row[2]=="") {
                    $row[2]=0;
                }
                $storecollection="";
                if ($replace=="on") {
                    $skip=1;
                    $ifReplaceisOn = 1;
                } else {
                    $skip=0;
                    $ifReplaceisOn = 0;
                }
                if ($skip==0) {
                    $storecollection=$this->storeModel->getCollection()
                        ->addFieldToSelect("*")
                        ->addFieldToFilter("store_id", $row[0]);
                    if ($storecollection instanceof Countable == 0) {
                        $data=[];
                        $data["sname"]=$row[1];
                        $data["storeId"]=$row[2];
                        $data["position"]=$row[3];
                        $data["address"]=$row[4];
                        $data["city"]=$row[5];
                        $data["country"]=$row[6];
                        $data["postcode"]=$row[7];
                        $data["region"]=$row[8];
                        $data["email"]=$row[9];
                        $data["phone"]=$row[10];
                        $data["link"]=$row[11];
                        $data["storeurl"]=$row[12];
                        $data["image"]=$row[13];
                        $data["icon"]=$row[14];
                        $data["latitude"]=$row[15];
                        $data["longitude"]=$row[16];
                        $data["sstatus"]=$row[17];
                        $data["updated_at"]=$row[18];
                        $data["created_at"]=$row[19];
                        $data["mon_open"]=$row[20];
                        $data["mon_otime"]=$row[21];
                        $data["mon_bstime"]=$row[22];
                        $data["mon_betime"]=$row[23];
                        $data["mon_ctime"]=$row[24];
                        $data["tue_open"]=$row[25];
                        $data["tue_otime"]=$row[26];
                        $data["tue_bstime"]=$row[27];
                        $data["tue_betime"]=$row[28];
                        $data["tue_ctime"]=$row[29];
                        $data["wed_open"]=$row[30];
                        $data["wed_otime"]=$row[31];
                        $data["wed_bstime"]=$row[32];
                        $data["wed_betime"]=$row[33];
                        $data["wed_ctime"]=$row[34];
                        $data["thu_open"]=$row[35];
                        $data["thu_otime"]=$row[36];
                        $data["thu_bstime"]=$row[37];
                        $data["thu_betime"]=$row[38];
                        $data["thu_ctime"]=$row[39];
                        $data["fri_open"]=$row[40];
                        $data["fri_otime"]=$row[41];
                        $data["fri_bstime"]=$row[42];
                        $data["fri_betime"]=$row[43];
                        $data["fri_ctime"]=$row[44];
                        $data["sat_open"]=$row[45];
                        $data["sat_otime"]=$row[46];
                        $data["sat_bstime"]=$row[47];
                        $data["sat_betime"]=$row[48];
                        $data["sat_ctime"]=$row[49];
                        $data["sun_open"]=$row[50];
                        $data["sun_otime"]=$row[51];
                        $data["sun_bstime"]=$row[52];
                        $data["sun_betime"]=$row[53];
                        $data["sun_ctime"]=$row[54];
                        if (isset($row[55])=="region_id") {
                            $data["region_id"]=$row[55];
                            $data["product_id"] = $row[56];
                        } else {
                            $data["product_id"] = $row[55];
                        }
                        $editModel=$storeModel;
                        $editModel->setData($data);

                        try {
                            //@codingStandardsIgnoreLine
                            $editModel->save();
                            $replaceWithId=$editModel->getId();
                            $productId=explode(",", $data["product_id"]);
                            $product=[];
                            $productModelSave=$this->productModel;
                            $objectManaget=\Magento\Framework\App\ObjectManager::getInstance();
                            $_resources = $objectManaget->get(\Magento\Framework\App\ResourceConnection::class);
                            $connection = $_resources->getConnection();
                            $table = $_resources->getTableName(
                                \Mageants\StoreLocator\Model\ResourceModel\ManageStore::TBL_ATT_PRODUCT
                            );
                            $insert=$productId;
                            if ($insert) {
                                $data = [];
                                foreach ($insert as $product_id) {
                                    $data[] = ['store_id' => (int)$replaceWithId, 'product_id' => (int)$product_id];
                                }
                                $connection->insertMultiple($table, $data);
                            }
                            $i++;
                        } catch (\Exception $ex) {
                            return $ex;
                        }
                    }
                }

                if ($skip==1) {
                    $replaceWithId='';
                    $model=$this->storeModel;
                    $storecollection=$this->storeModel->getCollection()
                        ->addFieldToSelect("*")
                        ->addFieldToFilter("store_id", $row[0]);
                    if (!empty($storecollection->getData())) {
                        $compareArrayFirst = $storecollection->getData();
                        foreach ($storecollection->getData() as $store) {
                            $replaceWithId=$store['store_id'];
                        }
                    } else {
                        $compareArrayFirst[] = '';
                        $incremental++;
                    }
                    $data=[];
                    if ($replaceWithId) {
                        $data = $storecollection->getData();
                        $data["store_id"]=$replaceWithId;
                    }
                    $data["sname"]=$row[1];
                    $data["storeId"]=$row[2];
                    $data["position"]=$row[3];
                    $data["address"]=$row[4];
                    $data["city"]=$row[5];
                    $data["country"]=$row[6];
                    $data["postcode"]=$row[7];
                    $data["region"]=$row[8];
                    $data["email"]=$row[9];
                    $data["phone"]=$row[10];
                    $data["link"]=$row[11];
                    $data["storeurl"]=$row[12];
                    $data["image"]=$row[13];
                    $data["icon"]=$row[14];
                    $data["latitude"]=$row[15];
                    $data["longitude"]=$row[16];
                    $data["sstatus"]=$row[17];
                    $data["updated_at"]=$row[18];
                    $data["created_at"]=$row[19];
                    $data["mon_open"]=$row[20];
                    $data["mon_otime"]=$row[21];
                    $data["mon_bstime"]=$row[22];
                    $data["mon_betime"]=$row[23];
                    $data["mon_ctime"]=$row[24];
                    $data["tue_open"]=$row[25];
                    $data["tue_otime"]=$row[26];
                    $data["tue_bstime"]=$row[27];
                    $data["tue_betime"]=$row[28];
                    $data["tue_ctime"]=$row[29];
                    $data["wed_open"]=$row[30];
                    $data["wed_otime"]=$row[31];
                    $data["wed_bstime"]=$row[32];
                    $data["wed_betime"]=$row[33];
                    $data["wed_ctime"]=$row[34];
                    $data["thu_open"]=$row[35];
                    $data["thu_otime"]=$row[36];
                    $data["thu_bstime"]=$row[37];
                    $data["thu_betime"]=$row[38];
                    $data["thu_ctime"]=$row[39];
                    $data["fri_open"]=$row[40];
                    $data["fri_otime"]=$row[41];
                    $data["fri_bstime"]=$row[42];
                    $data["fri_betime"]=$row[43];
                    $data["fri_ctime"]=$row[44];
                    $data["sat_open"]=$row[45];
                    $data["sat_otime"]=$row[46];
                    $data["sat_bstime"]=$row[47];
                    $data["sat_betime"]=$row[48];
                    $data["sat_ctime"]=$row[49];
                    $data["sun_open"]=$row[50];
                    $data["sun_otime"]=$row[51];
                    $data["sun_bstime"]=$row[52];
                    $data["sun_betime"]=$row[53];
                    $data["sun_ctime"]=$row[54];
                    if (isset($row[55])=="region_id") {
                        $data["region_id"]=$row[55];
                        $data["product_id"] = $row[56];
                    } else {
                        $data["product_id"] = $row[55];
                    }
                    try {
                        //@codingStandardsIgnoreLine
                        $model->setData($data);
                        $model->save();
                        $store_id=$model->getId();
                        $productId=explode(",", $data["product_id"]);
                        $product=[];
                        $productModelSave=$this->productModel;
                        $objectManaget=\Magento\Framework\App\ObjectManager::getInstance();
                        $_resources = $objectManaget->get(\Magento\Framework\App\ResourceConnection::class);
                        $connection = $_resources->getConnection();
                        $table = $_resources->getTableName(
                            \Mageants\StoreLocator\Model\ResourceModel\ManageStore::TBL_ATT_PRODUCT
                        );
                        $insert=$productId;
                        if ($insert) {
                            $data = [];
                            foreach ($insert as $product_id) {
                                $data[] = ['store_id' => (int)$replaceWithId, 'product_id' => (int)$product_id];
                            }
                            $connection->insertMultiple($table, $data);
                        }
                        $storeComparecollection=$this->storeModel->getCollection()
                        ->addFieldToSelect("*")
                        ->addFieldToFilter("store_id", $row[0]);
                        $compareArraySecond = $storeComparecollection->getData();
                        if (!empty($compareArrayFirst) && !empty($compareArraySecond)) {
                            $result=array_diff($compareArrayFirst[0], $compareArraySecond[0]);
                            if (!empty($result)) {
                                $storeIds[] = $compareArraySecond[0]['store_id'];
                                $lines[] = $row;
                                $i++;
                            }
                        }
                        unset($compareArrayFirst);
                        unset($compareArraySecond);
                    } catch (\Exception $ex) {
                        if ($replaceWithId) {
                            $lines[] = $row;
                        }
                    }
                } else {
                    if ($replaceWithId==0) {
                        $lines[] = $row;
                    }
                }

                $skip=0;
            }
        }

        $html="";
        //@codingStandardsIgnoreLine
        if (count($lines)>0) {
            $html="<table class='data-grid data-grid-draggable'><thead><tr>";
            $html.="<th style='padding:10px !important;'>Store Id</th>";
            $html.="<th>Title</th>";
            $html.="<th>Store Email</th></tr></thead><tbody>";

            foreach ($lines as $line) {
                $html.="<tr><td>".$line[0]."</td>";
                $html.="<td>".$line[1]."</td>";
                $html.="<td>".$line[9]."</td></tr>";
            }

            $html.="</tbody></table>";
        }
        if ($ifReplaceisOn == 1) {
            $message=($incremental)." Store Imported";
            if (!empty($storeIds)) {
                $message.= " And Records Updated Of ".implode(",", $storeIds)." Ids";
            } else {
                $message.= " And 0 Records Updated";
            }
        } else {
            $message=($i-1)." Store Imported";
        }
        $responce=["message"=>$message,"html"=>$html];
        //@codingStandardsIgnoreLine
        echo json_encode($responce);
        //@codingStandardsIgnoreLine
        return;
    }
}
