<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
 
namespace Mageants\GiftCertificate\Block\Cart;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;

/**
 * Total class for cart Totals
 */ 
class Totals extends \Magento\Checkout\Block\Cart\Totals
{
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param  \Magento\Sales\Model\Config $salesConfig,
     * @param array $data
     */
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
    \Magento\Customer\Model\Session $customerSession,
    \Magento\Checkout\Model\Session $checkoutSession,
    \Magento\Sales\Model\Config $salesConfig,
    array $layoutProcessors = [],
    array $data = []
	) {
		parent::__construct($context, $customerSession, $checkoutSession, $salesConfig, $layoutProcessors, $data);
	}
   
    /**
     * 
     * @return $this
     */
    public function getJsLayout()
    {
        foreach ($this->layoutProcessors as $processor) {
            $this->jsLayout = $processor->process($this->jsLayout);
        }
        return parent::getJsLayout();
    }
}
