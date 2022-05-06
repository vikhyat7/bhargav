<?php

namespace Mageants\Orderattachment\Model\ResourceModel\Attachment;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Mageants\Orderattachment\Model\Attachment', 'Mageants\Orderattachment\Model\ResourceModel\Attachment');
    }
}
