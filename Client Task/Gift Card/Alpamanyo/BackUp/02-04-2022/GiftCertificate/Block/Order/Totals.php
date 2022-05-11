<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\GiftCertificate\Block\Order;

/**
 * order totals block
 */
class Totals extends \Magento\Sales\Block\Adminhtml\Totals 
{
    

    /**
     * Check if we nedd display full tax total info
     *
     * @return bool
     */
    public function displayFullSummary()
    {
        return true;
    }

    /**
     * Get data (totals) source model
     *
     * @return \Magento\Framework\DataObject
     */
    public function getSource()
    {
        return $this->_source;
    } 

    /**
     * Get order store
     *
     * @return \Magento\Framework\DataObject
     */    
    public function getStore()
    {
        return $this->_order->getStore();
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * @return array
     */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * @return array
     */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }

    /**
     * Initialize order totals array
     *
     * @return $this
     */
    protected function _initTotals()
    {
        parent::_initTotals();
        $this->_totals['paid'] = new \Magento\Framework\DataObject(
            [
                'code' => 'paid',
                'strong' => true,
                'value' => $this->getSource()->getTotalPaid(),
                'base_value' => $this->getSource()->getBaseTotalPaid(),
                'label' => __('Total Paid'),
                'area' => 'footer',
            ]
        );
        $this->_totals['refunded'] = new \Magento\Framework\DataObject(
            [
                'code' => 'refunded',
                'strong' => true,
                'value' => $this->getSource()->getTotalRefunded(),
                'base_value' => $this->getSource()->getBaseTotalRefunded(),
                'label' => __('Total Refunded'),
                'area' => 'footer',
            ]
        );
        $this->_totals['due'] = new \Magento\Framework\DataObject(
            [
                'code' => 'due',
                'strong' => true,
                'value' => $this->getSource()->getTotalDue(),
                'base_value' => $this->getSource()->getBaseTotalDue(),
                'label' => __('Total Due'),
                'area' => 'footer',
            ]
        );

        $parent = $this->getParentBlock();
        $this->_order = $parent->getOrder();
        $this->_source = $parent->getSource();
        
        $fee = new \Magento\Framework\DataObject(
            [
                'code' => 'giftcertificate',
                'strong' => false,
                'value' => $this->_order->getOrderGift(),
                //'value' => $this->_source->getFee(),
                'label' => __('GiftCertificate'),
            ]
        );

        $parent->addTotal($fee, 'giftcertificate');
        $parent->addTotal($fee, 'giftcertificate');
        return $this;
    }
}
