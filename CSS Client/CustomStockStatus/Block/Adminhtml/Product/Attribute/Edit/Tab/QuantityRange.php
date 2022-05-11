<?php
/**
 * @category Mageants CustomStockStatus
 * @package Mageants_CustomStockStatus
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <info@mageants.com>
 */

namespace Mageants\CustomStockStatus\Block\Adminhtml\Product\Attribute\Edit\Tab;

class QuantityRange extends \Magento\Framework\View\Element\Template
{
    /**
     * Path to template file.
     */
    public $_template = 'QuantityRange.phtml';

    public $eavConfig;

    public $request;

    public $customRuleManage;

    const CUSTOM_RULE_ATTRIBUTE_CODE = 'mageants_custom_stock_rule';

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Eav\Model\Config $eavConfig,
        \Mageants\CustomStockStatus\Model\CustomStockRuleFactory $CustomRuleManage,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->request = $request;
        $this->eavConfig = $eavConfig;
        $this->customRuleManage = $CustomRuleManage;
    }

    public function getRuleAttributeOption()
    {
        $attributeCode = self::CUSTOM_RULE_ATTRIBUTE_CODE;
        $attributeDetails = $this->eavConfig->getAttribute("catalog_product", $attributeCode);
        $alloptions = $attributeDetails->getSource()->getAllOptions();
        $backOrderSt = $this->getBackOrderSt();
        $alloptions = array_merge($alloptions, $backOrderSt);

        return $alloptions;
    }

    public function getCustomRuleCollection()
    {
        $customRule = $this->customRuleManage->create();
        $ruleCollection = $customRule->getCollection();
        return $ruleCollection;
    }

    public function getBackOrderSt()
    {
        return [
            ['value' => \Magento\CatalogInventory\Model\Stock::BACKORDERS_NO,
            'label' => __('No Backorders (Sysetm Value <br> - Processed Automatically)')],
            [
                'value' => \Magento\CatalogInventory\Model\Stock::BACKORDERS_YES_NONOTIFY,
                'label' => __('Allow Qty Below 0 (Sysetm Value <br>- Processed Automatically)')
            ],
            [
                'value' => \Magento\CatalogInventory\Model\Stock::BACKORDERS_YES_NOTIFY,
                'label' => __('Allow Qty Below 0 and Notify Customer (Sysetm Value <br> - Processed Automatically)')
            ]
        ];
    }
}
