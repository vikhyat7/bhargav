<?php

namespace Magestore\PurchaseOrderCustomization\Ui\Component\Listing\Columns\Supplier\Transaction;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * Class Actions
 *
 * @package Magestore\PoMultipleTracking\Ui\Component\Listing\Columns\PurchaseOrder\Shipment
 */
class Actions extends Column
{

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Actions constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $editCallback = $this->getData('config/editCallback');
            foreach ($dataSource['data']['items'] as &$item) {
                $idFieldName = $item['id_field_name'];
                if (isset($item[$idFieldName])) {
                    $buttonLabel = __('Edit');
                    $callback = $editCallback;
                    $name = $this->getData('name');
                    $item[$name]['edit'] = [
                        'label' => $buttonLabel ? __($buttonLabel) : __('Edit'),
                        'callback' => $callback
                    ];
                }
            }
        }

        return $dataSource;
    }
}
