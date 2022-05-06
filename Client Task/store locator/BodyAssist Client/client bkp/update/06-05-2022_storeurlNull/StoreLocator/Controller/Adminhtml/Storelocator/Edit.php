<?php
/**
 * @category Mageants StoreLocator
 * @package Mageants_StoreLocator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@Mageants.com>
 */
namespace Mageants\StoreLocator\Controller\Adminhtml\Storelocator;

use Magento\Backend\App\Action;
use Magento\Backend\Model\Session;

/**
 * store edit Action
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Mageants\StoreLocator\Model\ManageStore $managestore
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->managestore = $managestore;
        $this->session = $context->getSession();
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function _isAllowed()
    {
        return true;
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Mageants_StoreLocator::storelocator_content')
            ->addBreadcrumb(__('Lists'), __('Lists'))
            ->addBreadcrumb(__('Manage Info'), __('Manage Info'));
        return $resultPage;
    }

    /**
     * Edit Store
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('store_id');
        $model = $this->managestore;

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This info no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        // 3. Set entered data if was error when we do save
        $data = $this->session->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        $this->_coreRegistry->register('locator_data', $model);

        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Store') : __('New Store'),
            $id ? __('Edit Store') : __('New Store')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Store'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? __('Edit Store') : __('New Store'));
        return $resultPage;
    }
}
