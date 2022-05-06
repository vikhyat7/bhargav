<?php

namespace Mageants\StoreLocator\Controller\Index;

class DeleteStore extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Mageants\StoreLocator\Model\ManageStore $managestore,
        \Mageants\StoreLocator\Helper\Data $dataHelper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->url=$context->getUrl();
        $this->responseFactory=$responseFactory;
        $this->customerSession=$customerSession;
        $this->managestore = $managestore;
        $this->dataHelper = $dataHelper;
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
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $store_id = $this->getRequest()->getParam("store_id");
            $model=$this->managestore;
            $model->load($store_id);
            $model->delete();
            $this->messageManager->addSuccess(__('You Delete this Store.'));
            $this->dataHelper->cachePrograme();
            return $resultRedirect->setPath('storelocator/index/dealer');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\RuntimeException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __($e->getMessage()));
        }
        $this->dataHelper->cachePrograme();
        return $resultRedirect->setPath('storelocator/index/dealer', ['store_id' => $this->getRequest()->getParam('store_id')]);
    }
}
