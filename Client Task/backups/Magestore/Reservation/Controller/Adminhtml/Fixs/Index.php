<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @developer   Louis Coding
 * @category    Magestore
 * @package     Magestore_Reservation
 * @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */
namespace Magestore\Reservation\Controller\Adminhtml\Fixs;

/**
 * Class Index
 *
 * @package Magestore\Reservation\Controller\Adminhtml\Fixs
 * @SuppressWarnings(PHPMD.AllPurposeAction)
 */
class Index extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magestore_AdjustStock::reservation';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory;

    /**
     * @var \Magestore\Reservation\Model\ResourceModel\ServiceReservations
     */
    public $serviceReservations;

    /**
     * Index constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magestore\Reservation\Model\ResourceModel\ServiceReservations $serviceReservations
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magestore\Reservation\Model\ResourceModel\ServiceReservations $serviceReservations
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->serviceReservations = $serviceReservations;
    }

    /**
     * Fix Reservation
     *
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $messageManager = $this->serviceReservations->execute();
        $this->messageManager->addSuccess($messageManager);
        return $resultRedirect->setPath('catalog/product/index');
    }
}
