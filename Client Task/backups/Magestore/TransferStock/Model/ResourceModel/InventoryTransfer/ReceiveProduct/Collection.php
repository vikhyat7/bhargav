<?php
/**
 * Copyright © 2019 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\ReceiveProduct;

/**
 * Receive product collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Magestore\TransferStock\Model\InventoryTransfer\ReceiveProduct::class,
            \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\ReceiveProduct::class
        );
    }

    /**
     * Get product's image
     */
    public function getImageProduct()
    {
        // get image
        $storeManager = \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Store\Model\StoreManagerInterface::class);
        $path = $storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
        $path .= 'catalog/product';
        $edition = \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\App\ProductMetadataInterface::class)
            ->getEdition();
        $rowId = strtolower($edition) == 'enterprise' ? 'row_id' : 'entity_id';
        /** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute */
        $eavAttribute = \Magento\Framework\App\ObjectManager::getInstance()
            ->create(\Magento\Eav\Model\ResourceModel\Entity\Attribute::class);
        $productImagesAttributeId = $eavAttribute->getIdByCode(\Magento\Catalog\Model\Product::ENTITY, 'thumbnail');
        $this->getSelect()->joinLeft(
            ['catalog_product_entity_varchar_img' => $this->getTable('catalog_product_entity_varchar')],
            "main_table.product_id = catalog_product_entity_varchar_img.$rowId && 
                catalog_product_entity_varchar_img.attribute_id = $productImagesAttributeId && 
                catalog_product_entity_varchar_img.store_id = 0",
            ['']
        )->columns([
            'image' => 'catalog_product_entity_varchar_img.value',
            'image_url' => 'CONCAT("'.$path.'", catalog_product_entity_varchar_img.value)'
        ]);
    }

    /**
     * Get product type
     */
    public function getProductType()
    {
        $edition = \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\App\ProductMetadataInterface::class)
            ->getEdition();
        $rowId = strtolower($edition) == 'enterprise' ? 'row_id' : 'entity_id';
        $this->getSelect()->joinLeft(
            ['catalog_product_entity' => $this->getTable('catalog_product_entity')],
            "main_table.product_id = catalog_product_entity.$rowId",
            ['']
        )->columns([
            'product_type_id' => 'catalog_product_entity.type_id',
        ]);
    }

    /**
     * Get Product From Receive
     *
     * @param int $transferId
     * @return $this
     */
    public function getProductFromReceive($transferId)
    {
        $this->getSelect()->joinLeft(
            ['receive' => $this->getTable('os_inventorytransfer_receive')],
            "main_table.receive_id = receive.receive_id",
            ['main_table.product_id']
        )->group("main_table.product_id");
        $this->addFieldToFilter('inventorytransfer_id', $transferId);
        return $this;
    }

    /**
     * Get Number Product Sku Receive
     *
     * @param int $transferId
     * @return int
     */
    public function getNumberProductSkuReceive($transferId)
    {
        return $this->getProductFromReceive($transferId)->getSize();
    }

    /**
     * Get list product SKU received
     *
     * @param int $transferId
     * @return array
     */
    public function getListProductSkuReceive($transferId)
    {
        $items = $this->getProductFromReceive($transferId);
        $listSku = [];
        foreach ($items as $item) {
            $listSku[] = $item['product_sku'];
        }
        return $listSku;
    }
}
