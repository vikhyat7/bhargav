<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCertificate\Controller\Adminhtml\Gcimages;
use Magento\Backend\App\Action\Context;
/**
 * Delete Image Template
 */ 
class Delete extends \Magento\Backend\App\Action
{
	/**
     * @param \Magento\Backend\Block\Template\Context $context
     */
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }
   	
   	/**
   	 * Perform delete template action
   	 */
    public function execute()
	{
		if($this->getRequest()->getParam('image_id')!=''):
			$id = $this->getRequest()->getParam('image_id');
	        $resultRedirect = $this->resultRedirectFactory->create();
			$row = $this->_objectManager->get('Mageants\GiftCertificate\Model\Templates')->load($id);
			$row->delete();
			$this->messageManager->addSuccess(__('Template has been deleted.'));
			$this->_redirect('giftcertificate/gcimages/');
		endif;
		
	}
}

