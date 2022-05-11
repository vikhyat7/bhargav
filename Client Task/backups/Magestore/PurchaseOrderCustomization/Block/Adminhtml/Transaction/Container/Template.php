<?php

namespace Magestore\PurchaseOrderCustomization\Block\Adminhtml\Transaction\Container;

/**
 * Class Template
 *
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD)
 * @package Magestore\PurchaseOrderCustomization\Block\Adminhtml\Transaction\Container
 */
class Template extends \Magento\Backend\Block\Template
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magestore\SupplierSuccess\Model\Repository\SupplierRepository
     */
    protected $supplierRepository;

    /**
     * @var \Magestore\PurchaseOrderCustomization\Service\PaymentTermService
     */
    protected $paymentTermService;

    /**
     * @var \Magestore\PurchaseOrderCustomization\Model\ResourceModel\Supplier\Transaction\CollectionFactory
     */
    protected $transactionCollectionFactory;

    /**
     * @var \Magestore\PurchaseOrderCustomization\Model\ResourceModel\Supplier\Transaction\Grid\CollectionFactory
     */
    protected $transactionGridCollectionFactory;

    /**
     * Template constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magestore\SupplierSuccess\Model\Repository\SupplierRepository $supplierRepository
     * @param \Magestore\PurchaseOrderCustomization\Service\PaymentTermService $paymentTermService
     * @param \Magestore\PurchaseOrderCustomization\Model\ResourceModel\Supplier\Transaction\CollectionFactory $transactionCollectionFactory
     * @param \Magestore\PurchaseOrderCustomization\Model\ResourceModel\Supplier\Transaction\Grid\CollectionFactory $transactionGridCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magestore\SupplierSuccess\Model\Repository\SupplierRepository $supplierRepository,
        \Magestore\PurchaseOrderCustomization\Service\PaymentTermService $paymentTermService,
        \Magestore\PurchaseOrderCustomization\Model\ResourceModel\Supplier\Transaction\CollectionFactory $transactionCollectionFactory,
        \Magestore\PurchaseOrderCustomization\Model\ResourceModel\Supplier\Transaction\Grid\CollectionFactory $transactionGridCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->scopeConfig = $scopeConfig;
        $this->supplierRepository = $supplierRepository;
        $this->paymentTermService = $paymentTermService;
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        $this->transactionGridCollectionFactory = $transactionGridCollectionFactory;
    }

    public function getStatementHeader()
    {
        return $this->scopeConfig->getValue('suppliersuccess/print_config/statement_header');
    }

    public function getStatementFooter()
    {
        return $this->scopeConfig->getValue('suppliersuccess/print_config/statement_footer');
    }

    public function getPeriod()
    {
        return $this->getData('transaction_date_from') . ' - ' . $this->getData('transaction_date_to');
    }

    public function getSupplierInfo()
    {
        $supplierId = $this->getData('supplier_id');
        if ($supplierId) {
            $paymentTermOptions = $this->paymentTermService->getPaymentTermOptions();
            $supplier = $this->supplierRepository->getById($supplierId);
            $paymentTerm = $supplier->getPaymentTerm();
            return [
                'supplier_info' => $supplier->getSupplierName() . ' ' . $supplier->getSupplierCode(),
                'payment_term' => ($paymentTerm != '' && $paymentTerm != '-1') ? $paymentTermOptions[$paymentTerm] : ''
            ];
        }
        return [
            'supplier_info' => '',
            'payment_term' => ''
        ];
    }

    public function getOpeningBalance()
    {
        $openBalance = 0;
        $collection = $this->transactionCollectionFactory->create();
        $transactionDateFrom = date_create_from_format("d/m/Y", $this->getData('transaction_date_from'));
        $dateFrom = date_format($transactionDateFrom, 'Y-m-d');
        $collection->addFieldToFilter('transaction_date', ['lt' => $dateFrom])
            ->addFieldToFilter('supplier_id', $this->getData('supplier_id'));
        if ($collection->getSize() > 0) {
            foreach ($collection as $transaction) {
                if ($transaction->getType() == \Magestore\PurchaseOrderCustomization\Model\Supplier\Transaction\Options\Type::TYPE_CREDIT) {
                    $openBalance += $transaction->getAmount();
                }
                if ($transaction->getType() == \Magestore\PurchaseOrderCustomization\Model\Supplier\Transaction\Options\Type::TYPE_DEBIT) {
                    $openBalance -= $transaction->getAmount();
                }
            }
        }
        return $openBalance;
    }

    public function getClosingBalance(){
        $closeBalance = 0;
        $transactionDateTo = date_create_from_format("d/m/Y", $this->getData('transaction_date_to'));
        $dateTo = date_format($transactionDateTo, 'Y-m-d');
        $collection = $this->transactionCollectionFactory->create();
        $collection->addFieldToFilter('transaction_date', ["lteq" => $dateTo])
            ->addFieldToFilter('supplier_id', $this->getData('supplier_id'));
        if ($collection->getSize() > 0) {
            foreach ($collection as $transaction) {
                if ($transaction->getType() == \Magestore\PurchaseOrderCustomization\Model\Supplier\Transaction\Options\Type::TYPE_CREDIT) {
                    $closeBalance += $transaction->getAmount();
                }
                if ($transaction->getType() == \Magestore\PurchaseOrderCustomization\Model\Supplier\Transaction\Options\Type::TYPE_DEBIT) {
                    $closeBalance -= $transaction->getAmount();
                }
            }
        }
        return $closeBalance;
    }

    public function getTransactions(){
        $transactionDateFrom = date_create_from_format("d/m/Y", $this->getData('transaction_date_from'));
        $dateFrom = date_format($transactionDateFrom, 'Y-m-d');
        $transactionDateTo = date_create_from_format("d/m/Y", $this->getData('transaction_date_to'));
        $dateTo = date_format($transactionDateTo, 'Y-m-d');
        $collection = $this->transactionGridCollectionFactory->create();
        $collection->addFieldToFilter('transaction_date', ["from" => $dateFrom, "to" => $dateTo])
            ->addFieldToFilter('supplier_id', $this->getData('supplier_id'))
            ->setOrder('transaction_date', 'ASC');
        return $collection;
    }
}