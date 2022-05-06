<?php

namespace Mageants\StoreLocator\Controller\Index;

class Dealer extends \Magento\Framework\App\Action\Action
{
    /**
     * result page Factory
     *
     * @var Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Mageants\RMA\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @var \Magento\Framework\App\ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    protected $resultRedirectFactory;
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Mageants\RMA\Helper\Data $helper
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Framework\App\ResponseFactory $responseFactory
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Mageants\StoreLocator\Helper\Data $dataHelper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\ResponseFactory $responseFactory
    ) {
        $this->responseFactory=$responseFactory;
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->resultPageFactory = $resultPageFactory;
        $this->dataHelper = $dataHelper;
        $this->url=$context->getUrl();
        $this->customerSession=$customerSession;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $customerId=$this->customerSession->create()->getCustomer()->getId();
        if (!$customerId) {
            $RedirectUrl= $this->url->getUrl('customer/account/login');
            $this->responseFactory->create()->setRedirect($RedirectUrl)->sendResponse();
            exit();
        } else {
            if ($this->dataHelper->getEnableStoreLocator() == 1) {
                $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Manage Dealer and Stores'));
                $this->dataHelper->cachePrograme();
                return $this->resultPageFactory->create();
            } else {
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('customer/account');
                return $resultRedirect;
            }
        }
    }
}
