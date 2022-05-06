<?php
/**
 * @category Mageants FreeGift
 * @package Mageants_FreeGift
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
 
namespace Mageants\FreeGift\Plugin;

class CalculatorFactory
{
	const ADD_FREE_ITEM_ACTION = 'add_free_item';

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    protected $classByType = [
		self::ADD_FREE_ITEM_ACTION => 'Mageants\FreeGift\Model\Rule\Action\Discount\AddFreeItem'
    ];

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_objectManager = $objectManager;
    }

    public function aroundCreate(
        \Magento\SalesRule\Model\Rule\Action\Discount\CalculatorFactory $subject,
        \Closure $proceed,
        $type
    ) {
        if (isset($this->classByType[$type])) {
            return $this->_objectManager->create($this->classByType[$type]);
        }

        return $proceed($type);
    }
}
