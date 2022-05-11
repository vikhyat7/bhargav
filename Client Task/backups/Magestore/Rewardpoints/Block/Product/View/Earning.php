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
 * @package     Magestore_RewardPoints
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Rewardpoints\Block\Product\View;

/**
 * RewardPoints Show Earning Point on Mini Cart Block
 */
class Earning extends \Magestore\Rewardpoints\Block\RewardpointTemplate
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magestore\Rewardpoints\Helper\Calculation\Earning
     */
    protected $_calculationEarning;

    /**
     * Earning constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magestore\Rewardpoints\Helper\Data $helper
     * @param \Magento\Framework\Registry $registry
     * @param \Magestore\Rewardpoints\Helper\Point $helperPoint
     * @param \Magestore\Rewardpoints\Helper\Calculation\Earning $calculationEarning
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magestore\Rewardpoints\Helper\Data $helper,
        \Magento\Framework\Registry $registry,
        \Magestore\Rewardpoints\Helper\Point $helperPoint,
        \Magestore\Rewardpoints\Helper\Calculation\Earning $calculationEarning,
        array $data = []
    ) {
        parent::__construct($context, $moduleManager, $helper, $helperPoint, $data);
        $this->_coreRegistry = $registry;
        $this->_calculationEarning = $calculationEarning;
    }

    /**
     * Check store is enable for display on minicart sidebar
     *
     * @return boolean
     */
    public function enableDisplay()
    {
        $enableDisplay = $this->_helperPoint->showOnProduct();
        $container = new \Magento\Framework\DataObject(
            [
                'enable_display' => $enableDisplay
            ]
        );

        $this->_eventManager->dispatch(
            'rewardpoints_block_show_earning_on_product',
            [
                'container' => $container,
            ]
        );

        if ($container->getEnableDisplay() && !$this->hasEarningRate()
            || $this->_coreRegistry->registry('product')->getRewardpointsSpend()
        ) {
            return false;
        }
        return $container->getEnableDisplay();
    }

    /**
     * Check product can earn point by rate or not
     *
     * @return boolean
     */
    public function hasEarningRate()
    {

        if ($product = $this->_coreRegistry->registry('product')) {
            if (!$this->_calculationEarning->getRateEarningPoints(10000)) {
                return false;
            }
            $productPrice = $product->getFinalPrice();

            if ($productPrice < 0.0001 && $product->getTypeId() == 'bundle') {
                $totalsprice = $product->getPriceModel()->getTotalPrices($product);
                if (isset($totalsprice[0]) && $totalsprice[0]) {
                    $productPrice = $totalsprice[0];
                }
            }
            if ($productPrice > 0.0001) {

                return true;
            }
        }
        return false;
    }

    /**
     * Get Image (HTML) for reward points
     *
     * @param boolean $hasAnchor
     * @return string
     */
    public function getImageHtml($hasAnchor = true)
    {

        return $this->_helperPoint->getImageHtml($hasAnchor);
    }

    /**
     * Get plural points name
     *
     * @return string
     */
    public function getPluralPointName()
    {
        return $this->_helperPoint->getPluralName();
    }

    /**
     * Get points name
     *
     * @return string
     */
    public function getPointName()
    {
        return $this->_helperPoint->getName();
    }

    /**
     * Get Earning Points
     *
     * @return string
     */
    public function getEarningPoints()
    {
        $earningPoint = 0;
        if ($this->hasData('earning_points')) {
            $earningPoint = $this->getData('earning_points');
        } else {
            if ($this->_coreRegistry->registry('product')
                && $point = $this->_calculationEarning->getRateEarningPoints(
                    $this->_coreRegistry->registry('product')
                        ->getPriceInfo()->getPrice('final_price')->getAmount()->getValue()
                )
            ) {
                $earningPoint = $point;
            }
        }
        if ($earningPoint <= 1) {
            return $earningPoint . ' ' . $this->getPointName();
        }
        return $earningPoint. ' ' . $this->getPluralPointName();
    }
}
