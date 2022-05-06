<?php
/**
 * @category Mageants_Orderattachment
 * @package Mageants_Orderattachment
 * @copyright Copyright (c) 2022 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\Orderattachment\Api\Data;

interface AttachmentInterface
{
    const ATTACHMENT_ID = 'attachment_id';
    const QUOTE_ID = 'quote_id';
    const ORDER_ID = 'order_id';
    const PATH = 'path';
    const COMMENT = 'comment';
    const HASH = 'hash';
    const TYPE = 'type';
    const UPLOADED_AT = 'uploaded_at';
    const MODIFIED_AT = 'modified_at';

    public function getAttachmentId();

    public function getQuoteId();

    public function getOrderId();

    public function getPath();

    public function getComment();

    public function getHash();

    public function getType();

    public function getUploadedAt();

    public function getModifiedAt();

    public function setAttachmentId($AttachmentId);

    public function setQuoteId($QuoteId);

    public function setOrderId($OrderId);

    public function setPath($Path);

    public function setComment($Comment);

    public function setHash($Hash);

    public function setType($Type);

    public function setUploadedAt($UploadedAt);

    public function setModifiedAt($ModifiedAt);
}
