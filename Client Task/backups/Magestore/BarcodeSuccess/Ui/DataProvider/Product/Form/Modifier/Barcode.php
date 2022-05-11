<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magestore\BarcodeSuccess\Ui\DataProvider\Product\Form\Modifier;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Form;
use Magento\Framework\View\Element\UiComponent\DataProvider\Sanitizer;

/**
 * Class Barcode
 *
 * Used to barcode modifier
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class Barcode extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier implements
    ModifierInterface
{
    /**
     * @var string
     */
    protected $_groupContainer = "barcode_labels";

    /**
     * @var string
     */
    protected $_groupLabel = "Barcode";

    /**
     * @var boolean
     */
    protected $_sortOrder = true;

    /**
     * Modifier Config
     *
     * @var array
     */
    protected $_modifierConfig = [
        'listing' => 'os_product_detail_barcode_listing',
        'columns_ids' => 'os_product_detail_barcode_listing.ids',
        'form' => 'os_product_detail_barcode_listing'
    ];

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Construct
     *
     * @param UrlInterface $urlBuilder
     * @param RequestInterface $request
     * @param ScopeConfigInterface $scopeConfig
     * @param array $modifierConfig
     */
    public function __construct(
        UrlInterface $urlBuilder,
        RequestInterface $request,
        ScopeConfigInterface $scopeConfig,
        array $modifierConfig = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
        $this->_modifierConfig = array_replace_recursive($this->_modifierConfig, $modifierConfig);
    }

    /**
     * Check Class Sanitizer exist method extractConfig
     *
     * @return boolean
     * @throws \Exception
     */
    private function isExistDisableTmpl()
    {
        try {
            if (class_exists(Sanitizer::class)) {
                $nameClass = Sanitizer::class;
                $nameMethod = 'extractConfig';
                return method_exists($nameClass, $nameMethod);
            } else {
                return false;
            }
        } catch (\Exception $error) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function modifyMeta(array $meta)
    {
        if ($this->scopeConfig->getValue('barcodesuccess/general/one_barcode_per_sku')) {
            return $meta;
        }
        $meta = array_replace_recursive(
            $meta,
            [
                $this->_groupContainer => [
                    'children' => $this->getModifierChildren(),
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __($this->_groupLabel),
                                'collapsible' => true,
                                'visible' => true,
                                'opened' => false,
                                'componentType' => Form\Fieldset::NAME,
                                'sortOrder' => $this->_sortOrder
                            ],
                        ],
                    ],
                ],
            ]
        );
        return $meta;
    }

    /**
     * Retrieve child meta configuration
     *
     * @return array
     * @throws \Exception
     */
    public function getModifierChildren()
    {
        $productId = $this->request->getParam('id', false);
        $children = [
            $this->_modifierConfig['listing'] => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'autoRender' => true,
                            'componentType' => 'insertListing',
                            'dataScope' => $this->_modifierConfig['listing'],
                            'externalProvider' =>
                                $this->_modifierConfig['listing']
                                . '.'
                                . $this->_modifierConfig['listing']
                                . '_data_source',
                            'selectionsProvider' =>
                                $this->_modifierConfig['listing']
                                . '.'
                                . $this->_modifierConfig['listing']
                                . '.'
                                . $this->_modifierConfig['columns_ids'],
                            'ns' => $this->_modifierConfig['listing'],
                            'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                            'realTimeLink' => true,
                            'provider' =>
                                $this->_modifierConfig['form']
                                . '.'
                                . $this->_modifierConfig['form']
                                . '_data_source',
                            'dataLinks' => ['imports' => false, 'exports' => true],
                            'behaviourType' => 'simple',
                            'externalFilterMode' => true,
                            'exports' => [
                                'storeId' => '${ $.externalProvider }:params.current_store_id',
                            ],
                            'params' => [
                                'product_id' => $productId
                            ]
                        ],
                    ],
                ]
            ]
        ];

        if ($this->isExistDisableTmpl()) {
            $children
                [$this->_modifierConfig['listing']]['arguments']['data']
                ['config']['exports']['__disableTmpl']['storeId'] = false;
        }

        return $children;
    }
}
