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
 * Add Image Template
 */ 
class Addtemplate extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
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
     * Perform AddTemplate controller Action
     */
    public function execute()
    {
        $imageid = (int) $this->getRequest()->getParam('image_id');
        $templateData = $this->_objectManager->create('Mageants\GiftCertificate\Model\Templates');
        if ($imageid) {
            $templateData = $templateData->load($imageid);
            $temptitle = $templateData->getImageTitle();
            if (!$templateData->getImageId()) {
                $this->messageManager->addError(__('Template no longer exist.'));
                $this->_redirect('giftcertificate/gcimages/index');
                return;
            }
        }
        $this->_coreRegistry->register('template_data', $templateData);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $title = $imageid ? __('Edit Template ').$temptitle : __('Add Template');
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }
}