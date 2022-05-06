<?php

namespace Mageants\GiftCertificate\Block\Adminhtml\Sales\Order\Invoice;

class GiftCard extends \Magento\Framework\View\Element\Template
{
    protected $_config;
    protected $_order;
    protected $_source;
    protected $helperdata;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Tax\Model\Config $taxConfig,
        \Mageants\GiftCertificate\Helper\Data $helperdata,
        array $data = []
    ) {
        $this->_config = $taxConfig;
        $this->helperdata = $helperdata;
        parent::__construct($context, $data);
    }

    public function displayFullSummary()
    {
        return true;
    }

    public function getSource()
    {
        return $this->_source;
    } 
    public function getStore()
    {
        return $this->_order->getStore();
    }
    public function getOrder()
    {
        return $this->_order;
    }
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }
     public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->_order = $parent->getOrder();
        $this->_source = $parent->getSource();

        $store = $this->getStore();
        if($this->_order->getOrderGift() != NULL)
        {
            $fee = new \Magento\Framework\DataObject(
                [
                    'code' => 'giftcertificate',
                    'strong' => false,
                    'value' => $this->_order->getOrderGift(),
                    // 'base_value' => $this->_order->getFee(),
                    'label' => __('Giftcertificate'),
                ]
            );
             $parent->addTotal($fee, 'giftcertificate');
             $parent->addTotal($fee, 'giftcertificate');
        }
            return $this;
    }

}