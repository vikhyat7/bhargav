<?php
/**
 * @category Mageants MaintenanceMode
 * @package Mageants_MaintenanceMode
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\MaintenanceMode\Model\Config\Source\System;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class SubscribeType
 *
 * @package Mageants\MaintenanceMode\Model\Config\Source\System
 */
class SubscribeType implements ArrayInterface
{
    const NONE          = 'none';
    const EMAIL_FORM    = 'email_form';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::NONE, 'label' => __('None')],            
            ['value' => self::EMAIL_FORM, 'label' => __('Newsletter Subscription')]
        ];
    }
}
