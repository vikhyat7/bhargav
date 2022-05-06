<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderCustomization\Ui\DataProvider\Supplier\TransactionPrint\Form\Modifier;

/**
 * Class General
 *
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD)
 * @package Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\Invoice\Form\Modifier
 */
class Period extends \Magestore\PurchaseOrderSuccess\Ui\DataProvider\Modifier\AbstractModifier
{
    /**
     * @var string
     */
    protected $groupContainer = 'period';
    
    /**
     * @var string
     */
    protected $groupLabel = 'Select transaction date';

    /**
     * @var string
     */
    protected $scopeName = 'os_supplier_transaction_print_form.os_supplier_transaction_print_form';
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
            'supplier_id' => $this->addFormFieldText('', 'hidden', 10),
            'transaction_date_from' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => \Magento\Ui\Component\Form\Field::NAME,
                            'component' => 'Magestore_PurchaseOrderCustomization/js/form/element/date',
                            'label' => __('From'),
                            'dataType' => 'date',
                            'formElement' => 'date',
                            'sortOrder' => 10,
                            'dataScope' => 'transaction_date_from',
                            'validation' => ['required-entry' => true],
//                            'default' => date("m/d/Y")
                        ],
                    ],
                ],
            ],
            'transaction_date_to' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => \Magento\Ui\Component\Form\Field::NAME,
                            'component' => 'Magestore_PurchaseOrderCustomization/js/form/element/date',
                            'label' => __('To'),
                            'dataType' => 'date',
                            'formElement' => 'date',
                            'sortOrder' => 20,
                            'dataScope' => 'transaction_date_to',
                            'validation' => ['required-entry' => true],
//                            'default' => date("m/d/Y")
                        ],
                    ],
                ],
            ]
        ];

        return $children;
    }
}
