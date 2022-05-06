<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderCustomization\Ui\DataProvider\Supplier\Transaction\Form\Modifier;

/**
 * Class General
 *
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD)
 * @package Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\Invoice\Form\Modifier
 */
class GeneralForm extends \Magestore\PurchaseOrderSuccess\Ui\DataProvider\Modifier\AbstractModifier
{
    /**
     * @var string
     */
    protected $groupContainer = 'general';
    
    /**
     * @var string
     */
    protected $groupLabel = 'Add transaction to supplier';

    /**
     * @var string
     */
    protected $scopeName = 'os_supplier_transaction_form.os_supplier_transaction_form';
    /**
     * @var int
     */
    protected $sortOrder = 10;
    
    /**
     * @var \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface
     */

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magestore\PurchaseOrderCustomization\Model\Supplier\Transaction\Options\Type
     */
    protected $transactionTypeOptions;

    /**
     * @var \Magestore\PurchaseOrderCustomization\Model\Supplier\Transaction\Options\Currency
     */
    protected $currencyOptions;

    /**
     * @var \Magestore\PurchaseOrderCustomization\Model\Supplier\Transaction\Options\DescriptionOptions
     */
    protected $desOptions;

    /**
     * GeneralForm constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magestore\PurchaseOrderCustomization\Model\Supplier\Transaction\Options\Type $transactionTypeOptions
     * @param \Magestore\PurchaseOrderCustomization\Model\Supplier\Transaction\Options\Currency $currencyOptions
     * @param \Magestore\PurchaseOrderCustomization\Model\Supplier\Transaction\Options\DescriptionOptions $desOptions
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magestore\PurchaseOrderCustomization\Model\Supplier\Transaction\Options\Type $transactionTypeOptions,
        \Magestore\PurchaseOrderCustomization\Model\Supplier\Transaction\Options\Currency $currencyOptions,
        \Magestore\PurchaseOrderCustomization\Model\Supplier\Transaction\Options\DescriptionOptions $desOptions
    ) {
        parent::__construct($objectManager, $registry, $request, $urlBuilder);
        $this->dateTime = $dateTime;
        $this->timezone = $timezone;
        $this->transactionTypeOptions = $transactionTypeOptions;
        $this->currencyOptions = $currencyOptions;
        $this->desOptions = $desOptions;
    }
    
    /**
     * modify data
     *
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
        $meta = array_replace_recursive(
            $meta,
            [
                $this->groupContainer => [
                    'children' => $this->getGeneralChildren(),
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __($this->groupLabel),
                                'collapsible' => true,
                                'dataScope' => 'data',
                                'visible' => true,
                                'opened' => true,
                                'componentType' => \Magento\Ui\Component\Form\Fieldset::NAME,
                                'sortOrder' => 10
                            ],
                        ],
                    ],
                ],
            ]
        );

        return $meta;
    }
    
    /**
     * Add general form fields
     *
     * @return array
     */
    public function getGeneralChildren()
    {
        $children = [
            'supplier_transaction_id' => $this->addFormFieldText('', 'hidden', 10),
            'supplier_id' => $this->addFormFieldText('', 'hidden', 10),
            'transaction_created_date' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => \Magento\Ui\Component\Form\Field::NAME,
                            'component' => 'Magestore_PurchaseOrderCustomization/js/form/element/date',
                            'label' => __('Transaction created date'),
                            'dataType' => 'date',
                            'formElement' => 'date',
                            'sortOrder' => 10,
                            'dataScope' => 'transaction_created_date',
                            'validation' => ['required-entry' => true],
                            'default' => date("Y-m-d")
//                            'options' => ['dateFormat' => "DD/MM/YYYY"]
                        ],
                    ],
                ],
            ],
            'transaction_date' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => \Magento\Ui\Component\Form\Field::NAME,
                            'component' => 'Magestore_PurchaseOrderCustomization/js/form/element/date',
                            'label' => __('Transaction date'),
                            'dataType' => 'date',
                            'formElement' => 'date',
                            'sortOrder' => 20,
                            'dataScope' => 'transaction_date',
                            'validation' => ['required-entry' => true],
//                            'options' => ['dateFormat' => "DD/MM/YYYY"]
                        ],
                    ],
                ],
            ],
            'type' => $this->addFormFieldSelect(
                'Transaction Type', $this->transactionTypeOptions->toOptionArray(), 30, true
            ),
            'doc_no' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => \Magento\Ui\Component\Form\Field::NAME,
                            'label' => __('Doc No.'),
                            'dataType' => 'text',
                            'formElement' => 'input',
                            'sortOrder' => 40,
                            'validation' => ['required-entry' => true],
                            'dataScope' => 'doc_no'
                        ],
                    ],
                ],
            ],
            'chq_no' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => \Magento\Ui\Component\Form\Field::NAME,
                            'label' => __('Chq No.'),
                            'dataType' => 'text',
                            'formElement' => 'input',
                            'sortOrder' => 50,
                            'validation' => ['required-entry' => true],
                            'dataScope' => 'chq_no'
                        ],
                    ],
                ],
            ],
            'amount' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => \Magento\Ui\Component\Form\Field::NAME,
                            'label' => __('Amount'),
                            'dataType' => 'number',
                            'formElement' => 'input',
                            'sortOrder' => 60,
                            'validation' => [
                                'validate-number' => true,
                                'required-entry' => true
                            ],
                            'dataScope' => 'amount'
                        ],
                    ],
                ],
            ],
            'currency' => $this->addFormFieldSelect(
                'Currency', $this->currencyOptions->toOptionArray(), 70, true
            ),
            'description_option' => $this->addFormFieldSelect(
                'Description option', $this->desOptions->toOptionArray(), 80, true
            )
        ];

        return $children;
    }
}