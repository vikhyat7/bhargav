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
 * @package     Magestore_Affiliateplus
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
namespace Magestore\Rewardpoints\Block\Checkout;

use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Reward points - Checkout form block
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Form extends \Magento\Payment\Block\Form
{
    /**
     * @var \Magestore\Rewardpoints\Helper\Point
     */
    protected $_helperPoint;

    /**
     * @var \Magestore\Rewardpoints\Helper\Calculation\Earning
     */
    protected $_helperEarning;

    /**
     * @var \Magestore\Rewardpoints\Helper\Calculation\Spending
     */
    protected $_helperSpending;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $_currencyFactory;

    /**
     * @var PriceCurrencyInterface
     */
    protected $_priceCurrency;
    /**
     * @var \Magento\Checkout\Model\SessionFactory
     */
    protected $_checkoutSessionFactory;
    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    protected $_customerSessionFactory;

    /**
     * @var \Magestore\Rewardpoints\Helper\Data
     */
    protected $_helperData;
    /**
     * Request
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magestore\Rewardpoints\Helper\Block\Spend
     */
    protected $_blockSpend;

    /**
     * @var \Magestore\Rewardpoints\Helper\Customer
     */
    protected $_rewardpointCustomer;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlInterface;

    /**
     * @var \Magento\Framework\Session\SessionManager
     */
    protected $_sessionManager;

    /**
     * Form constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magestore\Rewardpoints\Helper\Point $helperPoint
     * @param \Magestore\Rewardpoints\Helper\Data $helperData
     * @param \Magestore\Rewardpoints\Helper\Customer $helperCustomer
     * @param \Magestore\Rewardpoints\Helper\Calculation\Earning $helperEarning
     * @param \Magestore\Rewardpoints\Helper\Calculation\Spending $helperSpending
     * @param \Magento\Checkout\Model\SessionFactory $checkoutSessionFactory
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Customer\Model\SessionFactory $customerSessionFactory
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magestore\Rewardpoints\Helper\Block\Spend $blockSpend
     * @param \Magento\Framework\Session\SessionManager $sessionManager
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magestore\Rewardpoints\Helper\Point $helperPoint,
        \Magestore\Rewardpoints\Helper\Data $helperData,
        \Magestore\Rewardpoints\Helper\Customer $helperCustomer,
        \Magestore\Rewardpoints\Helper\Calculation\Earning $helperEarning,
        \Magestore\Rewardpoints\Helper\Calculation\Spending $helperSpending,
        \Magento\Checkout\Model\SessionFactory $checkoutSessionFactory,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Customer\Model\SessionFactory $customerSessionFactory,
        PriceCurrencyInterface $priceCurrency,
        \Magestore\Rewardpoints\Helper\Block\Spend $blockSpend,
        \Magento\Framework\Session\SessionManager $sessionManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_helperPoint = $helperPoint;
        $this->_helperEarning = $helperEarning;
        $this->_helperSpending = $helperSpending;
        $this->_checkoutSessionFactory = $checkoutSessionFactory;
        $this->_customerSessionFactory = $customerSessionFactory;
        $this->_currencyFactory = $currencyFactory;
        $this->_priceCurrency = $priceCurrency;
        $this->_request = $context->getRequest();
        $this->_helperData = $helperData;
        $this->_rewardpointCustomer = $helperCustomer;
        $this->_blockSpend = $blockSpend;
        $this->_sessionManager = $sessionManager;
    }

    /**
     * Get Store
     *
     * @param null|string|int $storeId
     * @return \Magento\Store\Api\Data\StoreInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStore($storeId = null)
    {
        return $this->_storeManager->getStore($storeId);
    }

    /**
     * Get Helper Earning
     *
     * @return \Magestore\Rewardpoints\Helper\Calculation\Earning
     */
    public function getHelperEarning()
    {
        return $this->_helperEarning;
    }

    /**
     * Get Reward points Data
     *
     * @return array
     */
    public function getRewardpointsData()
    {
        $earningLabel = __('You will earn');
        $spendingLabel = __('You will spend');

        if ($this->_request->getModuleName() == 'webpos') {
            $earningLabel = __('Customer will earn');
            $spendingLabel = __('Customer will spend');
        } elseif (!$this->_customerSessionFactory->create()->isLoggedIn()) {
            if ($this->_helperData->isModuleOutputEnabled('Magestore_Onestepcheckout')
                && $this->_request->getModuleName() == 'onestepcheckout'
            ) {
                $earningLabel = "<a href='javascript:void(0);' onclick='login_popup.show();'>"
                    . __('Login')
                    . "</a> "
                    . __('to earn');
            } else {
                $this->_sessionManager->setData('redirect', $this->_urlBuilder->getCurrentUrl());
                $earningLabel = "<a href='" . $this->getUrl('rewardpoints/index/redirectLogin') . "'>"
                    . __('Login')
                    . "</a> "
                    . __('to earn');
            }
        }
        // $this->_checkoutSessionFactory->create()->getQuote()->setTotalsCollectedFlag(false)->collectTotals()->save();
        $rewardPointDiscount = strip_tags(
            $this->_helperData->convertAndFormat(
                -$this->_checkoutSessionFactory->create()->getQuote()->getRewardpointsBaseDiscount()
            )
        );

        $result = [];
        $result['enableReward'] = $this->_blockSpend->enableReward();
        $result['displayEarning'] = $this->getEarningPoint() > 0;
        $result['rewardpointsEarning'] = $this->_helperPoint->format($this->getEarningPoint());
        $result['displaySpending'] = $this->getSpendingPoint() > 0;
        $result['rewardpointsSpending'] = $this->_helperPoint->format($this->getSpendingPoint());
        $result['displayUsePoint'] =
            $this->_checkoutSessionFactory->create()->getQuote()->getRewardpointsBaseDiscount() ?
                $this->_checkoutSessionFactory->create()->getQuote()->getRewardpointsBaseDiscount() : 0;
        $result['rewardpointsUsePoint'] = $rewardPointDiscount;
        $result['earningLabel'] = $earningLabel;
        $result['spendingLabel'] = $spendingLabel;
        return $result;
    }

    /**
     * Get Earning Point
     *
     * @return int
     */
    public function getEarningPoint()
    {
        if ($this->_helperSpending->getTotalPointSpent()
            && !$this->_helperData->getEarningConfig('earn_when_spend', $this->_storeManager->getStore()->getId())
        ) {
            return 0;
        }
        return $this->_helperEarning->getTotalPointsEarning();
    }

    /**
     * Get Spending Point
     *
     * @return int
     */
    public function getSpendingPoint()
    {
        return $this->_helperSpending->getTotalPointSpent();
    }
}
