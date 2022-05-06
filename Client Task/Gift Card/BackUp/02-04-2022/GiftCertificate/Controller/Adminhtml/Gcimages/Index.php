<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\GiftCertificate\Controller\Adminhtml\Gcimages;
use Magento\Framework\Controller\ResultFactory;
/**
 * Image Template index adtion
 */ 
class Index extends \Mageants\GiftCertificate\Controller\Adminhtml\Index
{
	/**
   	 * Perform Index Action for template
   	 */
	public function execute()
	{
		$resultPage = $this->resultPageFactory->create();
		$resultPage->setActiveMenu('Mageants_GiftCertificate::giftcertificate');
        $resultPage->addBreadcrumb(__('Manage Template'), __('Manage Template'));
        $resultPage->addBreadcrumb(__('Manage Template'), __('Manage Template'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Template'));
		return $resultPage;	
	}

	/**
	 * {@inheritdoc}
	 */
	protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mageants_GiftCertificate::GiftCertificateImage');
    }
}