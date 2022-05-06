<?php
namespace Mageants\Orderattachment\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface AttachmentSearchResultsInterface extends SearchResultsInterface
{
    public function getItems();

    public function setItems(array $items);
}
