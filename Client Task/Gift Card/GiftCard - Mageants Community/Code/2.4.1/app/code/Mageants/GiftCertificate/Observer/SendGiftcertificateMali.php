<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCertificate\Observer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/*
 * RemoveBlock Observer before render block
 */
class SendGiftcertificateMali implements ObserverInterface
{   
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $registry;

    /**
     * account
     *
     * @var \Mageants\GiftCertificate\Model\Account
     */
    protected $_account;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface 
     */ 


    public function __construct(
        \Mageants\GiftCertificate\Model\Giftquote $giftquote,
        \Mageants\GiftCertificate\Model\Account $account,
        \Mageants\GiftCertificate\Helper\Data $helper,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Mageants\GiftCertificate\Model\Codeset $codeset,
        \Mageants\GiftCertificate\Model\Codelist $codelist,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Catalog\Api\ProductRepositoryInterface $productrepository
    ) {

        $this->_giftquote=$giftquote;
        $this->_account = $account;
        $this->_helper=$helper;
        $this->_storeManager = $storeManager;
        $this->cookieManager = $cookieManager;
        $this->_codeset=$codeset; 
        $this->_codelist=$codelist; 
        $this->registry = $registry;
        $this->_assetRepo = $assetRepo;
        $this->productrepository = $productrepository;
    }

    public function execute(Observer $observer)
    {   
    $order = $observer->getEvent()->getInvoice()->getOrder();
    $items = $order->getAllVisibleItems();
    $order_id = $order->getIncrementId();
    if ($order_id) {
        $gift_quotes= $this->_giftquote->getCollection()->addFieldToFilter('order_increment_id', $order_id);
    }  
    $quote_id = array();
    foreach ($items as $item) {
        if ($item->getProductType()=='giftcertificate'):
            foreach ($gift_quotes as $gift) {
                $validDate=null;
                $validity = $gift->getCodeValidity();
                if ($validity) {
                    if($validity!==''):
                        if($gift->getDateOfDelivery()==''):
                            $validDate = date('Y-m-d', strtotime($validity.' days'));
                        else:
                            $validDate = date('Y-m-d', strtotime($gift->getDateOfDelivery(). ' + '.$validity.' days'));
                        endif; 
                    endif;
                }
                if ($gift->getProductId()==$item->getProductId()):
                    $quote_id[]=$gift->getId();
                    $codesetModel=$this->_codeset->getCollection()->addFieldToFilter('code_title',$gift->getCodesetid());
                    foreach ($codesetModel as $codeset) {
                        $id=$codeset->getId();   
                    }
                    if ($id):   
                        $applicableCodes = '';
                        $allocatedCodes=$this->_account->getCollection()->addFieldToFilter('order_increment_id', $order_id)->getData();
                        foreach ($allocatedCodes as $code) {
                            $applicableCodes = $code['gift_code'];
                        }
                        $certificateCode=array();
                        if ($applicableCodes!=''): 
                            $certificateCode[]=$applicableCodes;

                            $emailTemplateVariables['bcc']='test@giftcertificate.com';
                            if($this->_helper->getBcc()!=''):
                                $emailTemplateVariables['bcc']=explode(",",$this->_helper->getBcc());
                            endif;
                            $gift_template = $gift->getTemplateId();
                            if($gift->getCustomUpload()){
                                $mediapath = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                                $gift_template = $mediapath."giftcertificate/".$gift->getTemplateId();
                            }
                            if ($gift_template=="") {
                                $productid = $item->getProductId();
                                $store = $this->_storeManager->getStore();
                                $product = $this->productrepository->getById($productid);
                                $mediaUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                                if($product->getImage()) {
                                    $gift_template = $mediaUrl . 'catalog/product' .$product->getImage();
                                } else {
                                    $gift_template =  $this->_assetRepo->getUrl('Magento_Catalog::images/product/placeholder/image.jpg');
                                }
                            }
                            if ($gift->getCardTypes()!=1):
                                $emailTemplateVariables['left'] = '0px';
                                $emailTemplateVariables['top'] = '96px';
                                $emailTemplateVariables['bgcolor'] = '';
                                $emailTemplateVariables['color'] = '#fff';

                                if ($gift->getSendtemplateId()) {
                                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
                                    $templateData = $objectManager->get('Mageants\GiftCertificate\Model\Templates')->load($gift->getSendtemplateId());
                                    if ($templateData) {
                                        if ($templateData->getPositionleft()) { 
                                            $emailTemplateVariables['left'] = $templateData->getPositionleft().'px';
                                        }
                                        if ($templateData->getPositiontop()) { 
                                            $emailTemplateVariables['top'] = $templateData->getPositiontop().'px';
                                        }
                                        if ($templateData->getColor()) { 
                                            $emailTemplateVariables['bgcolor'] = $templateData->getColor();
                                        }
                                        if ($templateData->getForecolor()) { 
                                            $emailTemplateVariables['color'] = $templateData->getForecolor();
                                        }
                                        if ($templateData->getMessage()) { 
                                            $emailTemplateVariables['message'] = $templateData->getMessage();
                                        }
                                    }
                                }  
                                $emailTemplateVariables['template'] = $gift_template;
                                if ($gift->getMessage()) {
                                    $emailTemplateVariables['message'] = $gift->getMessage();    
                                }
                                $emailTemplateVariables['current_balance'] = $gift->getGiftCardValue();
                                $emailTemplateVariables['sender_name'] = $gift->getSenderName();     
                                $emailTemplateVariables['sender_email'] = $gift->getSenderEmail();     
                                $emailTemplateVariables['recipient_name'] = $gift->getRecipientName();     
                                $emailTemplateVariables['recipient_email'] = $gift->getRecipientEmail();
                                $emailTemplateVariables['validity'] = 'Unlimited';
                                $catArray=array();
                                $catArray=explode(',',$gift->getCategories());
                                $categoryname="";
                                foreach ($catArray as $cat) {
                                    $categoryname .= $this->_helper->getCategoriesName($cat).",";
                                }
                                $emailTemplateVariables['category_name'] = $categoryname;

                                if($validDate!='0000-00-00'):
                                    $emailTemplateVariables['validity'] = $validDate;
                                endif;

                                $emailTemplateVariables['code'] = $applicableCodes;
                                
                                if($gift->getTimezone()==''):
                                     if($gift['date_of_delivery'] != ''):
                                        if(!empty($emailTemplateVariables) && !empty($emailTemplateVariables['recipient_email'])):
                                            try{
                                            
                                                $this->_helper->sendTemplate($emailTemplateVariables);
                                            }
                                            catch(Exception $ex){
                                                $this->_logger->addDebug($ex->getMessage());    
                                            }
                                        endif;
                                     endif;  
                                endif;  
                            endif;  
                        endif;  
                    endif;    
                endif;
                if(!empty($quote_id)):
                    foreach($quote_id as $id){
                        $quote=$this->_giftquote->load($id);
                       // $quote->delete();
                    }
                endif;
            }
        endif;
    } 
    }
}