<?php
/**
 * @category Mageants_Orderattachment
 * @package Mageants_Orderattachment
 * @copyright Copyright (c) 2022 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\Orderattachment\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class AttachmentOrderAfterPlaceObserver implements ObserverInterface
{
    protected $attachmentCollection;

    public function __construct(
        \Mageants\Orderattachment\Model\ResourceModel\Attachment\Collection $attachmentCollection,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->attachmentCollection = $attachmentCollection;
        $this->logger = $logger;
    }
    /**
     * Set Order Id In Attachments After Placing The Order.
     */
    public function execute(EventObserver $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if (!$order) {
            return $this;
        }

        $attachments = $this->attachmentCollection
            ->addFieldToFilter('quote_id', $order->getQuoteId())
            ->addFieldToFilter('order_id', ['is' => new \Zend_Db_Expr('null')]);

        foreach ($attachments as $attachment) {
            try {
                $attachment->setOrderId($order->getId())->save();
            } catch (\Exception $e) {
                continue;
            }
        }

        return $this;
    }
}
