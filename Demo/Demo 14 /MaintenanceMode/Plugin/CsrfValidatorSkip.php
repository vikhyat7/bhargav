<?php
/**
 * @category Mageants MaintenanceMode
 * @package Mageants_MaintenanceMode
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\MaintenanceMode\Plugin;

use Closure;
use Magento\Framework\App\Request\CsrfValidator;

/**
 * Class CsrfValidatorSkip
 * @package Mageants\MaintenanceMode\Plugin
 */
class CsrfValidatorSkip
{
    /**
     * @param $subject
     * @param Closure $proceed
     * @param RequestInterface $request
     * @param $action
     *
     * @SuppressWarnings("Unused")
     */
    public function aroundValidate(
        CsrfValidator $subject,
        Closure $proceed,
        $request,
        $action
    ) {
        if ($request->getModuleName() === 'maintenancemode') {
            return; // Skip CSRF check
        }
        $proceed($request, $action); // Proceed Magento 2 core functionality
    }
}
