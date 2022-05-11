<?php
/**
 * Created by Magestore Developer.
 * Date: 1/26/2016
 * Time: 4:09 PM
 * Set final price to product
 */

namespace Magestore\Rewardpoints\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Observer - Sales Model Service Quote Submit Success
 */
class SalesModelServiceQuoteSubmitSuccess implements ObserverInterface
{

    /**
     * @var \Magento\Checkout\Model\SessionFactory
     */
    protected $_checkOutSessionFactory;
    /**
     * Helper Action
     *
     * @var \Magestore\Rewardpoints\Helper\Action
     */
    protected $_action;

    /**
     * SalesModelServiceQuoteSubmitAfter constructor.
     * @param \Magento\Checkout\Model\SessionFactory $sessionFactory
     * @param \Magestore\Rewardpoints\Helper\Action $action
     */
    public function __construct(
        \Magento\Checkout\Model\SessionFactory $sessionFactory,
        \Magestore\Rewardpoints\Helper\Action $action
    ) {
        $this->_checkOutSessionFactory = $sessionFactory;
        $this->_action = $action;
    }
    /**
     * Set Final Price to product in product list
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $order = $observer['order'];
        $quote = $observer['quote'];
        if ($order->getCustomerIsGuest()) {
            return $this;
        }

        // Process spending points for order
        if ($order->getRewardpointsSpent() > 0) {
            $this->_action->addTransaction(
                'spending_order',
                $quote->getCustomer(),
                $order
            );
        }

        // Clear reward points checkout session
        $session = $this->_checkOutSessionFactory->create();
        $session->setCatalogRules([]);
        $session->setData('use_point', 0);
        $session->setRewardSalesRules([]);
        $session->setRewardCheckedRules([]);

        return $this;
    }
}
