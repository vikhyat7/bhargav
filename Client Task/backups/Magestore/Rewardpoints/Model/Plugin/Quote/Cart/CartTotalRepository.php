<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Rewardpoints\Model\Plugin\Quote\Cart;
/**
 * Class CartTotalRepository
 * @package Magestore\Rewardpoints\Model\Plugin\Quote\Cart
 */
class CartTotalRepository
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
    public function aroundGet(
        \Magento\Quote\Model\Cart\CartTotalRepository $cartTotalRepository,
        \Closure $proceed,
        $cartId
    )
    {
        $quoteTotals = $proceed($cartId);
        if ($quoteTotals->getBaseDiscountAmount()) {
            /** @var \Magento\Quote\Model\Quote $quote */
            $quote = $this->quoteRepository->getActive($cartId);
            $rewardpointsBaseDiscount = $quote->getRewardpointsBaseDiscount();
            if($rewardpointsBaseDiscount){
                $quoteTotals->setBaseDiscountAmount($quoteTotals->getBaseDiscountAmount() + $rewardpointsBaseDiscount);
                $quoteTotals->setDiscountAmount($quoteTotals->getDiscountAmount() + $quote->getRewardpointsDiscount());
            }
        }
        return $quoteTotals;
    }
}