<?php
/**
 * @category Mageants StoreViewPricing
 * @package Mageants_StoreViewPricing
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\StoreViewPricing\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\Http;

class Saveprice implements ObserverInterface
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

    /**
     * jsonHelper
     *
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    private $storeManager;

    public function __construct(
        Http $request,
        \Mageants\StoreViewPricing\Model\Pricing $pricing,
        \Magento\Framework\Message\ManagerInterface $message,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Mageants\StoreViewPricing\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->request = $request;
        $this->_pricing = $pricing;
        $this->_message=$message;
        $this->_helper =$helper;
        $this->_jsonHelper = $jsonHelper;
        $this->_storeManager = $storeManager;
    }
    /**
     * Execute and perform price for store view
     */
    // @codingStandardsIgnoreLine
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ((int)$this->_helper->priceScope()==2) {
            try {
                $entityID = $observer->getProduct()->getEntityId();
                $posts = $this->request->getPost();
                $storeId = $this->_storeManager->getStore()->getId();
                foreach ($posts as $key => $postData) {
                    if ($key=='product') {
                        if (is_array($postData)) {
                            $cost='';
                            if (array_key_exists('cost', $postData)) {
                                $cost = $postData['cost'];
                            }
                            $tier_price='';
                            $msrp='';
                            $msrp_display_actual_price_type='';
                            $price='';
                            $special_price='';
                            $special_from_date='';
                            $special_to_date='';
                        
                            if (array_key_exists('sku', $postData)) {
                                $sku = $postData['sku'];
                            }
                            if (array_key_exists('tier_price', $postData)) {
                                $tier_price = $this->_jsonHelper->jsonEncode($postData['tier_price']);
                            }
                            if (array_key_exists('msrp', $postData)) {
                                $msrp = $postData['msrp'];
                            }
                            if (array_key_exists('msrp_display_actual_price_type', $postData)) {
                                $msrp_display_actual_price_type=$postData['msrp_display_actual_price_type'];
                            }
                            if (array_key_exists('price', $postData)) {
                                $price = $postData['price'];
                            }
                            if (array_key_exists('special_price', $postData)) {
                                $special_price = $postData['special_price'];
                            }
                            if (array_key_exists('special_from_date', $postData)) {
                                $special_from_date = $postData['special_from_date'];
                            }
                            if (array_key_exists('special_to_date', $postData)) {
                                $special_to_date = $postData['special_to_date'];
                            }
                            $pricingData = [
                                'entity_id'             => $entityID, 
                                'store_id'              => $storeId, 
                                'sku'                   => $sku,
                                'price'                 => $price,
                                'special_price'         =>  $special_price,
                                'special_from_date'     =>  $special_from_date, 
                                'special_to_date'       =>  $special_to_date,
                                'cost'=>$cost, 'msrp'   => $msrp,
                                'msrp_display_actual_price_type' =>  $msrp_display_actual_price_type,
                                'tier_price'    =>  $tier_price
                             ];
                        }
                    }
                }
                
                $availablePricing =$this->_pricing->getCollection()
                ->addFieldToFilter(
                    'store_id',
                    $storeId
                )->addFieldToFilter('entity_id', $entityID);
                $id = null;

                if ($availablePricing->getData()) {
                    foreach ($availablePricing as $currentPricing) {
                        $id=$currentPricing->getId();
                    }
                }
                
                if ($pricingData) {
                    $this->_pricing->setData($pricingData);

                    if ($id) {
                        $this->_pricing->setId($id);
                    }

                    $this->_pricing->save();
                }
            } catch (\Magento\Framework\Exception\LocalizedException $ex) {
                $this->_message->addError(__("Something is wrong".$ex->getMessage()));
            }
        }
        // @codingStandardsIgnoreLine
        return;
    }
}
