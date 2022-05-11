<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\Invoice\Form\Modifier;

use Magento\Ui\Component\Container;
use Magento\Ui\Component\Modal;
use Magento\Ui\Component\Form;
use Magento\Ui\Component\DynamicRows;
use Magento\Framework\View\Element\UiComponent\DataProvider\Sanitizer;

/**
 * Class PaymentList
 *
 * Used for payment list
 */
class PaymentList extends AbstractModifier
{
    /**
     * @var string
     */
    protected $groupContainer = 'payment_list';

    /**
     * @var string
     */
    protected $groupLabel = 'Payment List';

    /**
     * @var int
     */
    protected $sortOrder = 30;

    protected $children = [
        'button_set' => 'button_set',
        'invoice_payment_list_listing' => 'os_purchase_order_invoice_payment_listing',
        'register_payment_modal' => 'register_payment_modal',
        'register_payment_modal_form' => 'os_purchase_order_invoice_payment_form'
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
        if (!$this->getCurrentInvoice()) {
            return $meta;
        }
        $meta = array_replace_recursive(
            $meta,
            [
                $this->groupContainer => [
                    'children' => $this->getPaymentListChildren(),
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __($this->groupLabel),
                                'component' => 'Magestore_PurchaseOrderSuccess/js/form/components/fieldset',
                                'collapsible' => true,
                                'visible' => $this->getVisible(),
                                'opened' => false,
                                'componentType' => \Magento\Ui\Component\Form\Fieldset::NAME,
                                'sortOrder' => $this->getSortOrder(),
                                'actions' => [
                                    [
                                        'targetName' => $this->scopeName . '.' . $this->groupContainer . '.' .
                                            $this->children['invoice_payment_list_listing'],
                                        'actionName' => 'render',
                                    ]
                                ]
                            ]
                        ],
                    ],
                ],
            ]
        );
        return $meta;
    }

    /**
     * Add invoice payment form fields
     *
     * @return array
     */
    public function getPaymentListChildren()
    {
        $children = [];
        if ($this->getCurrentInvoice()->getTotalDue() > 0) {
            $children[$this->children['button_set']] = $this->getPaymentButtons();
        }
        $children[$this->children['invoice_payment_list_listing']] = $this->getInvoicePaymentListing();
        return $children;
    }

    /**
     * Get invoice payment buttons
     *
     * @return array
     */
    public function getPaymentButtons()
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
                'register_payment_button' => $this->addButton(
                    'Register Payment',
                    [
                        [
                            'targetName' => $this->scopeName . '.' . $this->groupContainer
                                . '.' . $this->children['button_set']
                                . '.' . $this->children['register_payment_modal'],
                            'actionName' => 'openModal'
                        ],
                        [
                        'targetName' => $this->scopeName . '.' . $this->groupContainer
                            . '.' . $this->children['button_set']
                            . '.' . $this->children['register_payment_modal']
                            . '.' . $this->children['register_payment_modal_form'],
                        'actionName' => 'render'
                        ]
                    ]
                ),
                $this->children['register_payment_modal'] => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Modal::NAME,
                                'type' => 'container',
                                'options' => [
                                    'onCancel' => 'actionCancel',
                                    'title' => __('Register Payment'),
                                    'buttons' => [
                                        [
                                            'text' => __('Cancel'),
                                            'actions' => ['closeModal']
                                        ],
                                        [
                                            'text' => __('Save Payment'),
                                            'class' => 'action-primary',
                                            'actions' => [
                                                [
                                                    'targetName' => $this->children['register_payment_modal_form']
                                                        . '.' . $this->children['register_payment_modal_form'],
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
                        $this->children['register_payment_modal_form'] => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'autoRender' => false,
                                        'componentType' => 'insertForm',
                                        'component' => 'Magestore_PurchaseOrderSuccess/js/form/components/insert-form',
                                        'ns' => $this->children['register_payment_modal_form'],
                                        'sortOrder' => '25',
                                        'params' => [
                                            'purchase_id' => $this->getCurrentInvoice()->getPurchaseOrderId(),
                                            'invoice_id' => $this->getCurrentInvoice()->getPurchaseOrderInvoiceId()
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Get invoice payment listing
     *
     * @return array
     * @throws \Exception
     */
    public function getInvoicePaymentListing()
    {
        $dataScope = 'invoice_payment_list_listing';
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
                            'invoice_id' => '${ $.provider }:data.purchase_order_invoice_id',
                            'purchase_id' => '${ $.provider }:data.purchase_order_id',
                        ],
                        'exports' => [
                            'invoice_id' => '${ $.externalProvider }:params.invoice_id',
                            'purchase_id' => '${ $.externalProvider }:params.purchase_id',
                        ],
                        'selectionsProvider' =>
                            $this->children[$dataScope]
                            . '.' . $this->children[$dataScope]
                            . '.purchase_order_invoice_payment_template_columns.ids'
                    ]
                ]
            ]
        ];

        if ($this->isExistDisableTmpl()) {
            $data['arguments']['data']['config']['imports']['__disableTmpl'] = [
                'invoice_id' => false,
                'purchase_id' => false
            ];

            $data['arguments']['data']['config']['exports']['__disableTmpl'] = [
                'invoice_id' => false,
                'purchase_id' => false
            ];
        }

        return $data;
    }
}
