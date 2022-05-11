<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCertificate\Model;
/**
 * PaymentMethodManagement  Model class for gift card
 */
class PaymentMethodManagement extends \Magento\Quote\Model\PaymentMethodManagement
{
    /**
     * {@inheritdoc}
     */
    public function set($cartId, \Magento\Quote\Api\Data\PaymentInterface $method)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->get($cartId);
        $method->setChecks([
            \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_CHECKOUT,
            \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_FOR_COUNTRY,
            \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_FOR_CURRENCY,
            \Magento\Payment\Model\Method\AbstractMethod::CHECK_ORDER_TOTAL_MIN_MAX,
        ]);
        $payment = $quote->getPayment();
        \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class)->debug("tet123".$payment->getMethod());
        
        $data = $method->getData();
        $payment->importData($data);

        if ($quote->isVirtual()) {
            $quote->getBillingAddress()->setPaymentMethod($payment->getMethod());
        } else {
            // check if shipping address is set
            if ($quote->getShippingAddress()->getCountryId() === null) {
                throw new InvalidTransitionException(__('Shipping address is not set'));
            }
            $quote->getShippingAddress()->setPaymentMethod($payment->getMethod());
        }
        if (!$quote->isVirtual() && $quote->getShippingAddress()) {
            $quote->getShippingAddress()->setCollectShippingRates(true);
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$_checkoutSession = $objectManager->create('\Magento\Checkout\Model\Session');
        if($_checkoutSession->getAccountid()=='' &&  $_checkoutSession->getGift()==''){
	        if (!$this->zeroTotalValidator->isApplicable($payment->getMethodInstance(), $quote)) {
	            throw new InvalidTransitionException(__('The requested Payment Method is not available.'));
	        }
    	}

        $quote->setTotalsCollectedFlag(false)->collectTotals()->save();
        return $quote->getPayment()->getId();
    }

  
}