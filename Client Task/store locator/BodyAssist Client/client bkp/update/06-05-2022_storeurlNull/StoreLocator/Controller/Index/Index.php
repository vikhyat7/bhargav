<?php
/**
 * @category Mageants StoreLocator
 * @package Mageants_StoreLocator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@Mageants.com>
 */
namespace Mageants\StoreLocator\Controller\Index;

use Magento\Framework\View\Result\PageFactory;

/**
 * Index for Store frontend
 */
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * Result PageFactory
     *
     * @var Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory;
    
    /**
     * @param \Magento\Backend\Block\Template\Context
     * @param Magento\Framework\View\Result\PageFactory
     */
    protected $resultRedirectFactory;
    public function __construct(
        PageFactory $resultPageFactory,
        \Magento\Framework\App\Action\Context $context,
        \Mageants\StoreLocator\Helper\Data $dataHelper
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->dataHelper = $dataHelper;
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        parent::__construct($context);
    }
    
    /**
     * perform execute method for Index
     *
     * @return $void
     */
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            $resultPage = $this->_resultPageFactory->create();
            $block = $resultPage->getLayout()
                    ->createBlock('Mageants\StoreLocator\Block\Map')
                    ->setTemplate('Mageants_StoreLocator::map.phtml')
                    ->toHtml();
            echo $block;
            return;
        } else {
            if ($this->dataHelper->getEnableStoreLocator() == 1) {
                $this->_view->loadLayout();
                $this->_view->renderLayout();
            } else {
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('customer/account');
                return $resultRedirect;
            }
        }
    }
}
