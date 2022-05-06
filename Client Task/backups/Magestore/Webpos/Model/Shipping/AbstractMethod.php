<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Shipping;

use Magento\Quote\Model\Quote\Address\RateRequest;

/**
 * class \Magestore\Webpos\Model\Shipping\AbstractMethod
 *
 * Web POS shipping abstract model
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
abstract class AbstractMethod extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements \Magento\Shipping\Model\Carrier\CarrierInterface
{

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $rateMethodFactory;
    /**
     * Carrier's code
     *
     * @var string
     */
    protected $_code = 'webpos_shipping';

    /**
     * Method's code
     *
     * @var string
     */
    protected $method_code = '';

    /**
     * Request object
     *
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request = '';

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \Magento\Framework\App\Request\Http $request
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Framework\App\Request\Http $request,
        array $data = []
    ) {
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->request = $request;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     *
     * @param RateRequest $request
     * @return \Magento\Shipping\Model\Rate\Result
     */
    public function collectRates(RateRequest $request)
    {
        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->rateResultFactory->create();
        $result->append($this->_getRate());
        return $result;
    }

    /**
     *
     * @return \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    public function _getRate()
    {
        $rate = $this->rateMethodFactory->create();
        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle($this->getConfigData('title'));
        $rate->setMethod($this->method_code);
        $rate->setMethodTitle($this->getConfigData('name'));
        $rate->setPrice($this->getConfigData('price'));
        $rate->setCost($this->getConfigData('price'));

        return $rate;
    }

    /**
     * Enable for Web POS
     * @return bool
     */
    public function isTrackingAvailable()
    {
        return true;
    }

    /**
     * Enable method for Web POS only
     * @return bool
     */
    public function isActive()
    {
        return true;
    }

    /**
     * Enable method for Web POS only
     * @return bool
     */
    public function checkAvailableShipCountries(\Magento\Framework\DataObject $request)
    {
        $active = $this->getConfigData('active');
        $isWebpos = $this->request->getParam(\Magestore\WebposIntegration\Controller\Rest\RequestProcessor::SESSION_PARAM_KEY);
        if ($isWebpos && ($active == 1 || $active == 'true')){
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getAllowedMethods() {
        return [];
    }
}
