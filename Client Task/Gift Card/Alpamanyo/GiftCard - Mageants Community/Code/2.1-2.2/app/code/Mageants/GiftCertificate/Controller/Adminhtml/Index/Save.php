<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\GiftCertificate\Controller\Adminhtml\Index;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
/**
 * Perform save action for codeList
 */
class Save extends Action
{
    /**
     * Execuite save action for codeList
     */
    public function execute()
    {
       $data=$this->getRequest()->getPostValue();
       $urlkey=$this->getRequest()->getParam('back');
       $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
       $connection = $resource->getConnection();
       $tableName = $resource->getTableName('gift_code_list');
       $qty=1;
       $modelData=array();
       $codelistData=$this->_objectManager->create('Mageants\GiftCertificate\Model\Codelist');
       $codesetData=$this->_objectManager->create('Mageants\GiftCertificate\Model\Codeset');
       $newcount=$data['code_qty'];
        if(isset($data['code_set_id'])){
           $codecount=$codelistData->getCollection()->addFieldToFilter('code_set_id',$data['code_set_id']);
        
        if($codecount->count()>0)
        {
          if($codecount->count() > $data['code_qty']){
            $newcount=-1;
            $this->messageManager->addNotice(__('Qty cannot be less than current quantity'));
             if($urlkey=='edit')
            {
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                return $resultRedirect->setPath('*/*/addcodeset', ['code_set_id' => $data['code_set_id'], '_current' => true]);
            }
            return $this->_redirect('giftcertificate/index/index');
            
          }
          else
          {
           $newcount=$data['code_qty']-$codecount->count(); 
          }
        }
        }
       $data['unused_code']=$data['code_qty'];
        try{
          $codesetData->setData($data);
            if (isset($data['code_set_id'])) {
                    $codesetData->setCodeSetId($data['code_set_id']);

                }

          $code_set_id=$codesetData->save()->getId();
        }
        catch(Exception $e){
           $this->messageManager->addError(__($e->getMessage()));              
        }
        $modelData['code_set_id']=$code_set_id;
       
        while($qty<=$newcount){
         preg_match_ALL('#\{(.*?)\}#', trim($data['code_pattern']) , $match);
         $pattern=call_user_func_array('array_merge', $match);
         $count=0;
         $str='';
         for($i=0; $i<sizeof($pattern); $i++)
         {
             if($pattern[$i]=='L'):
                $pattern[$i]=chr(64+rand(0,26));  
                $str.=$pattern[$i];
             endif;
             if($pattern[$i]=='D'):
                $pattern[$i]=mt_rand(0,9);
                $str.=$pattern[$i];
             endif;
         }
         $codepattern=preg_replace("/\{[^)]+\}/",$str,trim($data['code_pattern']));
          try{


              $modelData['used']=0;
              
                  $modelData['code']=$codepattern;
                  $codelistData->setData($modelData);
                  $codelistData->save(); 
               
            }
            catch(Exception $e){
                    $this->messageManager->addError(__($e->getMessage()));              
            }  
         $qty++;
       }  
       $this->messageManager->addSuccess(__('Codelist has been successfully saved.'));
       if($urlkey=='edit')
            {
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                return $resultRedirect->setPath('*/*/addcodeset', ['code_set_id' => $code_set_id, '_current' => true]);
            }
      $this->_redirect('giftcertificate/index/index');
    }
}