<?php
/**
 * @category Mageants_Orderattachment
 * @package Mageants_Orderattachment
 * @copyright Copyright (c) 2022 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\Orderattachment\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\UrlInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Mageants\Orderattachment\Model\Attachment;

class AttachmentConfigProvider implements ConfigProviderInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Mageants\Orderattachment\Model\ResourceModel\Attachment\Collection
     */
    protected $attachmentCollection;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Mageants\Orderattachment\Helper\Data
     */
    protected $dataHelper;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param UrlInterface $urlBuilder
     * @param CheckoutSession $checkoutSession
     * @param \Mageants\Orderattachment\Model\ResourceModel\Attachment\Collection $attachmentCollection
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        UrlInterface $urlBuilder,
        CheckoutSession $checkoutSession,
        \Mageants\Orderattachment\Model\ResourceModel\Attachment\Collection $attachmentCollection,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Mageants\Orderattachment\Helper\Data $dataHelper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->urlBuilder = $urlBuilder;
        $this->checkoutSession = $checkoutSession;
        $this->attachmentCollection = $attachmentCollection;
        $this->storeManager = $storeManager;
        $this->dataHelper = $dataHelper;
    }

    /**
     * Get System Configurations 
     */

    public function getConfig()
    {
        $uploadedAttachments = $this->getUploadedAttachments();
        $attachSize = $this->getOrderAttachmentFileSize();

        $attachSizeinMB = $attachSize / 1024 ;
        return [
            'AttachmentEnabled'  => $this->isOrderAttachmentEnabled(),
            'attachments'          => $uploadedAttachments['result'],
            'AttachmentLimit'    => $this->getOrderAttachmentFileLimit(),
            'AttachmentSize'     => $this->getOrderAttachmentFileSize(),
            'AttachmentExt'      => $this->getOrderAttachmentFileExt(),
            'AttachmentUpload'   => $this->getAttachmentUploadUrl(),
            'AttachmentUpdate'   => $this->getAttachmentUpdateUrl(),
            'AttachmentRemove'   => $this->getAttachmentRemoveUrl(),
            'removeItem' => __('Remove Item'),
            'AttachmentInvalidExt' => __('Invalid File Type'),
            'AttachmentComment' => __('Write comment here'),
            'AttachmentInvalidSize' => __('Size of the file is greater than ') . '(' . $attachSizeinMB . ' MB)',
            'AttachmentInvalidLimit' => __('You have reached the limit of files'),
            'AttachmentTitle' =>  $this->dataHelper->getTitle(),
            'AttachmentInfromation' => $this->scopeConfig->getValue(Attachment::XML_PATH_ATTACHMENT_ON_ATTACHMENT_INFORMATION, ScopeInterface::SCOPE_STORE),
            'totalCount' => $uploadedAttachments['totalCount']
        ];
    }

    /**
     * Get Uploaded Attachments in Order
     */
    private function getUploadedAttachments()
    {
        $quoteId = $this->checkoutSession->getQuote()->getId();
            $attachments = $this->attachmentCollection
                ->addFieldToFilter('quote_id', $quoteId)
                ->addFieldToFilter('order_id', ['is' => new \Zend_Db_Expr('null')]);

            $defaultStoreId = $this->storeManager->getDefaultStoreView()->getStoreId();
        foreach ($attachments as $attachment) {
            $url = $this->storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ) . "orderattachment/" . $attachment['path'];
            $attachment->setUrl($url);
            $preview = $this->storeManager->getStore($defaultStoreId)->getUrl(
                'orderattachment/attachment/preview',
                [
                    'attachment' => $attachment['attachment_id'],
                    'hash' => $attachment['hash']
                ]
            );
            $attachment->setPreview($preview);
            $attachment->setPath(basename($attachment->getPath()));
        }

            $result = $attachments->toArray();
            
        foreach ($result['items'] as $key => $value) {
            $result['items'][$key]['attachment_class'] = 'attachment-id'.$value['attachment_id'];
            $result['items'][$key]['hash_class'] = 'attachment-hash'.$value['attachment_id'] ;
        }
            
            $result = $result['items'];

            return ['result' => $result,'totalCount'=> $attachments->getSize()];
        // }

        // return false;
    }
    
    /**
     * Get Value of Configuration ( Extension is Enable/Disable) 
     */
    private function isOrderAttachmentEnabled()
    {
        $moduleEnabled = $this->scopeConfig->getValue(
            Attachment::XML_PATH_ENABLE_ATTACHMENT,
            ScopeInterface::SCOPE_STORE
        );

        return ($moduleEnabled);
    }

    /**
     * Get Value of File Limit from Configuration 
     */
    private function getOrderAttachmentFileLimit()
    {
        return $this->scopeConfig->getValue(
            Attachment::XML_PATH_ATTACHMENT_FILE_LIMIT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Value of File Size from Configuration 
     */
    private function getOrderAttachmentFileSize()
    {
        return $this->scopeConfig->getValue(
            Attachment::XML_PATH_ATTACHMENT_FILE_SIZE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Value of File Extensions ( FILE TYPE )from Configuration 
     */
    private function getOrderAttachmentFileExt()
    {
        return $this->scopeConfig->getValue(
            Attachment::XML_PATH_ATTACHMENT_FILE_EXT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Upload Attachments url (Upload Controller)
     */
    public function getAttachmentUploadUrl()
    {
        return $this->urlBuilder->getUrl('orderattachment/attachment/upload');
    }

    /**
     * Get Update Attachments url (Update Controller)
     */
    public function getAttachmentUpdateUrl()
    {
        return $this->urlBuilder->getUrl('orderattachment/attachment/update');
    }

    /**
     * Get Remove Attachments url (Remove Controller)
     */
    public function getAttachmentRemoveUrl()
    {
        return $this->urlBuilder->getUrl('orderattachment/attachment/delete');
    }
}
