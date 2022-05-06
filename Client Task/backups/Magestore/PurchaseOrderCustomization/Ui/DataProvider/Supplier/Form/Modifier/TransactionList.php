<?php
/**
 * Copyright © Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderCustomization\Ui\DataProvider\Supplier\Form\Modifier;

use Magento\Ui\Component\Container;
use Magento\Ui\Component\Modal;
use Magento\Ui\Component;

/**
 * Class Transaction
 *
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD)
 * @package Magestore\PurchaseOrderCustomization\Ui\DataProvider\Supplier\Transaction\Form\Modifier
 */
class TransactionList extends \Magestore\SupplierSuccess\Ui\DataProvider\Supplier\DataForm\Modifier\AbstractModifier
{
    /**
     * @var int $supplierId
     */
    protected $supplierId;

    /**
     * @var string
     */
    protected $groupContainer = 'transaction_list';
    
    /**
     * @var string
     */
    protected $groupLabel = 'Transaction List';

    /**
     * @var string
     */
    protected $scopeName = 'os_supplier_form.os_supplier_form';

    /**
     * @var array
     */
    protected $children = [
        'transaction_list_container' => 'transaction_list_container',
        'transaction_list_buttons' => 'transaction_list_buttons',
        'transaction_modal' => 'transaction_modal',
        'transaction_modal_form' => 'os_supplier_transaction_form',
        'transaction_list_listing' => 'os_supplier_transaction_listing',


        'edit_transaction_modal' => 'edit_transaction_modal',
        'edit_transaction_modal_form' => 'os_supplier_transaction_edit',

        'transaction_print_modal' => 'transaction_print_modal',
        'transaction_print_modal_form' => 'os_supplier_transaction_print_form'
    ];

    /**
     * @inheritdoc
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
        if(!$this->requestInterface->getParam('id')) {
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
                                'visible' => true,
                                'opened' => false,
                                'componentType' => \Magento\Ui\Component\Form\Fieldset::NAME,
                                'sortOrder' => 1000,
                                'actions' => [
                                    [
                                        'targetName' => $this->scopeName . '.' . $this->groupContainer . '.' .
                                            $this->children['transaction_list_container'],
                                        'actionName' => 'render',
                                    ],
                                ]
                            ],
                        ],
                    ],
                    'children' => $this->getTransactionChildren()
                ],
            ]
        );
        return $meta;
    }

    /**
     * Add invoice form fields
     *
     * @return array
     */
    public function getTransactionChildren()
    {
        $children[$this->children['transaction_list_buttons']] = $this->getAddNewTransactionButton();
        $children[$this->children['transaction_list_container']] = $this->getTransactionList();
        return $children;
    }

    /**
     * Get invoice button
     *
     * @return array
     */
    public function getAddNewTransactionButton()
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
                'print_transaction_button' => $this->addButton(
                    'Print',
                    [
                        [
                            'targetName' => $this->scopeName . '.' . $this->groupContainer
                                . '.' . $this->children['transaction_list_buttons']
                                . '.' . $this->children['transaction_print_modal'],
                            'actionName' => 'openModal'
                        ]
                    ]
                ),
                'add_transaction_button' => $this->addButton(
                    'Add Transaction',
                    [
                        [
                            'targetName' => $this->scopeName . '.' . $this->groupContainer
                                . '.' . $this->children['transaction_list_buttons']
                                . '.' . $this->children['transaction_modal'],
                            'actionName' => 'openModal'
                        ]
                    ]
                ),
                $this->children['transaction_modal'] => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Modal::NAME,
                                'type' => 'container',
                                'options' => [
                                    'onCancel' => 'actionCancel',
                                    'title' => __('Add transaction to supplier'),
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
                                                    'targetName' => $this->children['transaction_modal_form']
                                                        . '.' . $this->children['transaction_modal_form'],
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
                        $this->children['transaction_modal_form'] => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'autoRender' => true,
                                        'componentType' => 'insertForm',
                                        'ns' => $this->children['transaction_modal_form'],
                                        'sortOrder' => '25',
                                        'params' => [
                                            'supplier_id' => $this->getSupplierId()
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                $this->children['transaction_print_modal'] => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Modal::NAME,
                                'type' => 'container',
                                'options' => [
                                    'onCancel' => 'actionCancel',
                                    'title' => __('Period selection'),
                                    'buttons' => [
                                        [
                                            'text' => __('Cancel'),
                                            'actions' => ['closeModal']
                                        ],
                                        [
                                            'text' => __('Print'),
                                            'class' => 'action-primary',
                                            'actions' => [
                                                [
                                                    'targetName' => $this->children['transaction_print_modal_form']
                                                        . '.' . $this->children['transaction_print_modal_form'],
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
                        $this->children['transaction_print_modal_form'] => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'autoRender' => true,
                                        'componentType' => 'insertForm',
                                        'ns' => $this->children['transaction_print_modal_form'],
                                        'sortOrder' => '25',
                                        'params' => [
                                            'supplier_id' => $this->getSupplierId()
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                $this->children['edit_transaction_modal'] => $this->getEditTransactionModal()
//                $this->children['view_shipment_modal'] => $this->getViewShipmentModal(),
//                $this->children['view_shipment_complete_modal'] => $this->getViewShipmentCompleteModal()
            ]
        ];
    }

    /**
     * Get invoice list
     *
     * @return array
     */
    public function getTransactionList()
    {
        $dataScope = 'transaction_list_listing';
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'params' => [
                            'supplier_id' => $this->getSupplierId()
                        ],
                        'component' => 'Magestore_PurchaseOrderSuccess/js/form/components/insert-listing',
                        'autoRender' => true,
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
                            'supplier_id' => '${ $.provider }:data.supplier_id'
                        ],
                        'exports' => [
                            'supplier_id' => '${ $.externalProvider }:params.supplier_id'
                        ],
                        'selectionsProvider' =>
                            $this->children[$dataScope]
                            . '.' . $this->children[$dataScope]
                            . '.supplier_transaction_template_columns.ids'
                    ]
                ]
            ]
        ];
    }

    /**
     * Get supplier id
     *
     * @return int|mixed
     */
    public function getSupplierId()
    {
        if (!$this->supplierId) {
            $this->supplierId = $this->requestInterface->getParam('id');
        }

        return $this->supplierId;
    }

    /**
     * Returns Buttons Set configuration
     *
     * @return array
     * @throws \Exception
     */
    public function getTransactionListingButtons()
    {
        $data = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'container',
                        'componentType' => 'container',
                        'label' => false,
                        'template' => 'ui/form/components/complex'
                    ],
                ],
            ],
            'children' => [
                'add_product_button' => $this->getAddTransactionButtonMeta(),
            ],
        ];

        return $data;
    }

    /**
     * Get add transaction button meta
     *
     * @return array
     * @throws \Exception
     */
    public function getAddTransactionButtonMeta()
    {
        $data = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'container',
                        'componentType' => 'container',
                        'component' => 'Magento_Ui/js/form/components/button',
                        'actions' => [
                            [
                                'targetName' =>
                                    'os_supplier_form' . '.' . 'os_supplier_form'
                                    . '.'
                                    . 'supplier_transaction_listing_add',
                                'actionName' => 'openModal'
                            ],
                            [
                                'targetName' =>
                                    'os_supplier_form' . '.' . 'os_supplier_form'
                                    . '.'
                                    . 'supplier_transaction_listing_add'
                                    . '.'
                                    . 'os_supplier_transaction_modal_add_listing',
                                'actionName' => 'destroyInserted'
                            ],
                            [
                                'targetName' =>
                                    'os_supplier_form' . '.' . 'os_supplier_form'
                                    . '.'
                                    . 'supplier_transaction_listing_add'
                                    . '.'
                                    . 'os_supplier_transaction_modal_add_listing',
                                'actionName' => 'render'
                            ],
                        ],
                        'imports' => [
                            'supplier_id' => '${ $.provider }:data.supplier_id',
                        ],
                        'exports' => [
                            'supplier_id' => '${ $.externalProvider }:params.supplier_id',
                        ],
                        'title' => __('Add transaction to supplier'),
                        'provider' => null,
                    ],
                ],
            ],
        ];

        return $data;
    }

    /**
     * Get supplier products data
     *
     * @param array $meta
     * @return array
     */
    public function getSupplierTransactionAddModal(array $meta)
    {
        $meta['supplier_transaction_listing_add']['arguments']['data']['config'] = [
            'isTemplate' => false,
            'componentType' => Component\Modal::NAME,
            'type' => 'container',
            'dataScope' => '',
            'provider' => 'os_supplier_form.os_supplier_form_data_source',
            'options' => [
                'title' => __('Add products to supplier'),
                'buttons' => [
                    [
                        'text' => 'Cancel',
                        'actions' => [
                            [
                                'targetName' => '${ $.name }',
                                'actionName' => 'actionCancel'
                            ]
                        ]
                    ],
                    [
                        'text' => __('Save'),
                        'class' => 'action-primary',
                        'actions' => [
                            [
                                'targetName' => 'index = os_supplier_transaction_modal_add_listing',
                                'actionName' => 'save'
                            ],
//                            'closeModal'
                        ]
                    ]
                ],
            ],
        ];

        $meta['supplier_product_listing_add']['children'] = [
            'adding_button' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'additionalClasses' => 'admin__field-complex-attributes',
                            'formElement' => Container::NAME,
                            'componentType' => Container::NAME,
                            'content' => __('Select product(s) to add to this supplier'),
                            'label' => false,
                            'template' => 'ui/form/components/complex',
                        ],
                    ],
                ],
            ],
//            'os_supplier_product_modal_add_listing' => $this->getSupplierProductListingAddModalSelect()
        ];
        return $meta;
    }

    /**
     * Get edit transaction modal
     *
     * @return array
     */
    public function getEditTransactionModal()
    {
        $data = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Modal::NAME,
                        'type' => 'container',
                        'options' => [
                            'onCancel' => 'actionCancel',
                            'title' => __('Edit Transaction'),
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
                                            'targetName' => $this->children['edit_transaction_modal_form']
                                                . '.' . $this->children['edit_transaction_modal_form'],
                                            'actionName' => 'save',
                                        ],
                                    ],
                                ]
                            ],
                        ],
                    ],
                ],
            ],
            'children' => [
                $this->children['edit_transaction_modal_form'] => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender' => true,
                                'componentType' => 'insertForm',
                                'ns' => $this->children['edit_transaction_modal_form'],
                                'sortOrder' => '25'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $data;
    }
}
