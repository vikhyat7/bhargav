<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Rewardpoints\Model\Plugin\Checkout;
/**
 * Class Discount
 * @package Magestore\Rewardpoints\Model\Plugin\SalesRule\Quote
 */
class PaymentInformationManagement
{
    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * PaymentInformationManagement constructor.
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }
    
    /**
     * @param \Magento\Checkout\Model\PaymentInformationManagement $paymentInformationManagement
     * @param $paymentDetails
     * @param $cartId
     * @return $paymentDetails
     */
    public function aroundGetPaymentInformation(
        \Magento\Checkout\Model\PaymentInformationManagement $paymentInformationManagement,
        \Closure $proceed,
        $cartId
    )
    {
        $paymentDetails = $proceed($cartId);
        $totals = $paymentDetails->getTotals();
        if ($totals->getBaseDiscountAmount()) {
            /** @var \Magento\Quote\Model\Quote $quote */
            $quote = $this->quoteRepository->getActive($cartId);
            $rewardpointsBaseDiscount = $quote->getRewardpointsBaseDiscount();
            if($rewardpointsBaseDiscount){
                $totals->setDiscountAmount($totals->getDiscountAmount() + $quote->getRewardpointsDiscount());
                $totals->setBaseDiscountAmount($totals->getBaseDiscountAmount() + $quote->getRewardpointsBaseDiscount());
            }
        }
        return $paymentDetails->setTotals($totals);
    }
}