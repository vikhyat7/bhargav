<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\GiftCertificate\Controller\Adminhtml\Gcaccount;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;

class Save extends Action
{
    
    /**
     * FileId 
     *
     * @var String
     */
    protected $fileId = 'image';
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     */
    public function __construct(
        Action\Context $context
    ){
        parent::__construct($context);
    }

    /*
     * Perform Save action for Account
     */
    public function execute()
    {
       $data=$this->getRequest()->getPostValue();

       $urlkey=$this->getRequest()->getParam('back');
       try{
           $templateData=$this->_objectManager->create('Mageants\GiftCertificate\Model\Account');    
              if (isset($data['account_id'])) {
                  
                    $exist_order=$templateData->load($data['account_id']);
                    $customerid=$exist_order->getOrderId();
                    $customerdata=$this->_objectManager->create('Mageants\GiftCertificate\Model\Customer');    
                    $customerdata->setData($data);
                    $customerdata->setCustomerId($customerid);
                    $customerdata->save();
                }
                
                $templateData->setData($data);
                $templateData->setAccountId($data['account_id']);
                $templateData->save();
                $this->messageManager->addSuccess(__('Account data has been successfully saved.'));
       }
       catch(Exception $e){
           $this->messageManager->addError(__($e->getMessage()));
       }
       if($urlkey=='edit')
            {
                    $emailTemplateVariables = array();
                      $exist_order=$templateData->load($data['account_id']);
                    $customerid=$exist_order->getOrderId();
                  if(isset($exist_order)):
                    $customerdata=$this->_objectManager->create('Mageants\GiftCertificate\Model\Customer')->load($exist_order->getOrderId());
                   $emailTemplateVariables['template'] = $exist_order->getTemplate();
                   if($exist_order->getCustomUpload()){
                    $mediapath =  $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
                    ->getStore()
                    ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                    $emailTemplateVariables['template'] = $mediapath."giftcertificate/".$exist_order->getTemplate();
                   }
                   


                   $emailTemplateVariables['left'] = '0px';
                   $emailTemplateVariables['top'] = '96px';
                   $emailTemplateVariables['bgcolor'] = '#f00';
                   $emailTemplateVariables['color'] = '#fff';
                   $emailTemplateVariables['message'] = $exist_order->getMessage();
                   if($exist_order->getComment()){
                    $emailTemplateVariables['message'] = $exist_order->getComment(); 
                   }
                   if($exist_order->getSendtemplateId()){
                      $templateData = $this->_objectManager->get('Mageants\GiftCertificate\Model\Templates')->load($exist_order->getSendtemplateId());
                      if($templateData->getPositionleft()){ 
                        $emailTemplateVariables['left'] = $templateData->getPositionleft().'px';

                      }
                       if($templateData->getPositiontop()){ 
                        $emailTemplateVariables['top'] = $templateData->getPositiontop().'px';

                      }
                      if($templateData->getColor()){ 
                        $emailTemplateVariables['bgcolor'] = $templateData->getColor() ;

                      }
                      if($templateData->getForecolor()){ 
                        $emailTemplateVariables['color'] = $templateData->getForecolor() ;

                      }
                     
                   }

                   $_categoryFactory=$this->_objectManager->create('\Magento\Catalog\Model\CategoryFactory');
                   $cats = explode(',', $exist_order->getCategories());
                        $categoryName='';
                        foreach ($cats as $cat) {
                            $_category = $_categoryFactory->create()->load($cat);    
                            
                            $categoryName.=$_category->getName().",";
                        }
                   $emailTemplateVariables['code'] = $exist_order->getGiftCode();
                   $emailTemplateVariables['current_balance'] = $exist_order->getCurrentBalance();
                   $emailTemplateVariables['sender_name'] = $customerdata->getSenderName();     
                   $emailTemplateVariables['sender_email'] = $customerdata->getSenderEmail();     
                   $emailTemplateVariables['recipient_name'] = $customerdata->getRecipientName();     
                   $emailTemplateVariables['category_name']=$categoryName;
                   $emailTemplateVariables['recipient_email'] = $customerdata->getRecipientEmail();     
                   $emailTemplateVariables['validity'] = $exist_order->getExpireAt();     
                  endif;
                  
                  if(!empty($emailTemplateVariables) && !empty($emailTemplateVariables['recipient_email'])):
                   $this->_objectManager->get('Mageants\GiftCertificate\Helper\Data')->sendTemplate(
                          $emailTemplateVariables
                      );
                    endif;  

                    $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                    $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                    return $resultRedirect;
            }
       
     $this->_redirect('giftcertificate/gcaccount/index');
    }
}