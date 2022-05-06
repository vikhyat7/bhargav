<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\PurchaseOrder;

use \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface;

/**
 * Class Item
 * @package Magestore\PurchaseOrderSuccess\Model\PurchaseOrder
 */
class Item extends \Magento\Framework\Model\AbstractModel
    implements PurchaseOrderItemInterface
{
    
    public function __construct(
        \Magestore\PurchaseOrderSuccess\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ){
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item');
    }
    
    /**
     * Get purchase order item id
     *
     * @return int
     */
    public function getPurchaseOrderItemId(){
        return $this->_getData(self::PURCHASE_ORDER_ITEM_ID);
    }

    /**
     * Set purchase order item id
     *
     * @param int $purchaseOrderItemId
     * @return $this
     */
    public function setPurchaseOrderItemId($purchaseOrderItemId){
        return $this->setData(self::PURCHASE_ORDER_ITEM_ID, $purchaseOrderItemId);
    }

    /**
     * Get purchase order id
     *
     * @return int
     */
    public function getPurchaseOrderId(){
        return $this->_getData(self::PURCHASE_ORDER_ID);
    }
    
    /**
     * Set purchase order id
     *
     * @param int $purchaseOrderId
     * @return $this
     */
    public function setPurchaseOrderId($purchaseOrderId){
        return $this->setData(self::PURCHASE_ORDER_ID, $purchaseOrderId);
    }

    /**
     * Get product id
     *
     * @return int
     */
    public function getProductId(){
        return $this->_getData(self::PRODUCT_ID);
    }

    /**
     * Set product id
     *
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId){
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * Get product sku
     *
     * @return string
     */
    public function getProductSku(){
        return $this->_getData(self::PRODUCT_SKU);
    }

    /**
     * Set product sku
     *
     * @param string $productSku
     * @return $this
     */
    public function setProductSku($productSku){
        return $this->setData(self::PRODUCT_SKU, $productSku);
    }

    /**
     * Get product name
     *
     * @return string
     */
    public function getProductName(){
        return $this->_getData(self::PRODUCT_NAME);
    }

    /**
     * Set product name
     *
     * @param string $productName
     * @return $this
     */
    public function setProductName($productName){
        return $this->setData(self::PRODUCT_NAME, $productName);
    }

    /**
     * Get product supplier sku
     *
     * @return string
     */
    public function getProductSupplierSku(){
        return $this->_getData(self::PRODUCT_SUPPLIER_SKU);
    }

    /**
     * Set product supplier sku
     *
     * @param string $productSupplierSku
     * @return $this
     */
    public function setProductSupplierSku($productSupplierSku){
        return $this->setData(self::PRODUCT_SUPPLIER_SKU, $productSupplierSku);
    }

    /**
     * Get qty orderred
     *
     * @return float
     */
    public function getQtyOrderred(){
        return $this->_getData(self::QTY_ORDERRED);
    }
    
    /**
     * Set qty orderred
     *
     * @param float $qtyOrderred
     * @return $this
     */
    public function setQtyOrderred($qtyOrderred){
        return $this->setData(self::QTY_ORDERRED, $qtyOrderred);
    }

    /**
     * Get qty received
     *
     * @return float
     */
    public function getQtyReceived(){
        return $this->_getData(self::QTY_RECEIVED);
    }

    /**
     * Set qty received
     *
     * @param float $qtyReceived
     * @return $this
     */
    public function setQtyReceived($qtyReceived){
        return $this->setData(self::QTY_RECEIVED, $qtyReceived);
    }

    /**
     * Get qty transferred
     *
     * @return float
     */
    public function getQtyTransferred(){
        return $this->_getData(self::QTY_TRANSFERRED);
    }

    /**
     * Set qty transferred
     *
     * @param float $qtyTransferred
     * @return $this
     */
    public function setQtyTransferred($qtyTransferred){
        return $this->setData(self::QTY_TRANSFERRED, $qtyTransferred);
    }

    /**
     * Get qty returned
     *
     * @return float
     */
    public function getQtyReturned(){
        return $this->_getData(self::QTY_RETURNED);
    }

    /**
     * Set qty returned
     *
     * @param float $qtyReturned
     * @return $this
     */
    public function setQtyReturned($qtyReturned){
        return $this->setData(self::QTY_RETURNED, $qtyReturned);
    }

    /**
     * Get qty billed
     *
     * @return float
     */
    public function getQtyBilled(){
        return $this->_getData(self::QTY_BILLED);
    }

    /**
     * Set qty billed
     *
     * @param float $qtyBilled
     * @return $this
     */
    public function setQtyBilled($qtyBilled){
        return $this->setData(self::QTY_BILLED, $qtyBilled);
    }

    /**
     * Get original cost
     *
     * @return float
     */
    public function getOriginalCost(){
        return $this->getData(self::ORIGINAL_COST);
    }

    /**
     * Set original cost
     *
     * @param float $originalCost
     * @return $this
     */
    public function setOriginalCost($originalCost){
        return $this->setData(self::ORIGINAL_COST, $originalCost);
    }

    /**
     * Get cost
     *
     * @return float
     */
    public function getCost(){
        return $this->_getData(self::COST);
    }

    /**
     * Set cost
     *
     * @param float $cost
     * @return $this
     */
    public function setCost($cost){
        return $this->setData(self::COST, $cost);
    }

    /**
     * Get tax
     *
     * @return float
     */
    public function getTax(){
        return $this->_getData(self::TAX);
    }

    /**
     * Set tax
     *
     * @param float $tax
     * @return $this
     */
    public function setTax($tax){
        return $this->setData(self::TAX, $tax);
    }

    /**
     * Get discount
     *
     * @return float
     */
    public function getDiscount(){
        return $this->_getData(self::DISCOUNT);
    }

    /**
     * Set discount
     *
     * @param float $discount
     * @return $this
     */
    public function setDiscount($discount){
        return $this->setData(self::DISCOUNT, $discount);
    }

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt(){
        return $this->_getData(self::CREATED_AT);
    }

    /**
     * Set created at
     *
     * @param string|null $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt){
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Get updated at
     *
     * @return string
     */
    public function getUpdatedAt(){
        return $this->_getData(self::UPDATED_AT);
    }

    /**
     * Set updated at
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt){
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}