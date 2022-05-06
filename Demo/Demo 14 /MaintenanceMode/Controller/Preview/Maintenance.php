<?php
/**
 * @category Mageants MaintenanceMode
 * @package Mageants_MaintenanceMode
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\MaintenanceMode\Controller\Preview;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Maintenance
 *
 * @package Mageants\MaintenanceMode\Controller\Preview
 */
class Maintenance extends Action
{
    /**
     * @var PageFactory
     */
    protected $_pageFactory;

    /**
     * Maintenance constructor.
     *
     * @param Context $context
     * @param PageFactory $pageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory
    ) {
        $this->_pageFactory = $pageFactory;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        $resultPageFactory = $this->_pageFactory->create();
        $resultPageFactory->getConfig()->getTitle()->set(__('Maintenance Preview'));

        return $resultPageFactory;
    }
}
