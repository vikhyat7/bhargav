<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCertificate\Controller\Adminhtml\Index;
use Magento\Framework\Controller\ResultFactory;
/**
 * Index Controller for CodeList
 */
class Index extends \Mageants\GiftCertificate\Controller\Adminhtml\Index
{
	/**
     * Perform Index Action
     */
	public function execute()
	{
		$resultPage = $this->resultPageFactory->create();
		$resultPage->setActiveMenu('Mageants_GiftCertificate::giftcertificate');
        $resultPage->addBreadcrumb(__('Code List'), __('Code List'));
        $resultPage->addBreadcrumb(__('Code List'), __('Code List'));
        $resultPage->getConfig()->getTitle()->prepend(__('Code List'));
		return $resultPage;
	}
}