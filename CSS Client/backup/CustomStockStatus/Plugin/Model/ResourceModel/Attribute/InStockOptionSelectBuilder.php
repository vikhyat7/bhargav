<?php
/**
 * @category Mageants CustomStockStatus
 * @package Mageants_CustomStockStatus
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <info@mageants.com>
 */

namespace Mageants\CustomStockStatus\Plugin\Model\ResourceModel\Attribute;

use Magento\CatalogInventory\Model\ResourceModel\Stock\Status;
use Magento\ConfigurableProduct\Model\ResourceModel\Attribute\OptionSelectBuilderInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Plugin for OptionSelectBuilderInterface to add stock status filter.
 */
class InStockOptionSelectBuilder
{
    /**
     * CatalogInventory Stock Status Resource Model.
     *
     * @var Status
     */
    private $stockStatusResource;

    public $scopeConfig;

    /**
     * @param Status $stockStatusResource
     */
    public function __construct(Status $stockStatusResource, ScopeConfigInterface $scopeConfig)
    {
        $this->stockStatusResource = $stockStatusResource;
        $this->scopeConfig = $scopeConfig;
    }
    /**
     * Add stock status filter to select.
     *
     * @param OptionSelectBuilderInterface $subject
     * @param Select $select
     * @return Select
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetSelect(OptionSelectBuilderInterface $subject, Select $select)
    {
        $subject = $subject;
        $isEnables = $this->scopeConfig->getValue(
            'CustomStockSt/general/outofstockconfigattribute',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if ($isEnables) {
            $select->joinInner(
                ['stock' => $this->stockStatusResource->getMainTable()],
                'stock.product_id = entity.entity_id',
                ['stock.stock_status']
            );
            
            return $select;
        } else {
            $select->joinInner(
                ['stock' => $this->stockStatusResource->getMainTable()],
                'stock.product_id = entity.entity_id',
                []
            )->where(
                'stock.stock_status = ?',
                1
            );
            return $select;
        }
    }
}
