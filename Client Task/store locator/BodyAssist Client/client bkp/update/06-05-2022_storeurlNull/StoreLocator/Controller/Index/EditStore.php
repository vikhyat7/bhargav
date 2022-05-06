<?php

namespace Mageants\StoreLocator\Controller\Index;

class EditStore extends \Magento\Framework\App\Action\Action
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
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Mageants\StoreLocator\Helper\Data $dataHelper,
        \Magento\Customer\Model\SessionFactory $customerSession
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->url=$context->getUrl();
        $this->responseFactory=$responseFactory;
        $this->customerSession=$customerSession;
        $this->dataHelper = $dataHelper;
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
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
                $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Edit Store'));
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
