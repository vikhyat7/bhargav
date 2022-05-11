<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magestore\PurchaseOrderSuccess\Ui\DataProvider\Supplier\Form\Modifier;

use Magestore\SupplierSuccess\Api\Data\SupplierInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Sanitizer;

/**
 * Class PurchaseOrderList
 *
 * Used for purchased order list
 */
class PurchaseOrderList extends \Magestore\PurchaseOrderSuccess\Ui\DataProvider\Modifier\AbstractModifier
{
    /**
     * @var string
     */
    protected $scopeName = 'os_supplier_form.os_supplier_form';

    /**
     * @var string
     */
    protected $groupContainer = 'purchase_order_list';

    /**
     * @var string
     */
    protected $groupLabel = 'Purchase Sales List';

    /**
     * @var int
     */
    protected $sortOrder = 100;

    protected $supplier;

    /**
     * @var array
     */
    protected $children = [
        'purchase_order_container' => 'purchase_order_container',
        'purchase_order_listing' => 'os_supplier_purchase_order_listing',
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
     * Get current supplier
     *
     * @return mixed|null
     */
    public function getCurrentSupplier()
    {
        if (!$this->supplier) {
            $this->supplier = $this->registry->registry(SupplierInterface::CURRENT_SUPPLIER);
        }
        return $this->supplier;
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
        $supplier = $this->getCurrentSupplier();
        if (!$supplier->getId()) {
            return $meta;
        }
        $totalOrder = $this->objectManager
            ->create(\Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface::class)
            ->getListBySupplierId(
                $supplier->getId(),
                \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Type::TYPE_PURCHASE_ORDER
            )->getSize();
        if (empty($totalOrder)) {
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
                                'opened' => false,
                                'componentType' => \Magento\Ui\Component\Form\Fieldset::NAME,
                                'sortOrder' => $this->getSortOrder(),
                                'actions' => [
                                    [
                                        'targetName' => $this->scopeName . '.' . $this->groupContainer . '.' .
                                            $this->children['purchase_order_container'],
                                        'actionName' => 'render',
                                    ]
                                ]
                            ],
                        ],
                    ],
                    'children' => $this->getPurchaseOrderListChildren()
                ],
            ]
        );
        return $meta;
    }

    /**
     * Add purchase order list form fields
     *
     * @return array
     */
    public function getPurchaseOrderListChildren()
    {
        $children[$this->children['purchase_order_container']] = $this->getPurchaseOrderList();
        return $children;
    }

    /**
     * Get received product list.
     *
     * @return array
     * @throws \Exception
     */
    public function getPurchaseOrderList()
    {
        $dataScope = 'purchase_order_listing';
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
                        ],
                        'exports' => [
                            'supplier_id' => '${ $.externalProvider }:params.supplier_id',
                        ],
                        'selectionsProvider' =>
                            $this->children[$dataScope]
                            . '.' . $this->children[$dataScope]
                            . '.supplier_purchase_order_template_columns.ids'
                    ]
                ]
            ]
        ];

        if ($this->isExistDisableTmpl()) {
            $data['arguments']['data']['config']['imports']['__disableTmpl'] = [
                'supplier_id' => false,
            ];

            $data['arguments']['data']['config']['exports']['__disableTmpl'] = [
                'supplier_id' => false,
            ];
        }

        return $data;
    }
}
