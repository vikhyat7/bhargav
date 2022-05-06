<?php
/**
 * @category Mageants_Orderattachment
 * @package Mageants_Orderattachment
 * @copyright Copyright (c) 2022 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\Orderattachment\Block\Account\Order;

class Attachments extends \Magento\Framework\View\Element\Template
{
    protected $_template = 'account/order/attachments.phtml';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Mageants\Orderattachment\Helper\Attachment
     */
    protected $attachmentHelper;

    /**
     * @var \Mageants\Orderattachment\Helper\Data
     */
    protected $dataHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Mageants\Orderattachment\Helper\Attachment $attachmentHelper
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Mageants\Orderattachment\Helper\Attachment $attachmentHelper,
        \Mageants\Orderattachment\Helper\Data $dataHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->coreRegistry = $registry;
        $this->attachmentHelper = $attachmentHelper;
        $this->dataHelper = $dataHelper;
    }

    public function getOrder()
    {
        return $this->coreRegistry->registry('current_order');
    }

    public function isOrderAttachmentEnabled()
    {
        return $this->dataHelper->isOrderAttachmentEnabled();
    }

    public function getAttachmentConfig()
    {
        $config = $this->dataHelper->getAttachmentConfig($this);

        return $config;
    }

    public function getOrderAttachments()
    {
        $orderId = $this->getOrder()->getId();

        return $this->attachmentHelper->getOrderAttachments($orderId);
    }
    public function getOrderAttachments1($orderId)
    {
        // $orderId = $this->getOrder()->getId($orderId);

        return $this->attachmentHelper->getOrderAttachments($orderId);
    }

    public function getUploadUrl()
    {
        return $this->getUrl(
            'orderattachment/attachment/upload',
            ['order_id' => $this->getOrder()->getId()]
        );
    }
    public function getEmailUrl()
    {
        return $this->getUrl(
            'orderattachment/attachment/sendmail',
            ['order_id' => $this->getOrder()->getId()]
        );
    }

    public function getUpdateUrl()
    {
        return $this->getUrl(
            'orderattachment/attachment/update',
            ['order_id' => $this->getOrder()->getId()]
        );
    }

    public function getRemoveUrl()
    {
        return $this->getUrl(
            'orderattachment/attachment/delete',
            ['order_id' => $this->getOrder()->getId()]
        );
    }
}
