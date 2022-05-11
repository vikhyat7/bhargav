<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GIftCertificate\Model\Total;

/**
 * Gift class 
 */
class Gift extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{   
    /**
     * price currency
     *
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceCurrency;
    
    /**
     * checkout session
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;
    
    /**
     * fees
     *
     * @var    String
     */
    protected $_fees;

    /**
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */ 
    public function __construct(\Magento\Checkout\Model\Session $checkoutSession, \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency)
    {
        $this->_checkoutSession=$checkoutSession;
        $this->_priceCurrency = $priceCurrency;
        $this->_fees=0;

        if($this->_checkoutSession->getGift()!=''):

            $this->_fees=$this->_checkoutSession->getGift();
        endif;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $this->_checkoutSession = $objectManager->create('Magento\Checkout\Model\Session');
        $this->_fees=0;

        if($this->_checkoutSession->getGift()!=''):

            $this->_fees=$this->_checkoutSession->getGift();
        endif;

        $discount = $this->_fees;
        $total->addTotalAmount('giftcertificate', - $discount);
        $total->addBaseTotalAmount('giftcertificate', -$discount);
        //$total->setBaseGrandTotal($total->getBaseGrandTotal() - $discount);
        $quote->setCustomDiscount(-$discount);
        return $this;
    } 

    /**
     * @param Magento\Quote\Model\Quote\Address $total
     */
    protected function clearValues(Address\Total $total)
    {
        $total->setTotalAmount('subtotal', 0);
        $total->setBaseTotalAmount('subtotal', 0);
        $total->setTotalAmount('tax', 0);
        $total->setBaseTotalAmount('tax', 0);
        $total->setTotalAmount('discount_tax_compensation', 0);
        $total->setBaseTotalAmount('discount_tax_compensation', 0);
        $total->setTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setBaseTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setSubtotalInclTax(0);
        $total->setBaseSubtotalInclTax(0);
    }
    
    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param Address\Total $total
     * @return array|null
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        
        return [
            'code' => 'giftcertificate',
            'title' => 'giftcertificate',
            'value' => -$this->_fees
        ];

    }

    /**
     * Get Subtotal label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('Gift Card');
    }
}