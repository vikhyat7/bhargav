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
namespace Magestore\Rewardpoints\Block\Totals\Creditmemo;

/**
 * Rewardpoints Total Label Block
 */
class Point extends \Magento\Sales\Block\Order\Totals
{
    /**
     * @var \Magestore\Rewardpoints\Helper\Point
     */
    protected $helperPoint;

    /**
     * Point constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magestore\Rewardpoints\Helper\Point $helperPoint
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magestore\Rewardpoints\Helper\Point $helperPoint,
        array $data = []
    ) {
        $this->helperPoint = $helperPoint;
        parent::__construct($context, $registry, $data);
    }

    /**
     * Add points value into creditmemo total
     */
    public function initTotals()
    {
        if (!$this->helperPoint->getGeneralConfig('enable')) {
            return $this;
        }
        /** @var \Magento\Sales\Block\Order\Creditmemo\Totals $totalsBlock */
        $totalsBlock = $this->getParentBlock();
        $creditmemo = $totalsBlock->getCreditmemo();

        if ($creditmemo->getRewardpointsEarn()) {
            $totalsBlock->addTotal(new \Magento\Framework\DataObject([
                'code' => 'rewardpoints_earn_label',
                'label' => __('Earn Points'),
                'value' => $this->helperPoint->format($creditmemo->getRewardpointsEarn()),
                'is_formated' => true,
            ]), 'subtotal');
        }

        if ($creditmemo->getRewardpointsSpent()) {
            $totalsBlock->addTotal(new \Magento\Framework\DataObject([
                'code' => 'rewardpoints_spent_label',
                'label' => __('Spend Points'),
                'value' => $this->helperPoint->format($creditmemo->getRewardpointsSpent()),
                'is_formated' => true,
            ]), 'rewardpoints_earn_label');
        }

        if ($creditmemo->getRewardpointsDiscount() >= 0.0001) {
            $totalsBlock->addTotal(new \Magento\Framework\DataObject([
                'code' => 'rewardpoints',
                'label' => __('Use points on spend'),
                'value' => -$creditmemo->getRewardpointsDiscount(),
                'base_value' => -$creditmemo->getRewardpointsBaseDiscount(),
            ]), 'rewardpoints_spent_label');

            /**
             * Get total discount and re-calculate discount value to showing
             */
            $discountTotal = $totalsBlock->getTotal('discount');
            if (!empty($discountTotal) && $discountTotal->getValue() != 0) {
                $discountTotal->setValue($discountTotal->getValue() + $creditmemo->getRewardpointsDiscount());
                if ($discountTotal->getValue() != 0) {
                    $totalsBlock->addTotal($discountTotal);
                } else {
                    $totalsBlock->removeTotal($discountTotal->getCode());
                }
            }
        }
        return $this;
    }
}
