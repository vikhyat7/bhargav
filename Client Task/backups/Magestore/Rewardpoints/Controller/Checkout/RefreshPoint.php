<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Giftvoucher
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
namespace Magestore\Rewardpoints\Controller\Checkout;

/**
 * @category Magestore
 * @package  Magestore_Affiliateplus
 * @module   Affiliateplus
 * @author   Magestore Developer
 */
class RefreshPoint extends \Magestore\Rewardpoints\Controller\AbstractAction
{
    /**
     * @return mixed
     */
    public function execute()
    {
        return $this->getResponse()->setBody($this->_helperSpend->getRulesJson($this->_helperSpend->getSliderRules()));

    }



}