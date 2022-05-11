<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderCustomization\Service;

/**
 * Class PaymentTermService
 *
 * @package Magestore\PurchaseOrderCustomization\Service
 */
class PaymentTermService
{
    const TERM_0_DAY = 0;
    const TERM_30_DAY = 30;
    const TERM_60_DAY = 60;
    const TERM_90_DAY = 90;
    const TERM_BLANK = -1;

    /**
     * Get Payment Term Options
     *
     * @return array
     */
    public function getPaymentTermOptions()
    {
        return [
            self::TERM_BLANK => __('--Please Select Term--'),
            self::TERM_0_DAY => __('Cash (0 day)'),
            self::TERM_30_DAY => __('30 days'),
            self::TERM_60_DAY => __('60 days'),
            self::TERM_90_DAY => __('90 days'),
        ];
    }

    /**
     * To Payment Term Option Array
     *
     * @return array
     */
    public function toPaymentTermOptionArray()
    {
        $availableOptions = $this->getPaymentTermOptions();
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
