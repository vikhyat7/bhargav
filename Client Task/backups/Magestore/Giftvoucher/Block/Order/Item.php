<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Block\Order;

use \Magento\Sales\Model\Order\Item as OrderItem;
use \Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;
use \Magento\Sales\Model\Order\Creditmemo\Item as CreditmemoItem;

/**
 * Giftvoucher Order Escape Item Block
 *
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Item extends \Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer
{
    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $_currencyFactory;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $_localeCurrency;

    /**
     * @var \Magestore\Giftvoucher\Model\Giftvoucher
     */
    protected $_giftvoucher;

    /**
     * @var \Magestore\Giftvoucher\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Item constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory
     * @param \Magestore\Giftvoucher\Helper\Data $helper
     * @param \Magestore\Giftvoucher\Model\Giftvoucher $giftvoucher
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory,
        \Magestore\Giftvoucher\Helper\Data $helper,
        \Magestore\Giftvoucher\Model\Giftvoucher $giftvoucher,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        array $data = []
    ) {
        $this->_helper = $helper;
        $this->_giftvoucher = $giftvoucher;
        $this->_currencyFactory = $currencyFactory;
        $this->_objectManager = $objectManager;
        $this->_localeCurrency = $localeCurrency;
        parent::__construct($context, $string, $productOptionFactory, $data);
    }

    /**
     * Get Gift Card item options in the order
     *
     * @return array
     * @throws \Zend_Currency_Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * phpcs:disable Generic.Metrics.NestingLevel
     */
    public function getItemOptions()
    {
        $result = parent::getItemOptions();
        $item = $this->getOrderItem();
        $cartType = $item->getGiftCardType();
        if ($item->getProductType() != 'giftvoucher') {
            return $result;
        }

        $templates = $this->_objectManager->create(\Magestore\Giftvoucher\Model\GiftTemplate::class)
                        ->getCollection()
                        ->addFieldToFilter('status', '1');

        if ($options = $item->getProductOptionByCode('info_buyRequest')) {
            foreach ($this->_helper->getGiftVoucherOptions() as $code => $label) {
                if (isset($options[$code]) && $options[$code]) {
                    if ($code == 'giftcard_template_id') {
                        foreach ($templates as $template) {
                            if ($template->getId() == $options[$code]) {
                                $valueTemplate = $template;
                            }
                        }
                        if ($cartType !=1) {
                            $result[] = [
                                'label' => $label,
                                'value' => $this->_escaper->escapeHtml($valueTemplate->getTemplateName()),
                                'option_value' => $this->_escaper->escapeHtml($valueTemplate->getTemplateName()),
                            ];
                        }
                    } else {
                        $result[] = [
                            'label' => $label,
                            'value' => $this->_escaper->escapeHtml($options[$code]),
                            'option_value' => $this->_escaper->escapeHtml($options[$code]),
                        ];
                    }
                }
            }
        }

        if ($item->getQuoteItemId()) {
            $giftVouchers = $this->_giftvoucher->getCollection()->addItemFilter($item->getQuoteItemId());
        } else {
            $giftVouchers = $this->_giftvoucher->getCollection()->addItemFilter($item->getId(), true);
        }
        if ($giftVouchers->getSize()) {
            $giftVouchersCode = [];
            foreach ($giftVouchers as $giftVoucher) {
                $balance = $this->_localeCurrency->getCurrency($giftVoucher->getCurrency())
                    ->toCurrency($giftVoucher->getBalance(), []);
                if ($giftVoucher->getSetId()>0) {
                    $giftVouchersCode[] = 'XXXXXXXXX'. ' (' . $balance . ') ';
                } else {
                    $giftVouchersCode[] = $giftVoucher->getGiftCode() . ' (' . $balance . ') ';
                }
            }
            $codes = implode('<br />', $giftVouchersCode);
            $result[] = [
                'label' => __('Gift Card Code'),
                'value' => $codes,
                'option_value' => $codes,
            ];
        }

        return $result;
    }

    /**
     * Get the html for item price
     *
     * @param OrderItem|InvoiceItem|CreditmemoItem $item
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getItemPrice($item)
    {
        $block = $this->getLayout()->getBlock('item_price');
        $block->setItem($item);
        return $block->toHtml();
    }
}
