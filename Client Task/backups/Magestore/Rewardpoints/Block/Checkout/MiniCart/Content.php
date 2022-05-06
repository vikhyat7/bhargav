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

/**
 * RewardPoints Show Spending Point on Shopping Cart Page
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
namespace Magestore\Rewardpoints\Block\Checkout\MiniCart;

/**
 * Reward points - Checkout minicart content block
 */
class Content extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magestore\Rewardpoints\Helper\Point
     */
    protected $helperPoint;

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    protected $_customerSessionFactory;

    /**
     * @var \Magestore\Rewardpoints\Helper\Calculation\Earning
     */
    protected $_calculationEarning;

    /**
     * Content constructor.
     * @param \Magestore\Rewardpoints\Helper\Point $helperPoint
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\SessionFactory $customerSessionFactory
     * @param \Magestore\Rewardpoints\Helper\Calculation\Earning $calculationEarning
     * @param array $data
     */
    public function __construct(
        \Magestore\Rewardpoints\Helper\Point $helperPoint,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\SessionFactory $customerSessionFactory,
        \Magestore\Rewardpoints\Helper\Calculation\Earning $calculationEarning,
        array $data
    ) {
        parent::__construct($context, $data);
        $this->helperPoint = $helperPoint;
        $this->_customerSessionFactory = $customerSessionFactory;
        $this->_calculationEarning = $calculationEarning;
    }

    /**
     * Check store is enable for display on minicart sidebar
     *
     * @return string
     */
    public function enableDisplay()
    {
        return $this->helperPoint->showOnMiniCart();
    }

    /**
     * Get Image (HTML) for reward points
     *
     * @param boolean $hasAnchor
     * @return string
     */
    public function getImageHtml($hasAnchor = true)
    {
        return $this->helperPoint->getImageHtml($hasAnchor);
    }

    /**
     * Knockout Data
     *
     * @return array
     */
    public function knockoutData()
    {
        $earning = $this->_calculationEarning;
        $results = [];
        if ($this->enableDisplay()) {
            $earningPoint = $earning->getTotalPointsEarning();
            $results['enableReward'] = $this->enableDisplay();
            $results['getImageHtml'] = $this->getImageHtml(true);
            $results['customerLogin'] = $this->_customerSessionFactory->create()->isLoggedIn();
            $earningPointFormat = $this->helperPoint->format($earningPoint);
            if ($earningPointFormat) {
                $results['earnPoint'] = $earningPointFormat;
            } else {
                $results['earnPoint'] = false;
            }

            $results['urlRedirectLogin'] = $this->_urlBuilder->getUrl(
                'rewardpoints/index/redirectLogin',
                [
                    'redirect'=>$this->_urlBuilder->getCurrentUrl()
                ]
            );
        }

        return $results;
    }
}
