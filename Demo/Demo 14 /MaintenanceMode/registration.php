<?php
/**
 * Mageants
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageants.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageants.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  Mageants
 * @package   Mageants_MaintenanceMode
 * @copyright Copyright (c) Mageants (https://www.mageants.com/)
 * @license   https://www.mageants.com/LICENSE.txt
 */

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'Mageants_MaintenanceMode',
    __DIR__
);
