<?php
/**
 * Copyright © Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderCustomization\Controller\Adminhtml\Supplier\Transaction;

use Magento\Backend\App\Action;
use Magestore\PoMultipleTracking\Model\Repository\PurchaseOrderShipmentRepository;
use Magestore\PoMultipleTracking\Model\PurchaseOrderShipmentFactory;
use Magestore\PoMultipleTracking\Model\PurchaseOrderShipment;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class PrintTransaction
 *
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD)
 * @package Magestore\PoMultipleTracking\Controller\Adminhtml\PurchaseOrder\Shipment
 * @SuppressWarnings(PHPMD.AllPurposeAction)
 */
class PrintTransaction extends Action
{

    /**
     * @var \Magestore\PurchaseOrderCustomization\Model\ResourceModel\Supplier\Transaction\CollectionFactory
     */
    protected $transactionCollectionFactory;

    /**
     * @var \Magestore\PurchaseOrderCustomization\Model\ResourceModel\Supplier\TransactionFactory
     */
    protected $transactionResourceFactory;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Save constructor.
     *
     * @param Action\Context $context
     * @param \Magestore\PurchaseOrderCustomization\Model\ResourceModel\Supplier\Transaction\CollectionFactory $transactionCollectionFactory
     * @param \Magestore\PurchaseOrderCustomization\Model\ResourceModel\Supplier\TransactionFactory $transactionResourceFactory
     */
    public function __construct(
        Action\Context $context,
        \Magestore\PurchaseOrderCustomization\Model\ResourceModel\Supplier\Transaction\CollectionFactory $transactionCollectionFactory,
        \Magestore\PurchaseOrderCustomization\Model\ResourceModel\Supplier\TransactionFactory $transactionResourceFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    )
    {
        parent::__construct($context);
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        $this->transactionResourceFactory = $transactionResourceFactory;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Execture
     *
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $html = '';
        $block = $this->createBlock('Magestore\PurchaseOrderCustomization\Block\Adminhtml\Transaction\Container\Template',
            '',
            'Magestore_PurchaseOrderCustomization::transaction/print/template.phtml'
        );
        $data = $this->getRequest()->getParam('data');
        $block->setData([
                'supplier_id' => $this->getRequest()->getParam('supplier_id'),
                'transaction_date_from' => isset($data['transaction_date_from']) ? $data['transaction_date_from'] : '',
                'transaction_date_to' => isset($data['transaction_date_to']) ? $data['transaction_date_to'] : '',
            ]
        );
        $html .= $block->toHtml();
        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData([
            'html' => $html,
            'success' => true
        ]);
        return $resultJson;
    }

    /**
     *
     * @param string $class
     * @param string $name
     * @param string $template
     * @return block type
     */
    public function createBlock($class, $name = '', $template = "")
    {
        $block = "";
        try {
            $block = $this->_view->getLayout()->createBlock($class, $name);
            if ($block && $template != "") {
                $block->setTemplate($template);
            }
        } catch (\Exception $e) {
            return $block;
        }
        return $block;
    }
}
