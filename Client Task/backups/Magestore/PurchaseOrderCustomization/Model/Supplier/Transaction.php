<?php

namespace Magestore\PurchaseOrderCustomization\Model\Supplier;

/**
 * Class Transaction
 *
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD)
 * @package Magestore\PurchaseOrderCustomization\Model\Supplier
 */
class Transaction extends \Magento\Framework\Model\AbstractModel
    implements \Magestore\PurchaseOrderCustomization\Api\Data\SupplierTransactionInterface
{

    /**
     * Construct
     *
     */
    protected function _construct()
    {
        $this->_init('Magestore\PurchaseOrderCustomization\Model\ResourceModel\Supplier\Transaction');
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->getData('supplier_transaction_id');
    }

    /**
     * @param int|mixed $id
     * @return \Magento\Framework\Model\AbstractModel|Transaction|mixed
     */
    public function setId($id)
    {
        return $this->setData('supplier_transaction_id', $id);
    }

    /**
     * @return mixed
     */
    public function getSupplierId()
    {
        return $this->getData('supplier_id');
    }

    /**
     * @param int $supplierId
     * @return Transaction|mixed
     */
    public function setSupplierId($supplierId)
    {
        return $this->setData('supplier_id', $supplierId);
    }

    /**
     * @return mixed
     */
    public function getTransactionCreatedDate()
    {
        return $this->getData('transaction_created_date');
    }

    /**
     * @param string $transactionCreatedDate
     * @return Transaction|mixed
     */
    public function setTransactionCreatedDate($transactionCreatedDate)
    {
        return $this->setData('transaction_created_date', $transactionCreatedDate);
    }

    /**
     * @return mixed
     */
    public function getTransactionDate()
    {
        return $this->getData('transaction_date');
    }

    /**
     * @param string $transactionDate
     * @return Transaction|mixed
     */
    public function setTransactionDate($transactionDate)
    {
        return $this->setData('transaction_date', $transactionDate);
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->getData('type');
    }

    /**
     * @param string $type
     * @return Transaction|mixed
     */
    public function setType($type)
    {
        return $this->setData('type', $type);
    }

    /**
     * @return mixed
     */
    public function getDocNo()
    {
        return $this->getData('doc_no');
    }

    /**
     * @param string $docNo
     * @return Transaction|mixed
     */
    public function setDocNo($docNo)
    {
        return $this->setData('doc_no', $docNo);
    }

    /**
     * @return mixed
     */
    public function getChqNo()
    {
        return $this->getData('chq_no');
    }

    /**
     * @param string $chq_no
     * @return Transaction|mixed
     */
    public function setChqNo($chq_no)
    {
        return $this->setData('chq_no', $chq_no);
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->getData('amount');
    }

    /**
     * @param float $amount
     * @return Transaction|mixed
     */
    public function setAmount($amount)
    {
        return $this->setData('amount', $amount);
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->getData('currency');
    }

    /**
     * @param string $currency
     * @return Transaction|mixed
     */
    public function setCurrency($currency)
    {
        return $this->setData('currency', $currency);
    }

    /**
     * @return mixed
     */
    public function getDescriptionOption()
    {
        return $this->getData('description_option');
    }

    /**
     * @param string $descriptionOption
     * @return Transaction|mixed
     */
    public function setDescriptionOption($descriptionOption)
    {
        return $this->setData('description_option', $descriptionOption);
    }
}