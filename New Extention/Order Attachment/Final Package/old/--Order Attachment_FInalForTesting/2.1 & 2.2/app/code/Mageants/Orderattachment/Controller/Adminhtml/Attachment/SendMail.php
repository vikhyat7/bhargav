<?php
/**
 * @category Mageants_Orderattachment
 * @package Mageants_Orderattachment
 * @copyright Copyright (c) 2022 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\Orderattachment\Controller\Adminhtml\Attachment;

use Magento\Sales\Api\OrderManagementInterface;
use Mageants\Orderattachment\Helper\Data;
use Mageants\Orderattachment\Helper\Email;
use Magento\Framework\App\Action\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class SendMail extends \Magento\Backend\App\Action
{
    protected $helperData;
    protected $helperEmail;
    protected $subject;
    protected $orderResource;
    protected $orderFactory;
    protected $orderRepository;
    protected $storeManager;

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
     * @param Context $context
     * @return OrderInterface
     */
    public function __construct(
        Data $helperData,
        Email $helperEmail,
        Context $context,
        OrderManagementInterface $subject,
        \Magento\Framework\Registry $registry,
        \Mageants\Orderattachment\Model\AttachmentFactory $attachmentFactory,
        \Magento\Sales\Model\Spi\OrderResourceInterface $orderResource,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Api\Data\OrderInterfaceFactory $orderFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Mageants\Orderattachment\Helper\Attachment $attachmentHelper
        // array $data = []
    ) {
        parent::__construct($context);
        $this->helperData = $helperData;
        $this->helperEmail = $helperEmail;
        $this->attachmentFactory = $attachmentFactory;
        $this->subject = $subject;
        $this->coreRegistry = $registry;
        $this->orderResource = $orderResource;
        $this->orderRepository = $orderRepository;
        $this->orderFactory = $orderFactory;
        $this->attachmentHelper = $attachmentHelper;
        $this->storeManager = $storeManager;
    }
    public function execute()
    {
        $orderId = (int)$this->getRequest()->getParam('order_id');
        $order = $this->orderRepository->get($orderId);
        $orderIncrementId = $order->getIncrementId();
        $name = $order->getCustomerFirstname(). " " .$order->getCustomerLastname();
        $emailVar = $order->getCustomerEmail();
    
        $EmailAttachmentData = $this->getOrderAttachments();
        
        if (count($EmailAttachmentData) > 0) {
        
            $this->helperEmail->sendEmail($orderIncrementId, $name, $emailVar, $EmailAttachmentData);
        
            $this->messageManager->addSuccess(__('Send Attachments Email For Order #'.$orderIncrementId));

        } else {
             $this->messageManager->addError(__('There Is No Attachments For Order #'.$orderIncrementId));
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $order_url = $this->getUrl("sales/order/view/order_id/".$orderId);
        $resultRedirect->setUrl($order_url);
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
