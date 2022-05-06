<?php
/**
 * @category Mageants_Orderattachment
 * @package Mageants_Orderattachment
 * @copyright Copyright (c) 2022 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\Orderattachment\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Mageants\Orderattachment\Model\Attachment;
use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;
    const XML_PATH_EMAIL_SENDER = 'orderattachments/demo/template';
    // const XML_PATH_EMAIL_TEMPLATE = 'orderattachments/demo/request_email_tepmlate';
    
    /**
     * @var \Mageants\Orderattachment\Model\ResourceModel\Attachment\Collection
     */
    protected $attachmentCollection;
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    
    public $scopeConfig;
    
    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Mageants\Orderattachment\Model\ResourceModel\Attachment\Collection $attachmentCollection
    ) {
        parent::__construct($context);
        $this->jsonEncoder = $jsonEncoder;
        $this->scopeConfig = $scopeConfig;
        $this->attachmentCollection = $attachmentCollection;
    }

    /**
     * Get title
     * @return boolean
     */
    public function getTitle()
    {
        $titleValue = $this->scopeConfig->getValue(
            \Mageants\Orderattachment\Model\Attachment::XML_PATH_ATTACHMENT_ON_ATTACHMENT_TITLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
     
        return (trim($titleValue))?$titleValue:\Mageants\Orderattachment\Model\Attachment::DEFAULT_TITLE_ATTACHMENT;
    }
    
    /**
     * Get config for order attachments enabled
     * @return boolean
     */
    public function isOrderAttachmentEnabled()
    {
        return (bool)$this->scopeConfig->getValue(
            \Mageants\Orderattachment\Model\Attachment::XML_PATH_ENABLE_ATTACHMENT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get config for Email sender
     */
    public function emailSender()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, $storeScope);
    }

    /**
     * Get config for Email Template
     */
    public function emailTemplate()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(
            \Mageants\Orderattachment\Model\Attachment::XML_PATH_EMAIL_TEMPLATE,
            $storeScope
        );
    }

    /**
     * Get attachment config json
     * @param mixed $block
     * @return string
     */
    public function getAttachmentConfig($block)
    {
        $attachments = $this->attachmentCollection;
        $attachSize = $this->scopeConfig->getValue(
            \Mageants\Orderattachment\Model\Attachment::XML_PATH_ATTACHMENT_FILE_SIZE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if ($block->getOrder()->getId()) {
            $attachments->addFieldToFilter('quote_id', ['is' => new \Zend_Db_Expr('null')]);
            $attachments->addFieldToFilter('order_id', $block->getOrder()->getId());
        }
        $attachSizeinMB = $attachSize / 1024 ;
        $config = [
            'attachments' => $block->getOrderAttachments(),
            'AttachmentLimit' => $this->scopeConfig->getValue(
                \Mageants\Orderattachment\Model\Attachment::XML_PATH_ATTACHMENT_FILE_LIMIT,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ),
            'AttachmentSize' => $attachSize,
            'AttachmentExt' => $this->scopeConfig->getValue(
                \Mageants\Orderattachment\Model\Attachment::XML_PATH_ATTACHMENT_FILE_EXT,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ),
            'AttachmentUpload' => $block->getUploadUrl(),
            'AttachmentUpdate' => $block->getUpdateUrl(),
            'AttachmentRemove' => $block->getRemoveUrl(),
            'AttachmentTitle' =>  $this->getTitle(),
            'AttachmentInfromation' => $this->scopeConfig->getValue(Attachment::XML_PATH_ATTACHMENT_ON_ATTACHMENT_INFORMATION, ScopeInterface::SCOPE_STORE),
            'removeItem' => __('Remove Item'),
            'AttachmentInvalidExt' => __('Invalid File Type'),
            'AttachmentComment' => __('Write comment here'),
            'AttachmentInvalidSize' => __('Size of the file is greater than ') . '('
                . $attachSizeinMB . ' MB)',
            'AttachmentInvalidLimit' => __('You have reached the limit of files'),
            'attachment_class' => 'attachment-id',
            'hash_class' => 'attachment-hash',
            'totalCount' => $attachments->getSize()
        ];

        return $this->jsonEncoder->encode($config);
    }
}
