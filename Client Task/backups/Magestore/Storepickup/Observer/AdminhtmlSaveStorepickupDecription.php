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
 * @package     Magestore_StorePickup
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Storepickup\Observer;

use Magento\Framework\Event\ObserverInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Magestore\Storepickup\Model\ResourceModel\Orders\StorepickupStatus;

/**
 * Class AdminhtmlSaveStorepickupDecription
 *
 * Used to observe store pickup save description
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class AdminhtmlSaveStorepickupDecription implements ObserverInterface
{
    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @codeCoverageIgnore
     */
    protected $_checkoutSession;

    /**
     * @var \Magestore\Storepickup\Model\StoreFactory
     */
    protected $_storeCollection;
    /**
     * @var \Magento\Sales\Api\Data\OrderAddressInterface
     */
    protected $_orderAddressInterface;
    /**
     * @var \Magestore\Storepickup\Helper\Data
     */
    protected $_storepickupHelper;
    /**
     * @var \Magestore\Storepickup\Helper\Email
     */
    protected $_storepickupHelperEmail;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendSession;

    /**
     * AdminhtmlSaveStorepickupDecription constructor.
     *
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Magestore\Storepickup\Model\StoreFactory $storeCollection
     * @param \Magento\Sales\Api\Data\OrderAddressInterface $orderAddressInterface
     * @param \Magestore\Storepickup\Helper\Data $storepickupHelper
     * @param \Magestore\Storepickup\Helper\Email $storepickupHelperEmail
     */
    public function __construct(
        \Magento\Backend\Model\Session $backendSession,
        \Magestore\Storepickup\Model\StoreFactory $storeCollection,
        \Magento\Sales\Api\Data\OrderAddressInterface $orderAddressInterface,
        \Magestore\Storepickup\Helper\Data $storepickupHelper,
        \Magestore\Storepickup\Helper\Email $storepickupHelperEmail
    ) {
        $this->_backendSession = $backendSession;
        $this->_storeCollection = $storeCollection;
        $this->_orderAddressInterface = $orderAddressInterface;
        $this->_storepickupHelper = $storepickupHelper;
        $this->_storepickupHelperEmail = $storepickupHelperEmail;
    }

    /**
     * Execute
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            /** @var \Magento\Sales\Model\Order $order */
            $order = $observer->getEvent()->getOrder();
            $shippingMethod = $order->getShippingMethod();
            if ($shippingMethod && $shippingMethod == "storepickup_storepickup") {
                if ($this->_backendSession->getData('storepickup')) {
                    $new = $order->getShippingDescription();
                    $storepickup_session = $this->_backendSession->getData('storepickup', true);
                    $storeId = $storepickup_session['store_id'];
                    $collectionstore = $this->_storeCollection->create();
                    $store = $collectionstore->load($storeId, 'storepickup_id');
                    //set Shipping Description

                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $gooleAPI = $objectManager->create(\Magestore\Storepickup\Helper\Data::class)
                        ->getGoogleApiKey();

                    $pickupTime = '';
                    if (isset($storepickup_session['shipping_date']) && isset($storepickup_session['shipping_time'])) {
                        $pickupTime = $storepickup_session['shipping_date'] . ' '
                            . $storepickup_session['shipping_time'];
                        $new .= '<br>' . __('Pickup date') . ' : ' . $storepickup_session['shipping_date'] . '<br>'
                            . __('Pickup time') . ' : ' . $storepickup_session['shipping_time']
                            . '<br><img src="http://maps.google.com/maps/api/staticmap?key=' . $gooleAPI
                            . '&center=' . $store->getData('latitude') . ',' . $store->getData('longitude')
                            . '&zoom=15&size=200x200&markers=color:red|label:S|' . $store->getData('latitude')
                            . ',' . $store->getData('longitude') . '&sensor=false" />';
                    } else {
                        $new .= '<br><img src="http://maps.google.com/maps/api/staticmap?key='
                            . $gooleAPI . '&center=' . $store->getData('latitude') . ','
                            . $store->getData('longitude') . '&zoom=15&size=200x200&markers=color:red|label:S|'
                            . $store->getData('latitude') . ',' . $store->getData('longitude') . '&sensor=false" />';
                    }
                    $order->setShippingDescription($new);
                    //$order->sendNewOrderEmail();
                    //$this->_storepickupHelperEmail->sendNoticeEmailToStoreOwner($order,$store);
                    //$this->_storepickupHelperEmail->sendNoticeEmailToAdmin($order,$store);
                    $order->setData('storepickup_id', $store->getId())
                        ->setData('storepickup_status', StorepickupStatus::STOREPICUP_PENDING)
                        ->setData('storepickup_time', $pickupTime);
                }
            }
        } catch (Exception $e) {
            return $this;
        }
    }
}
