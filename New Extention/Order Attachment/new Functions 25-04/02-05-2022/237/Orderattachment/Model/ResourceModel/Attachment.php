<?php
/**
 * @category Mageants_Orderattachment
 * @package Mageants_Orderattachment
 * @copyright Copyright (c) 2022 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\Orderattachment\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Attachment extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageants_Orderattachment', 'attachment_id');
    }
    
    /*
    * Get Attachments By Order Id
    */
    public function getOrderAttachments($orderId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from($this->getMainTable())
            ->where('order_id = ?', $orderId);

        return $connection->fetchAll($select);
    }
    
    /*
    * Get Attachments By Order Id
    */
    public function getAttachmentsByQuote($quoteId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from($this->getMainTable())
            ->where('quote_id = ?', $quoteId);

        return $connection->fetchAll($select);
    }
}
