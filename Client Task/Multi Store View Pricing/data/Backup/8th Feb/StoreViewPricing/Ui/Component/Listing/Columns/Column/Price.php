<?php
/**
 * @category Mageants StoreViewPricing
 * @package Mageants_StoreViewPricing
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\StoreViewPricing\Ui\Component\Listing\Columns\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * @api
 * @since 100.0.2
 */
class Price extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Column name
     */
    const NAME = 'column.price';

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    public $localeCurrency;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->localeCurrency = $localeCurrency;
        $this->storeManager = $storeManager;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $store_id = \Magento\Store\Model\Store::DEFAULT_STORE_ID;

        $filterParams =$this->context->getFiltersParams();
        if (isset($filterParams['store_id'])) {
            $store_id = $filterParams['store_id'];
        }

        if (isset($dataSource['data']['items'])) {
            $store = $this->storeManager->getStore(
                $this->context->getFilterParam('store_id', \Magento\Store\Model\Store::DEFAULT_STORE_ID)
            );
            $currency = $this->localeCurrency->getCurrency($store->getBaseCurrencyCode());
            $fieldName = $this->getData('name');
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['entity_id'])) {
                    // @codingStandardsIgnoreLine
                    $productCollection= $objectManager->create(
                        \Mageants\StoreViewPricing\Model\Pricing::class
                    )->getCollection()->addFieldToFilter('entity_id', $item['entity_id'])
                                ->addFieldToFilter('store_id', $store_id);
                    if (empty($productCollection->getData())) {
                        // @codingStandardsIgnoreLine
                        $productCollection= $objectManager->create(
                            \Mageants\StoreViewPricing\Model\Pricing::class
                        )->getCollection()->addFieldToFilter('entity_id', $item['entity_id'])
                                    ->addFieldToFilter('store_id', 0);
                    }
                    // @codingStandardsIgnoreLine
                    if ($productCollection->getFirstItem()->getPrice() != "" && $productCollection->getFirstItem()->getPrice() != 0) {
                        // @codingStandardsIgnoreLine
                        $item[$fieldName] = $productCollection->getFirstItem()->getPrice();
                    }
                    if (isset($item[$fieldName])) {
                        $item[$fieldName] = $currency->toCurrency(sprintf("%f", $item[$fieldName]));
                    }
                }
            }
        }

        return $dataSource;
    }
}
