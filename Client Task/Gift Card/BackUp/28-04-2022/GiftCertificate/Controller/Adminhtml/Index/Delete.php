<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCertificate\Controller\Adminhtml\Index;
 
use Magento\Backend\App\Action\Context;

class Delete extends \Magento\Backend\App\Action
{
	/**
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    /**
     * Perform Delete Action
     */
    public function execute()
	{
		if($this->getRequest()->getParam('id')!=''):
			$id = $this->getRequest()->getParam('id');
	        $resultRedirect = $this->resultRedirectFactory->create();
			$row = $this->_objectManager->get('Mageants\GiftCertificate\Model\Codelist')->load($id);
			$codesetid=$row->getCodeSetId();
			$codesetCollection = $this->_objectManager->get('Mageants\GiftCertificate\Model\Codeset')->load($codesetid);
			$qty=$codesetCollection->getCodeQty();
			$unusedcode=$codesetCollection->getUnusedCode();
			$row->delete();
			$qty=$qty-1;
			$unusedcode=$unusedcode-1;

			$data['code_qty']=$qty;
			$data['unused_code']=$unusedcode;
			$codesetData=$this->_objectManager->create('Mageants\GiftCertificate\Model\Codeset');
			 try{
         		 $codesetData->setData($data);
           		    $codesetData->setCodeSetId($codesetid);
           		    $codesetData->save();

                }
			    catch(Exception $e){
           			$this->messageManager->addError(__($e->getMessage()));              
       			 }
			$this->messageManager->addSuccess(__('Code has been deleted '));
			$this->_redirect('giftcertificate/index/index');
		endif;
		if($this->getRequest()->getParam('code_set_id')!=''):
			$row = $this->_objectManager->get('Mageants\GiftCertificate\Model\Codeset')->load($this->getRequest()->getParam('code_set_id'));
			$row->delete();
			$codelist=$this->_objectManager->get('Mageants\GiftCertificate\Model\Codelist')->getCollection();
			$codelist->addFieldToFilter('code_set_id',$this->getRequest()->getParam('code_set_id'));
			foreach($codelist as $list){
				$list->delete();
			}
		$this->messageManager->addSuccess(__('record has been deleted.'));
		endif;
			$this->_redirect($this->_redirect->getRefererUrl());
	}
}

