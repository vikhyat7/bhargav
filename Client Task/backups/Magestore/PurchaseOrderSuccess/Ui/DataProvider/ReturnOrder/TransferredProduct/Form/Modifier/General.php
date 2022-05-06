<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Ui\DataProvider\ReturnOrder\TransferredProduct\Form\Modifier;

/**
 * Class General
 * @package Magestore\PurchaseOrderSuccess\Ui\DataProvider\ReturnOrder\TransferredProduct\Form\Modifier
 */
class General extends AbstractModifier
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
    ) {
        $this->localeDate = $localeDate;
        parent::__construct($objectManager, $registry, $request, $urlBuilder);
    }

    /**
     * @var string
     */
    protected $groupContainer = 'general_information';

    /**
     * @var string
     */
    protected $groupLabel = 'Delivered Time';

    /**
     * @var int
     */
    protected $sortOrder = 10;

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
     * Modify return order form meta
     *
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta){
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
                                'visible' => $this->getVisible(),
                                'opened' => true,
                                'componentType' => \Magento\Ui\Component\Form\Fieldset::NAME,
                                'sortOrder' => $this->getSortOrder()
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
    public function getGeneralChildren(){
        $defaultDate = new \DateTime();
//        $warehouse = $this->objectManager->create('Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Warehouse');
        $children = [
            'return_id' => $this->addFormFieldText('', 'hidden', 10),
            'transferred_at' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => \Magento\Ui\Component\Form\Field::NAME,
                            'label' => __('Delivered Date'),
                            'dataType' => 'date',
                            'formElement' => 'date',
                            'sortOrder' => 20,
                            'dataScope' => 'transferred_at',
                            'validation' => ['required-entry' => true],
                            'default' => $this->localeDate->date()->format('Y-m-d')
                        ],
                    ],
                ],
            ],
            'is_decrease_stock' => $this->addFormFieldCheckbox('Subtract stock on source', '30', '1'),
//            'warehouse_id' => $this->addFormFieldSelect('Warehouse', $warehouse->getOptionArray(), 30, true)
        ];
        return $children;
    }
}