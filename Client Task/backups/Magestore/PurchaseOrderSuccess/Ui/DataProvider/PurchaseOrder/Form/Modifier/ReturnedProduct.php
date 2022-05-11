<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\Form\Modifier;

use Magento\Ui\Component\Container;
use Magento\Ui\Component\Modal;
use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Status;
use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Type;
use Magento\Framework\View\Element\UiComponent\DataProvider\Sanitizer;

/**
 * Class ReturnedProduct
 *
 * Used for returned product
 */
class ReturnedProduct extends AbstractModifier
{
    /**
     * @var string
     */
    protected $groupContainer = 'returned_product';

    /**
     * @var string
     */
    protected $groupLabel = 'Returned Items';

    /**
     * @var int
     */
    protected $sortOrder = 50;

    /**
     * @var array
     */
    protected $children = [
        'returned_product_buttons' => 'returned_product_buttons',
        'returned_product_container' => 'returned_product_container',
        'returned_product_listing' => 'os_purchase_order_returned_product_listing',
        'returned_product_modal' => 'returned_product_modal',
        'returned_product_modal_form' => 'os_purchase_order_returned_product_form'
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
        if (!$this->getPurchaseOrderId() || $this->getCurrentPurchaseOrder()->getStatus() == Status::STATUS_PENDING ||
            ($this->getCurrentPurchaseOrder()->getType() == Type::TYPE_QUOTATION)) {
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
                                            $this->children['returned_product_container'],
                                        'actionName' => 'render',
                                    ],
                                ]
                            ],
                        ],
                    ],
                    'children' => $this->getReturnedProductChildren()
                ],
            ]
        );
        return $meta;
    }

    /**
     * Add returned form fields
     *
     * @return array
     */
    public function getReturnedProductChildren()
    {
        $purchaseOrder = $this->getCurrentPurchaseOrder();
        if ($purchaseOrder->getStatus() != Status::STATUS_CANCELED
            && $purchaseOrder->getTotalQtyReceived() > $purchaseOrder->getTotalQtyReturned() + $purchaseOrder->getTotalQtyTransferred()) { //phpcs:disable
            $children[$this->children['returned_product_buttons']] = $this->getReturnedProductButton();
        }
        $children[$this->children['returned_product_container']] = $this->getReturnedProductList();
        return $children;
    }

    /**
     * Get returned product button
     *
     * @return array
     */
    public function getReturnedProductButton()
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
                'returned_products' => $this->addButton(
                    'Return Products',
                    [
                        [
                            'targetName' => $this->scopeName . '.' . $this->groupContainer
                                . '.' . $this->children['returned_product_buttons']
                                . '.' . $this->children['returned_product_modal'],
                            'actionName' => 'openModal'
                        ], [
                        'targetName' => $this->scopeName . '.' . $this->groupContainer
                            . '.' . $this->children['returned_product_buttons']
                            . '.' . $this->children['returned_product_modal']
                            . '.' . $this->children['returned_product_modal_form'],
                        'actionName' => 'render'
                    ]
                    ]
                ),
                $this->children['returned_product_modal'] => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Modal::NAME,
                                'type' => 'container',
                                'options' => [
                                    'onCancel' => 'actionCancel',
                                    'title' => __('Returned Product'),
                                    'buttons' => [
                                        [
                                            'text' => __('Cancel'),
                                            'actions' => ['closeModal']
                                        ],
                                        [
                                            'text' => __('Save'),
                                            'class' => 'action-primary',
                                            'actions' => [
                                                [
                                                    'targetName' => $this->children['returned_product_modal_form']
                                                        . '.' . $this->children['returned_product_modal_form'],
                                                    'actionName' => 'save',
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'children' => [
                        $this->children['returned_product_modal_form'] => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'autoRender' => false,
                                        'componentType' => 'insertForm',
                                        'component' => 'Magestore_PurchaseOrderSuccess/js/form/components/insert-form',
                                        'ns' => $this->children['returned_product_modal_form'],
                                        'sortOrder' => '25',
                                        'params' => [
                                            'purchase_id' => $this->getPurchaseOrderId(),
                                            'supplier_id' => $this->getCurrentPurchaseOrder()->getSupplierId()
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];
    }

    /**
     * Get returned product list
     *
     * @return array
     * @throws \Exception
     */
    public function getReturnedProductList()
    {
        $dataScope = 'returned_product_listing';
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
                            . '.purchase_order_item_returned_template_columns.ids'
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
