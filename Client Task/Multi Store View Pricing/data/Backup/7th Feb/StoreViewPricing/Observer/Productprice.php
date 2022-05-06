<?php
/**
 * @category Mageants StoreViewPricing
 * @package Mageants_StoreViewPricing
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\StoreViewPricing\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\Http;

/**
 * Save Price class
 */
class Productprice implements ObserverInterface
{
    /**
     * request
     *
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * pricing
     *
     * @var \Mageants\StoreViewPricing\Model\Pricing
     */
    private $pricing;

    /**
     * message
     *
     * @var Magento\Framework\Message\ManagerInterface
     */
    private $message;

    /**
     * helper
     *
     * @var Mageants\StoreViewPricing\Helper\Data
     */
    private $helper;

    public function __construct(
        Http $request,
        \Mageants\StoreViewPricing\Model\Pricing $pricing,
        \Magento\Framework\Message\ManagerInterface $message,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Mageants\StoreViewPricing\Helper\Data $helper
    ) {
        $this->request = $request;
        $this->_pricing = $pricing;
        $this->_message=$message;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->_helper =$helper;
        $this->pricing =  $pricing;
    }
    /**
     * Execute and perform price for store view
     */
    // @codingStandardsIgnoreLine
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ((int)$this->_helper->priceScope()==2) {
            $event = $observer->getEvent();
            $product = $observer->getProduct();
            $priceCollection = $this->pricing->getCollection()
            ->addFieldToFilter(
                'entity_id',
                $product->getId()
            )->addFieldToFilter('store_id', $this->storeManagerInterface->getStore()->getId());
            if (empty($priceCollection->getData())) {
                $priceCollection = $this->pricing->getCollection()
                ->addFieldToFilter(
                    'entity_id',
                    $product->getId()
                )->addFieldToFilter('store_id', 0);
            }
            foreach ($priceCollection as $price) {
                if ($price->getPrice() != 0) {
                    $product->setPrice($price->getPrice());
                }
                if ($price->getSpecialPrice() != 0) {
                    $product->setSpecialPrice($price->getSpecialPrice());
                }
                if ($price->getCost()) {
                    $product->setCost($price->getCost());
                }
                if ($price->getSpecialFromDate()) {
                    $product->setSpecialFromDate($price->getSpecialFromDate());
                }
                if ($price->getSpecialToDate()) {
                    $product->setSpecialToDate($price->getSpecialToDate());
                }
                if ($price->getMsrp()) {
                    $product->setMsrp($price->getMsrp());
                }
                if ($price->getMsrpDisplayActualPriceType()) {
                    $product->setMsrpDisplayActualPriceType($price->getMsrpDisplayActualPriceType());
                }
            }
        }
    }
}
