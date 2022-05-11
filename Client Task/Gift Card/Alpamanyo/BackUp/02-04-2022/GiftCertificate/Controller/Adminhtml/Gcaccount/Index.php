<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCertificate\Controller\Adminhtml\Gcaccount;
use Magento\Framework\Controller\ResultFactory;
/*
 * Account Index Controller
 */
class Index extends \Mageants\GiftCertificate\Controller\Adminhtml\Index
{
	/*
	 * Perform Index controller execute Action
	 */
	public function execute()
	{
		$resultPage = $this->resultPageFactory->create();
		$resultPage->setActiveMenu('Mageants_GiftCertificate::giftcertificate');
		$resultPage->addBreadcrumb(__('Manage Account'), __('Manage Account'));
		$resultPage->addBreadcrumb(__('Manage Account'), __('Manage Account'));
		$resultPage->getConfig()->getTitle()->prepend(__('Manage Account'));
		return $resultPage;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mageants_GiftCertificate::GiftCertificateAccount');
    }

}