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
 * search for Store
 */
class Search extends \Magento\Framework\App\Action\Action
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
    public function __construct(
        PageFactory $resultPageFactory,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
    
    /**
     * perform execute method for search store
     *
     * @return $void
     */
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            $resultPage = $this->_resultPageFactory->create();
            $block = $resultPage->getLayout()
                    ->createBlock('Mageants\StoreLocator\Block\Map')
                    ->setTemplate('Mageants_StoreLocator::map_search.phtml')
                    ->toHtml();
            echo $block;
            return;
        }
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
