<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\ReturnOrder\Edit\Fieldset\ReturnSumary;

/**
 * Block ImportProduct
 */
class ImportProduct extends \Magento\Backend\Block\Widget\Form\Generic
{

    protected $_template = 'Magestore_PurchaseOrderSuccess::returnorder/form/import.phtml';

    /**
     * @var int
     */
    protected $returnId;

    /**
     * @var int
     */
    protected $supplierId;

    /**
     * ImportProduct constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->setUseContainer(true);
    }

    /**
     * Get Html Id
     *
     * @return array|mixed|string|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getHtmlId()
    {
        if (null === $this->getData('id')) {
            $this->setData('id', $this->mathRandom->getUniqueHash('id_'));
        }
        return $this->getData('id');
    }

    /**
     * Get current return id
     *
     * @return int|mixed
     */
    public function getPurchaseId()
    {
        if (!$this->returnId) {
            $this->returnId = $this->getRequest()->getParam('return_id');
        }
        return $this->returnId;
    }

    /**
     * Get current supplier id
     *
     * @return int|mixed
     */
    public function getSupplierId()
    {
        if (!$this->supplierId) {
            $this->supplierId = $this->getRequest()->getParam('supplier_id');
        }
        return $this->supplierId;
    }

    /**
     * Get sample download csv file url
     *
     * @return string
     */
    public function getCsvSampleLink()
    {
        return $this->getUrl(
            'purchaseordersuccess/returnOrder_product/downloadSample',
            [
                'return_id' => $this->getPurchaseId(),
                'supplier_id' => $this->getSupplierId()
            ]
        );
    }

    /**
     * Get import url
     *
     * @return string
     */
    public function getImportUrl()
    {
        return $this->getUrl(
            'purchaseordersuccess/returnOrder_product/import',
            [
                'return_id' => $this->getPurchaseId(),
                'supplier_id' => $this->getSupplierId()
            ]
        );
    }
}
