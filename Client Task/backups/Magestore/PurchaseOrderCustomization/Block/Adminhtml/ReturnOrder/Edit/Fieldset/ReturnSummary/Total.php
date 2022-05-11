<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderCustomization\Block\Adminhtml\ReturnOrder\Edit\Fieldset\ReturnSummary;

use Magestore\PurchaseOrderSuccess\Api\ReturnOrderRepositoryInterface;
use Magestore\PurchaseOrderSuccess\Model\ResourceModel\ReturnOrder\Item\CollectionFactory;

/**
 * Class Total
 *
 * @package Magestore\PurchaseOrderCustomization\Block\Adminhtml\ReturnOrder\Edit\Fieldset\ReturnSummary
 */
class Total extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface
     */
    protected $purchaseOrderRepository;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $currencyFactory;

    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $currency;

    /**
     * @var ReturnOrderRepositoryInterface
     */
    protected $returnOrderRepository;

    /**
     * @var mixed
     */
    protected $returnOrder;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Model\ResourceModel\ReturnOrder\Item\CollectionFactory
     */
    protected $returnItemCollectionFactory;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $priceHelper;

    /**
     * Total constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ReturnOrderRepositoryInterface $returnOrderRepository
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param CollectionFactory $returnItemCollectionFactory
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param array $data
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magestore\PurchaseOrderSuccess\Api\ReturnOrderRepositoryInterface $returnOrderRepository,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        CollectionFactory $returnItemCollectionFactory,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->currencyFactory = $currencyFactory;
        $this->returnOrderRepository = $returnOrderRepository;
        $this->returnItemCollectionFactory = $returnItemCollectionFactory;
        $this->priceHelper = $priceHelper;
        $this->returnOrder = $this->getCurrentReturnOrder();
    }

    protected $_template = 'Magestore_PurchaseOrderCustomization::returnorder/form/returnsummary/total.phtml';

    /**
     * Get current return order
     *
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderInterface|mixed
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getCurrentReturnOrder()
    {
        $returnOrder = $this->registry->registry('current_return_order');
        if (!$returnOrder || ($returnOrder && !$returnOrder->getId())) {
            $returnOrder = $this->returnOrderRepository->get($this->getRequest()->getParam('id'));
        }
        return $returnOrder;
    }

    /**
     * Get Returned Total
     *
     * @return float|int
     */
    public function getReturnedTotal()
    {
        $result = 0;
        if ($this->returnOrder->getId()) {
            $collection = $this->returnItemCollectionFactory->create()
                ->addFieldToFilter('return_id', $this->returnOrder->getId());
            if ($collection->getSize()) {
                foreach ($collection as $returnOrderItem) {
                    $result += $returnOrderItem->getData('cost') * $returnOrderItem->getQtyReturned();
                }
            }
        }
        return $result;
    }

    /**
     * Convert Price
     *
     * @param float $price
     * @return float|string
     */
    public function convertPrice($price)
    {
        return $this->priceHelper->currency($price);
    }
}
