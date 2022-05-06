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
 * AddCodeSet class for add new Codeset
 */ 
class Addcodeset extends \Magento\Backend\App\Action
{
    /**
     * session Id for Order
     *
     * @var \Magento\Backend\Model\Session
     */
	protected $_sessionId;  

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) 
    {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
    }
	
    /**
     * Perform MassDele Action
     */
	public function execute()
    {
       $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $_sessionId=$objectManager->create("\Magento\Backend\Model\Session");    
       $codesetid = (int) $this->getRequest()->getParam('code_set_id');
        $_sessionId->setCodeId($codesetid);
        $codesetData = $this->_objectManager->create('Mageants\GiftCertificate\Model\Codeset');
        if ($codesetid) {
            $codesetData = $codesetData->load($codesetid);
		    $templateTitle = $codesetData->getQuestion();
            if (!$codesetData->getCodeSetId()) {
                $this->messageManager->addError(__('Codeset no longer exist.'));
                $this->_redirect('giftcertificate/index/');
                return;
            }
        }
        $this->_coreRegistry->register('code_set_data', $codesetData);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $title = $codesetid ? __('Edit Code Set').$templateTitle : __('Add Code Set');
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }

}