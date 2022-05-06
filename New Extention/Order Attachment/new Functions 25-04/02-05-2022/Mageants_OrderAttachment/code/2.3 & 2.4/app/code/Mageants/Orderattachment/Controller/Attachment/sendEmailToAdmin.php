<?php
/**
 * @category Mageants_Orderattachment
 * @package Mageants_Orderattachment
 * @copyright Copyright (c) 2022 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\Orderattachment\Controller\Attachment;

use Magento\Sales\Api\OrderManagementInterface;
use Mageants\Orderattachment\Helper\Data;
use Mageants\Orderattachment\Helper\FrontEndEmail;
use Magento\Framework\App\Action\Action;
// use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class sendEmailToAdmin extends \Magento\Framework\App\Action\Action
{
    protected $helperData;
    protected $helperEmail;
    protected $subject;
    protected $orderResource;
    protected $orderFactory;
    protected $orderRepository;
    protected $storeManager;
    protected $resultFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;
    /**
     * @var \Mageants\Orderattachment\Helper\Attachment
     */
    protected $attachmentHelper;
    /**
     * @var \Mageants\Orderattachment\Model\AttachmentFactory
     */
    protected $attachmentFactory;

    /**
     * @param \Mageants\Orderattachment\Helper\Attachment $attachmentHelper
     * @param \Mageants\Orderattachment\Helper\Data $helperData
     * @param OrderManagementInterface $subject
     * @param OrderInterface $order
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $registry
     * @param \Mageants\Orderattachment\Model\AttachmentFactory $attachmentFactory
     * @return OrderInterface
     */
    public function __construct(
        Data $helperData,
        \Magento\Framework\UrlInterface $url,
        ResultFactory $resultFactory,
        FrontEndEmail $helperEmail,
        OrderManagementInterface $subject,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Mageants\Orderattachment\Model\AttachmentFactory $attachmentFactory,
        \Magento\Sales\Model\Spi\OrderResourceInterface $orderResource,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Api\Data\OrderInterfaceFactory $orderFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Mageants\Orderattachment\Helper\Attachment $attachmentHelper
        // array $data = []
    ) {
        parent::__construct($context);
        $this->helperData = $helperData;
        $this->helperEmail = $helperEmail;
        $this->resultFactory = $resultFactory;
        $this->url = $url;
        $this->attachmentFactory = $attachmentFactory;
        $this->subject = $subject;
        $this->coreRegistry = $registry;
        $this->orderResource = $orderResource;
        $this->orderRepository = $orderRepository;
        $this->messageManager = $messageManager;
        $this->orderFactory = $orderFactory;
        $this->attachmentHelper = $attachmentHelper;
        $this->storeManager = $storeManager;
    }
    public function execute()
    {

       
        $orderId = (int)$this->getRequest()->getParam('order_id');
        // $orderId = 93;
        $order = $this->orderRepository->get($orderId);
        $orderIncrementId = $order->getIncrementId();
        $name = $order->getCustomerFirstname(). " " .$order->getCustomerLastname();
        // $emailVar = $order->getCustomerEmail();
        $emailVar = $this->helperData->adminEmial();
    
        $EmailAttachmentData = $this->getOrderAttachments();

        if (count($EmailAttachmentData) > 0) {
        
            $this->helperEmail->sendEmailToAdmin($orderIncrementId, $name, $emailVar, $EmailAttachmentData);
        
            // $this->messageManager->addSuccess(__('Send Attachments Email For Order #'.$orderIncrementId));

        }
         // else {
        //      // $this->messageManager->addError(__('There Is No Attachments For Order #'.$orderIncrementId));
        // }

        

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $order_url = $this->url->getUrl("orderattachment/view/attachment/order_id/".$orderId);
        // echo ('$order_url');
        // echo ($order_url);
        $resultRedirect->setUrl($order_url);
        // $resultRedirect->setUrl('http://127.0.0.1/mage237p2/orderattachment/view/attachment/order_id/94/');
        return $resultRedirect;
    }

    public function getOrder()
    {
        $OiD = (int)$this->getRequest()->getParam('order_id');
        return $OiD;
    }

    public function getOrderAttachments()
    {
        $orderIds = $this->getOrder();

        $AttachmentData = $this->attachmentHelper->getOrderAttachments($orderIds);
        $ImageData = [];
        $result = [];

        foreach ($AttachmentData as $Data) {
            $ImageData[] = $Data['path'];
        }
        $ImageData1 = $ImageData;
        foreach ($ImageData1 as $path1) {

            $result[]= $path1;
        }
        
        return $result;
    }
}
