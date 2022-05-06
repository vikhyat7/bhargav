<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Block\Adminhtml\Order\Item;

/**
 * Adminhtml Giftvoucher Order Item Name Block
 *
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Name extends \Magento\Sales\Block\Adminhtml\Items\Column\Name
{
    /**
     * @var \Magestore\Giftvoucher\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $currencyFactory;

    /**
     * @var \Magestore\Giftvoucher\Api\GiftTemplateRepositoryInterface
     */
    protected $giftTemplateRepository;

    /**
     * @var \Magestore\Giftvoucher\Api\GiftCode\GiftCodeManagementServiceInterface
     */
    protected $giftCodeManagementService;

    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\Product\OptionFactory $optionFactory
     * @param \Magestore\Giftvoucher\Helper\Data $helper
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magestore\Giftvoucher\Api\GiftTemplateRepositoryInterface $giftTemplateRepository
     * @param \Magestore\Giftvoucher\Api\GiftCode\GiftCodeManagementServiceInterface $giftCodeManagementService
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
        \Magestore\Giftvoucher\Helper\Data $helper,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magestore\Giftvoucher\Api\GiftTemplateRepositoryInterface $giftTemplateRepository,
        \Magestore\Giftvoucher\Api\GiftCode\GiftCodeManagementServiceInterface $giftCodeManagementService,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->currencyFactory = $currencyFactory;
        $this->giftTemplateRepository = $giftTemplateRepository;
        $this->giftCodeManagementService = $giftCodeManagementService;
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $optionFactory, $data);
    }

    /**
     * Get Order Options
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getOrderOptions()
    {
        $result = parent::getOrderOptions();
        $item = $this->getItem();

        if ($item->getProductType() != \Magestore\Giftvoucher\Model\Product\Type\Giftvoucher::GIFT_CARD_TYPE) {
            return $result;
        }

        if ($options = $item->getProductOptionByCode('info_buyRequest')) {
            foreach ($this->helper->getGiftVoucherOptions() as $code => $label) {
                if (isset($options[$code]) && $options[$code]) {
                    if ($code == 'giftcard_template_id') {
                        $giftTemplate = $this->giftTemplateRepository->getById($options[$code]);

                        $result[] = [
                            'label' => $label,
                            'value' => $this->_escaper->escapeHtml($giftTemplate->getTemplateName()),
                            'option_value' => $this->_escaper->escapeHtml($giftTemplate->getTemplateName()),
                        ];
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

        $giftVouchers = $this->giftCodeManagementService->getGiftCodesFromOrderItem($item);
        if (count($giftVouchers)) {
            $giftVouchersCode = [];
            foreach ($giftVouchers as $giftVoucher) {
                $currency = $this->currencyFactory->create()->load($giftVoucher->getCurrency());
                $balance = $giftVoucher->getBalance();
                if ($currency) {
                    $balance = $currency->format($balance, [], false);
                }
                $giftVouchersCode[] = $giftVoucher->getGiftCode() . ' (' . $balance . ') ';
            }
            $codes = implode(',', $giftVouchersCode);
            $result[] = [
                'label' => __('Gift Code'),
                'value' => $this->_escaper->escapeHtml($codes),
                'option_value' => $this->_escaper->escapeHtml($codes),
            ];
        }

        return $result;
    }
}
