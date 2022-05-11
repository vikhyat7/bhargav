<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\Form\Modifier;

use Magento\Ui\Component\Container;
use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Status;
use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Type;
use Magento\Framework\View\Element\UiComponent\DataProvider\Sanitizer;

/**
 * Class ShortfallProduct
 *
 * Used for short fall product
 */
class ShortfallProduct extends AbstractModifier
{
    /**
     * @var string
     */
    protected $groupContainer = 'shortfall_product';

    /**
     * @var string
     */
    protected $groupLabel = 'Shortfall Items';

    /**
     * @var int
     */
    protected $sortOrder = 40;

    /**
     * @var array
     */
    protected $children = [
        'shortfall_product_container' => 'shortfall_product_container',
        'shortfall_product_listing' => 'os_purchase_order_shortfall_product_listing'
    ];

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
     * Modify data
     *
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * Modify purchase order form meta
     *
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        if (!$this->getPurchaseOrderId()
            || $this->getCurrentPurchaseOrder()->getType() == Type::TYPE_QUOTATION
            || !in_array($this->getCurrentPurchaseOrder()->getStatus(), [Status::STATUS_COMPLETED])) {
            return $meta;
        }
        $meta = array_replace_recursive(
            $meta,
            [
                $this->groupContainer => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __($this->groupLabel),
                                'component' => 'Magestore_PurchaseOrderSuccess/js/form/components/fieldset',
                                'collapsible' => true,
                                'dataScope' => 'data',
                                'visible' => $this->getVisible(),
                                'opened' => $this->getOpened(),
                                'componentType' => \Magento\Ui\Component\Form\Fieldset::NAME,
                                'sortOrder' => $this->getSortOrder(),
                                'actions' => [
                                    [
                                        'targetName' => $this->scopeName . '.' . $this->groupContainer . '.' .
                                            $this->children['shortfall_product_container'],
                                        'actionName' => 'render',
                                    ],
                                ]
                            ],
                        ],
                    ],
                    'children' => $this->getShortfallProductChildren()
                ],
            ]
        );
        return $meta;
    }

    /**
     * Add shortfall product form fields
     *
     * @return array
     */
    public function getShortfallProductChildren()
    {
        $children[$this->children['shortfall_product_container']] = $this->getShortfallProductList();
        return $children;
    }

    /**
     * Get shortfall product button
     *
     * @return array
     */
    public function getShortfallProductButton()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => Container::NAME,
                        'componentType' => Container::NAME,
                        'label' => false,
                        'template' => 'Magestore_PurchaseOrderSuccess/form/components/button-list',
                    ],
                ],
            ],
            'children' => [
                'shortfall_products' => $this->addButton(
                    'Transfer Product to Warehouse',
                    []
                )
            ],
        ];
    }

    /**
     * Get shortfall product list
     *
     * @return array
     */
    public function getShortfallProductList()
    {
        $dataScope = 'shortfall_product_listing';
        $data = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'component' => 'Magestore_PurchaseOrderSuccess/js/form/components/insert-listing',
                        'autoRender' => false,
                        'componentType' => 'insertListing',
                        'dataScope' => $this->children[$dataScope],
                        'externalProvider' => $this->children[$dataScope] . '.' . $this->children[$dataScope]
                            . '_data_source',
                        'ns' => $this->children[$dataScope],
                        'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                        'realTimeLink' => true,
                        'dataLinks' => [
                            'imports' => false,
                            'exports' => true
                        ],
                        'behaviourType' => 'simple',
                        'externalFilterMode' => true,
                        'imports' => [
                            'supplier_id' => '${ $.provider }:data.supplier_id',
                            'purchase_id' => '${ $.provider }:data.purchase_order_id',
                        ],
                        'exports' => [
                            'supplier_id' => '${ $.externalProvider }:params.supplier_id',
                            'purchase_id' => '${ $.externalProvider }:params.purchase_id',
                        ],
                        'selectionsProvider' =>
                            $this->children[$dataScope]
                            . '.' . $this->children[$dataScope]
                            . '.purchase_order_item_shortfall_template_columns.ids'
                    ]
                ]
            ]
        ];

        if ($this->isExistDisableTmpl()) {
            $data['arguments']['data']['config']['imports']['__disableTmpl'] = [
                'supplier_id' => false,
                'purchase_id' => false
            ];

            $data['arguments']['data']['config']['exports']['__disableTmpl'] = [
                'supplier_id' => false,
                'purchase_id' => false
            ];
        }

        return $data;
    }
}
