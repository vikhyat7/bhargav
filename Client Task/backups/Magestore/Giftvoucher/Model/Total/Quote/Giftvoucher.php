<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Model\Total\Quote;

/**
 * Giftvoucher Total Quote Giftvoucher Model
 */
class Giftvoucher extends GiftvoucherAbstract
{
    /**
     * @var string
     */
    protected $_code = 'giftvoucher';

    /**
     * Collect reward points total
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        $this->calculateDiscount($quote, $shippingAssignment, $total, true);
        return $this;
    }
}
