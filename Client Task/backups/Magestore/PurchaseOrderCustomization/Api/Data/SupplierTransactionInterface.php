<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderCustomization\Api\Data;

/**
 * Interface SupplierTransactionInterface
 *
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD)
 * @package Magestore\PurchaseOrderCustomization\Api\Data
 */
interface SupplierTransactionInterface
{
    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param int $id
     * @return mixed
     */
    public function setId($id);

    /**
     * @return mixed
     */
    public function getSupplierId();

    /**
     * @param int $supplierId
     * @return mixed
     */
    public function setSupplierId($supplierId);

    /**
     * @return mixed
     */
    public function getTransactionCreatedDate();

    /**
     * @param string $transactionCreatedDate
     * @return mixed
     */
    public function setTransactionCreatedDate($transactionCreatedDate);

    /**
     * @return mixed
     */
    public function getTransactionDate();

    /**
     * @param string $transactionDate
     * @return mixed
     */
    public function setTransactionDate($transactionDate);

    /**
     * @return mixed
     */
    public function getType();

    /**
     * @param string $type
     * @return mixed
     */
    public function setType($type);

    /**
     * @return mixed
     */
    public function getDocNo();

    /**
     * @param string $docNo
     * @return mixed
     */
    public function setDocNo($docNo);

    /**
     * @return mixed
     */
    public function getChqNo();

    /**
     * @param string $chq_no
     * @return mixed
     */
    public function setChqNo($chq_no);

    /**
     * @return mixed
     */
    public function getAmount();

    /**
     * @param float $amount
     * @return mixed
     */
    public function setAmount($amount);

    /**
     * @return mixed
     */
    public function getCurrency();

    /**
     * @param string $currency
     * @return mixed
     */
    public function setCurrency($currency);

    /**
     * @return mixed
     */
    public function getDescriptionOption();

    /**
     * @param string $descriptionOption
     * @return mixed
     */
    public function setDescriptionOption($descriptionOption);
}