<?php
/**
 * @category   Mageants ShippingPerProduct
 * @package    Mageants_ShippingPerProduct
 * @copyright  Copyright (c) 2016 Mageants
 * @author     Mageants Team <support@mageants.com>
 */

namespace Mageants\ShippingPerProduct\Model\Carrier;
 
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;

class RockShippingModel extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    /**
     * @var $_code string
     */
    public $_code = 'rockshippingmodel';
    
    /**
     * @var $cart \Magento\Checkout\Model\Cart
     */
    private $cart;
    
    /**
     * @var $product \Magento\Catalog\Model\Product
     */
    private $product;

    /**
     * @var $logger \Psr\Log\LoggerInterface
     */
    private $logger;
 
    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Catalog\Model\Product $product
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Model\Product $product,
        \Magento\Sales\Model\AdminOrder\Create  $OrderCreate,
        \Magento\Framework\App\State $appState,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->cart=$cart;
        $this->product=$product;
        $this->logger=$logger;
        $this->_orderCreate = $OrderCreate;
        $this->_appState = $appState;
        $this->_productloader = $_productloader;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }
 
    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return ['rockshippingmodel' => $this->getConfigData('name')];
    }
 
    /**
     * @param RateRequest $request
     * @return bool|Result
     */
    public function collectRates(RateRequest $request = null)
    {
        try{
            if (!$this->getConfigFlag('active')) {
                return false;
            }
     
            /**
             * @var \Magento\Shipping\Model\Rate\Result $result
             */
            $result = $this->_rateResultFactory->create();
     
            /**
             * @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method
             */
            $method = $this->_rateMethodFactory->create();
     
            $method->setCarrier('rockshippingmodel');
            $method->setCarrierTitle($this->getConfigData('title'));
     
            $method->setMethod('rockshippingmodel');
            $method->setMethodTitle($this->getConfigData('name'));
            $maxProductPrice=0;
            
            if( ($this->_appState->getAreaCode() == 'adminhtml') || ($this->_appState->getAreaCode() == 'admin') ) {
                $items =  $this->_orderCreate->getQuote()->getAllItems();
            } else {
                $items=$this->cart->getQuote()->getAllItems();
            }
            $shippingtotal=0;
            $itemqty=1;
            $isRuleForProduct=0;
            $highRate = [];
            foreach ($items as $item) {
                if ($item->getProductType()=="simple") {
                    $item_product=$item->getProduct();
                    $itemproduct = $this->getLoadProduct($item_product->getId());
                    
                    if (($itemproduct->getEnableShippingPerProduct()) && ($itemproduct->getShippingForProduct() > 0)) {
                        if ($itemqty == 1) {
                            $itemqty=$item->getQty();
                        }

                        if ($this->getConfigData('cal_item_separately') == 1) {
                            $shippingtotal+=($itemqty*$itemproduct->getShippingForProduct());
                        } else {
                            if ($itemproduct->getCalculateShipping()==1) {
                                $shippingtotal+=$itemproduct->getShippingForProduct();
                            } else {
                                $shippingtotal+=($itemqty*$itemproduct->getShippingForProduct());
                            }
                        }

                        if ($maxProductPrice < $itemproduct->getShippingForProduct()) {
                            // echo 'if';
                            $maxProductPrice = $itemproduct->getShippingForProduct();
                        }
                        $highRate[] = $itemproduct->getShippingForProduct();
                        $maxProductPrice = 15;

                        $isRuleForProduct=1;
                        
                        $itemqty=1;
                    } else {
                        if ($itemqty == 1) {
                            $itemqty=$item->getQty();
                        }

                        if ($this->getConfigData('use_default_rate') == 1) {
                            if ($this->getConfigData('default_rate_per_item') > 0
                                && $this->getConfigData('default_rate_per_item') != "") {
                                if ($this->getConfigData('cal_item_separately') == 1) {
                                    $shippingtotal+=($itemqty*$this->getConfigData('default_rate_per_item'));
                                } else {
                                    $shippingtotal+=$this->getConfigData('default_rate_per_item');
                                }
                            }
                        }

                        $itemqty=1;
                    }
                } else {
                    $itemqty=$item->getQty();
                }
            }

            if ($this->getConfigData('show_if_ind_item_apply_rule') == 1) {
                if ($isRuleForProduct == 0) {
                    return false;
                }
            }
            
            /**
             * you can fetch shipping price from different sources over some APIs,
             * we used price from config.xml - xml node price
             */
// $shippingtotal = 7.2;
// $maxProductPrice = 7.2000;//exit;
            // echo "<script>alert('test');</script>";
            if ($this->getConfigData('use_high_prod_rate_whole_order') == 1) {
                if (!empty($highRate)) {
                    $shippingtotal = max($highRate);
                }
            }
            
            if ($this->getConfigData('min_value') != "" && $this->getConfigData('min_value') > 0) {
                if ($this->getConfigData('min_value') > $shippingtotal) {
                    $shippingtotal=$this->getConfigData('min_value');
                }
            }

            if ($this->getConfigData('max_value') != "" && $this->getConfigData('max_value') > 0) {
                if ($this->getConfigData('max_value') < $shippingtotal) {
                    $shippingtotal=$this->getConfigData('max_value');
                }
            }

            $amount=$shippingtotal;

            if ($this->getConfigData('handling_type')=="P"
                && $this->getConfigData('handling_fee')>0
                && $this->getConfigData('handling_fee')!="") {
                if ($shippingtotal>0) {
                    $amount = (($shippingtotal*$this->getConfigData('handling_fee'))/100)+$shippingtotal+$this->getConfigData('price');
                }
            } else {
                $amount = $this->getConfigData('handling_fee')+$shippingtotal+$this->getConfigData('price');
            }

            $method->setPrice($amount);
            $method->setCost($amount);
     
            $result->append($method);
     
            return $result;
        }
        catch(\Exception $ex){
            $this->logger->addError($ex->getMessage());
            return false;
        }
    }

    public function getProduct($id = '')
    {
        if ($id!=='') {
            return $this->product->load($id);
        }
        return null;
    }

    public function getLoadProduct($id = '')
    {
        if ($id!=='') {
            return $this->_productloader->create()->load($id);
        }
        return null;   
    }
}
