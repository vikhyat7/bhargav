<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\Product\Type;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Giftvoucher Product Type Model
 *
 * @module   Giftvoucher
 * @author   Magestore Developer
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Giftvoucher extends \Magento\Catalog\Model\Product\Type\AbstractType
{
    const GIFT_CARD_TYPE = 'giftvoucher';

    /**
     * If product can be configured
     *
     * @var bool
     */
    protected $_canConfigure = true;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\MediaStorage\Helper\File\Storage\Database
     */
    protected $_fileStorageDb;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * Catalog product type
     *
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $_catalogProductType;

    /**
     * Eav config
     *
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * Catalog product option
     *
     * @var \Magento\Catalog\Model\Product\Option
     */
    protected $_catalogProductOption;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Object Manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Tax data
     *
     * @var \Magento\Tax\Helper\Data
     */
    protected $_taxData;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_catalogHelper;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $currencyFactory;

    /**
     * @var \Magestore\Giftvoucher\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magestore\Giftvoucher\Helper\Giftproduct
     */
    protected $giftProductHelper;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productModel;

    /**
     * Giftvoucher constructor.
     *
     * @param \Magento\Catalog\Model\Product\Option $catalogProductOption
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Catalog\Model\Product\Type $catalogProductType
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Psr\Log\LoggerInterface $logger
     * @param ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Tax\Helper\Data $taxHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Directory\Model\CurrencyFactory $currency
     * @param \Magestore\Giftvoucher\Helper\Data $helperData
     * @param \Magestore\Giftvoucher\Helper\Giftproduct $giftproduct
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Catalog\Model\ProductRepository $productModel
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Catalog\Model\Product\Option $catalogProductOption,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\Product\Type $catalogProductType,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Registry $coreRegistry,
        \Psr\Log\LoggerInterface $logger,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Tax\Helper\Data $taxHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Directory\Model\CurrencyFactory $currency,
        \Magestore\Giftvoucher\Helper\Data $helperData,
        \Magestore\Giftvoucher\Helper\Giftproduct $giftproduct,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Catalog\Model\ProductRepository $productModel
    ) {
        $this->_catalogProductOption = $catalogProductOption;
        $this->_eavConfig = $eavConfig;
        $this->_catalogProductType = $catalogProductType;
        $this->_coreRegistry = $coreRegistry;
        $this->_eventManager = $eventManager;
        $this->_fileStorageDb = $fileStorageDb;
        $this->_filesystem = $filesystem;
        $this->_logger = $logger;
        $this->productRepository = $productRepository;
        $this->_objectManager = $objectManager;
        $this->_taxData = $taxHelper;
        $this->_storeManager = $storeManager;
        $this->_catalogHelper = $catalogData;
        $this->priceCurrency = $priceCurrency;
        $this->currencyFactory = $currency;
        $this->helperData = $helperData;
        $this->giftProductHelper = $giftproduct;
        $this->productModel = $productModel;

        parent::__construct(
            $catalogProductOption,
            $eavConfig,
            $catalogProductType,
            $eventManager,
            $fileStorageDb,
            $filesystem,
            $coreRegistry,
            $logger,
            $productRepository
        );
    }

    /**
     * Initialize product(s) for add to cart process
     *
     * @param \Magento\Framework\DataObject $buyRequest
     * @param \Magestore\Giftvoucher\Model\Product $product
     *
     * @return array|\Magento\Framework\Phrase|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function prepareForCart(\Magento\Framework\DataObject $buyRequest, $product)
    {
        $result = parent::prepareForCart($buyRequest, $product);
        if (is_string($result)) {
            return $result;
        }
        reset($result);
        $product = current($result);
        $result = $this->_prepareGiftVoucher($buyRequest, $product, null);
        return $result;
    }

    /**
     * Prepare product and its configuration to be added to some products list.
     *
     * Perform standard preparation process and then prepare options belonging to specific product type.
     *
     * @param  \Magento\Framework\DataObject $buyRequest
     * @param  \Magestore\Giftvoucher\Model\Product $product
     * @param  string $processMode
     *
     * @return array|\Magento\Framework\Phrase|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _prepareProduct(\Magento\Framework\DataObject $buyRequest, $product, $processMode)
    {
        if ($product->getGiftCardType() == \Magestore\Giftvoucher\Model\Source\GiftCardTypeOptions::TYPE_PHYSICAL) {
            $buyRequest->unsetData('giftcard_template_id');
        }
        if (!$buyRequest->getData('send_friend')) {
            $fields = [
                'recipient_name',
                'recipient_email',
                'message',
                'day_to_send',
                'timezone_to_send',
                'recipient_address',
                'notify_success'
            ];
            foreach ($fields as $field) {
                if ($buyRequest->getData($field)) {
                    $buyRequest->unsetData($field);
                }
            }
        }

        $result = parent::_prepareProduct($buyRequest, $product, $processMode);
        if (is_string($result)) {
            return $result;
        }
        reset($result);
        $product = current($result);
        $result = $this->_prepareGiftVoucher($buyRequest, $product, $processMode);
        return $result;
    }

    /**
     * Prepare Gift Voucher
     *
     * @param \Magento\Framework\DataObject $buyRequest
     * @param \Magestore\Giftvoucher\Model\Product $product
     * @param null|string $processMode
     *
     * @return array|\Magento\Framework\Phrase
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     *
     * phpcs:disable Generic.Metrics.NestingLevel
     */
    public function _prepareGiftVoucher(\Magento\Framework\DataObject $buyRequest, $product, $processMode = null)
    {
        $store = $this->_storeManager->getStore();
        $amount = $buyRequest->getAmount();
        $baseCurrencyCode = $store->getBaseCurrencyCode();
        $currentCurrencyCode = $store->getCurrentCurrencyCode();
        $baseCurrency = $this->currencyFactory->create()->load($baseCurrencyCode);
        $currentCurrency = $this->currencyFactory->create()->load($currentCurrencyCode);
        $baseValue = $amount;
        $fnPrice = 0;
        $giftAmount = $this->giftProductHelper->getGiftValue($product);
        if ($amount || !$this->_isStrictProcessMode($processMode)) {
            if (is_object($product)) {
                switch ($giftAmount['type']) {
                    case 'range':
                        if ($amount < $this->convertPrice($product, $giftAmount['from'])) {
                            $amount = $this->convertPrice($product, $giftAmount['from']);
                            $baseValue = $giftAmount['from'];
                        } elseif ($amount > $this->convertPrice($product, $giftAmount['to'])) {
                            $amount = $this->convertPrice($product, $giftAmount['to']);
                            $baseValue = $giftAmount['to'];
                        } else {
                            $baseCurrencyCode = $store->getBaseCurrencyCode();
                            $currentCurrencyCode = $store->getCurrentCurrencyCode();

                            $baseCurrency = $this->currencyFactory->create()->load($baseCurrencyCode);
                            $currentCurrency = $this->currencyFactory->create()->load($currentCurrencyCode);

                            // convert price from current currency to base currency
                            if ($amount > 0) {
                                $amount = $amount * $amount / $baseCurrency->convert($amount, $currentCurrency);
                                $baseValue = $amount;
                            } else {
                                $amount = 0;
                                $baseValue = 0;
                            }
                        }

                        $fnPrice = $amount;
                        if ($giftAmount['gift_price_type'] == 'percent') {
                            $fnPrice = $fnPrice * $giftAmount['gift_price_options'] / 100;
                        }
                        break;
                    case 'dropdown':
                        if (!empty($giftAmount['options'])) {
                            $check = false;
                            $giftDropdown = [];
                            $giftAmountOptionsLength = count($giftAmount['options']);
                            for ($i = 0; $i < $giftAmountOptionsLength; $i++) {
                                $giftDropdown[$i] = $this->convertPrice($product, $giftAmount['options'][$i]);
                                if ($amount == $giftDropdown[$i]) {
                                    $check = true;
                                    $baseValue = $giftAmount['options'][$i];
                                }
                            }
                            if (!$check) {
                                $amount = $giftAmount['options'][0];
                                $baseValue = $giftAmount['options'][0];
                                $amount = round($baseCurrency->convert($amount, $currentCurrency), 4);
                            }
                            $fnPrices = array_combine($giftDropdown, $giftAmount['prices']);
                            $fnPrice = $fnPrices[(string)$amount];
                        }
                        break;
                    case 'static':
                        if ($amount != $this->convertPrice($product, $giftAmount['value'])) {
                            $amount = $giftAmount['value'];
                        }
                        $baseValue = $giftAmount['value'];
                        $fnPrice = $giftAmount['gift_price'];
                        break;
                    default:
                        return __('Please enter Gift Card information.');
                }
            } else {
                return __('Please specify product\'s required option(s).');
            }
        } else {
            //return __('Please enter Gift Card information.');
            $amount = $product->getPrice();
            $baseValue = $product->getPrice();
        }
        $buyRequest->setAmount((string) $amount);
        $product->addCustomOption('base_gc_value', (string) $baseValue);
        $product->addCustomOption('base_gc_currency', $store->getBaseCurrencyCode());
        $product->addCustomOption('gc_product_type', $giftAmount['type']);
        $product->addCustomOption('price_amount', $this->priceCurrency->round($fnPrice));

        $fullOptions = $this->helperData->getFullGiftVoucherOptions();
        foreach (array_keys($fullOptions) as $key) {
            if ($value = $buyRequest->getData($key)) {
                $product->addCustomOption($key, $value);
            }
        }
        if (!$this->_coreRegistry->registry('giftvoucher_product_' . $product->getId())) {
            $this->_coreRegistry->register('giftvoucher_product_' . $product->getId(), $product);
        }
        return [$product];
    }

    /**
     * Check is virtual product
     *
     * @param \Magestore\Giftvoucher\Model\Product $product
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isVirtual($product)
    {
        $isPostoffice = $this->helperData->getInterfaceConfig('postoffice', $product->getStoreId());
        if (!$isPostoffice) {
            return true;
        }
        $productData = $this->productModel->getById($product->getId());
        $giftCardType = $productData->getAttributeText('gift_card_type');
        $productOption = $product->getCustomOption('recipient_ship');
        if ($giftCardType == 'Physical') {
            return false;
        }
        if (!$productOption) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if product has required options
     *
     * @param \Magestore\Giftvoucher\Model\Product $product
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function hasRequiredOptions($product)
    {
        return true;
    }

    /**
     * Check if product can be configured
     *
     * @param \Magestore\Giftvoucher\Model\Product $product
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function canConfigure($product)
    {
        return true;
    }

    /**
     * Convert Price
     *
     * @param \Magestore\Giftvoucher\Model\Product $product
     * @param float $price
     * @return float
     */
    public function convertPrice($product, $price)
    {
        $includeTax = ( $this->_taxData->getPriceDisplayType() != 1 );
        $priceWithTax = $this->_catalogHelper->getTaxPrice($product, $price, $includeTax);

        return $this->priceCurrency->convert($priceWithTax);
    }

    /**
     * Delete data specific for Giftvoucher product type
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function deleteTypeSpecificData(\Magento\Catalog\Model\Product $product)
    {
        return $this;
    }
}
