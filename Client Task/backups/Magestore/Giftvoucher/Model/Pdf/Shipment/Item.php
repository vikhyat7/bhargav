<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Model\Pdf\Shipment;

/**
 * Giftvoucher Pdf Shipment Item Model
 *
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Item extends \Magento\Sales\Model\Order\Pdf\Items\AbstractItems
{
    /**
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    protected $string;

    /**
     * @var \Magestore\Giftvoucher\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magestore\Giftvoucher\Model\Giftvoucher
     */
    protected $_giftvoucher;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $_currencyFactory;

    /**
     * Item constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filter\FilterManager $filterManager
     * @param \Magento\Framework\Stdlib\StringUtils $coreString
     * @param \Magestore\Giftvoucher\Helper\Data $helper
     * @param \Magestore\Giftvoucher\Model\Giftvoucher $giftvoucher
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Magento\Framework\Stdlib\StringUtils $coreString,
        \Magestore\Giftvoucher\Helper\Data $helper,
        \Magestore\Giftvoucher\Model\Giftvoucher $giftvoucher,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->string = $coreString;
        $this->_helper = $helper;
        $this->_giftvoucher = $giftvoucher;
        $this->_currencyFactory = $currencyFactory;
        parent::__construct(
            $context,
            $registry,
            $taxData,
            $filesystem,
            $filterManager,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * @inheritDoc
     */
    public function draw()
    {
        $item = $this->getItem();
        $pdf = $this->getPdf();
        $page = $this->getPage();
        $lines = [];

        // draw Product name
        $lines[0] = [['text' => $this->string->split($item->getName(), 35, true, true), 'feed' => 100]];

        // draw QTY
        $lines[0][] = ['text' => $item->getQty() * 1, 'feed' => 35];

        // draw SKU
        $lines[0][] = [
            'text' => $this->string->split($this->getSku($item), 17),
            'feed' => 565,
            'align' => 'right',
        ];

        // custom options
        $options = $this->getItemOptions();
        if ($options) {
            foreach ($options as $option) {
                // draw options label
                $lines[][] = [
                    'text' => $this->string->split($this->filterManager->stripTags($option['label']), 40, true, true),
                    'font' => 'italic',
                    'feed' => 100,
                ];

                if ($option['value']) {
                    if (isset($option['print_value'])) {
                        $printValue = $option['print_value'];
                    } else {
                        $printValue = $this->filterManager->stripTags($option['value']);
                    }
                    $values = explode(', ', $printValue);
                    foreach ($values as $value) {
                        $lines[][] = ['text' => $this->string->split($value, 30, true, true), 'feed' => 100];
                    }
                }
            }
        }
        $lineBlock = ['lines' => $lines, 'height' => 20];

        $page = $pdf->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $this->setPage($page);
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getItemOptions()
    {
        $result = parent::getItemOptions();
        $item = $this->getItem()->getOrderItem();
        if ($item->getProductType() != 'giftvoucher') {
            return $result;
        }
        if ($options = $item->getProductOptionByCode('info_buyRequest')) {
            foreach ($this->_helper->getGiftVoucherOptions() as $code => $label) {
                if (isset($options[$code]) && $options[$code]) {
                    $result[] = [
                        'label' => $label,
                        'value' => $options[$code],
                        'print_value' => $options[$code],
                    ];
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
                $currency = $this->_currencyFactory->create()->load($giftVoucher->getCurrency());
                $balance = $giftVoucher->getBalance();
                if ($currency) {
                    $balance = $currency->format($balance, [], false);
                }
                $giftVouchersCode[] = $giftVoucher->getGiftCode() . ' (' . $balance . ') ';
            }
            $codes = implode(' ', $giftVouchersCode);
            $result[] = [
                'label' => __('Gift Card Code'),
                'value' => $codes,
                'print_value' => $codes,
            ];
        }

        return $result;
    }
}
