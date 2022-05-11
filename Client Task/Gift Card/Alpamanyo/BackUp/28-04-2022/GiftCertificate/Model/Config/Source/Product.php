<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCertificate\Model\Config\Source;
/**
 * Order classs for Fetch order for Grid
 */ 
class Product extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    const VALUE_YES = 1; // constant 1 
    const VALUE_NO = 0; // constant 0
    
    /**
     * Eav entity Attribute
     *
     * @var \Magento\Eav\Model\ResourceModel\Entity\AttributeFactory
     */
    protected $_eavAttrEntity;

    /**
     * @param \Magento\Eav\Model\ResourceModel\Entity\AttributeFactory $eavAttrEntity
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\AttributeFactory $eavAttrEntity
    ) {
        $this->_eavAttrEntity = $eavAttrEntity;
    }

    /**
     * @return Array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                ['label' => __('Yes'), 'value' => self::VALUE_YES],
                ['label' => __('No'), 'value' => self::VALUE_NO],
            ];
        }
        return $this->_options;
    }

    /**
     * @return Array
     */
    public function getOptionArray()
    {
        $_options = [];
        foreach ($this->getAllOptions() as $option) {
            $_options[$option['value']] = $option['label'];
        }
        return $_options;
    }

    /**
     * @return Bool
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions();
        foreach ($options as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }

    /**
     * @return Array
     */
    public function getFlatColumns()
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        return [
            $attributeCode => [
                'unsigned' => false,
                'default' => null,
                'extra' => null,
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'length' => 1,
                'nullable' => true,
                'comment' => $attributeCode . ' column',
            ],
        ];
    }

    /**
     * @return Array
     */
    public function getFlatIndexes()
    {
        $indexes = [];
        $index = 'IDX_' . strtoupper($this->getAttribute()->getAttributeCode());
        $indexes[$index] = ['type' => 'index', 'fields' => [$this->getAttribute()->getAttributeCode()]];
        return $indexes;
    }

    /**
     * @return String
     */
    public function getFlatUpdateSelect($store)
    {
        return $this->_eavAttrEntity->create()->getFlatUpdateSelect($this->getAttribute(), $store);
    }

    /**
     * @return const
     */
    public function getIndexOptionText($value)
    {
        switch ($value) {
            case self::VALUE_YES:
                return 'Yes';
            case self::VALUE_NO:
                return 'No';
        }
        return parent::getIndexOptionText($value);
    }

    /**
     * @return $this
     */
    public function addValueSortToCollection($collection, $dir = \Magento\Framework\DB\Select::SQL_ASC)
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $attributeId = $this->getAttribute()->getId();
        $attributeTable = $this->getAttribute()->getBackend()->getTable();
        $linkField = $this->getAttribute()->getEntity()->getLinkField();

        if ($this->getAttribute()->isScopeGlobal()) {
            $tableName = $attributeCode . '_t';
            $collection->getSelect()
                ->joinLeft(
                    [$tableName => $attributeTable],
                    "e.{$linkField}={$tableName}.{$linkField}"
                    . " AND {$tableName}.attribute_id='{$attributeId}'"
                    . " AND {$tableName}.store_id='0'",
                    []
                );
            $valueExpr = $tableName . '.value';
        } else {
            $valueTable1 = $attributeCode . '_t1';
            $valueTable2 = $attributeCode . '_t2';
            $collection->getSelect()
                ->joinLeft(
                    [$valueTable1 => $attributeTable],
                    "e.{$linkField}={$valueTable1}.{$linkField}"
                    . " AND {$valueTable1}.attribute_id='{$attributeId}'"
                    . " AND {$valueTable1}.store_id='0'",
                    []
                )
                ->joinLeft(
                    [$valueTable2 => $attributeTable],
                    "e.{$linkField}={$valueTable2}.{$linkField}"
                    . " AND {$valueTable2}.attribute_id='{$attributeId}'"
                    . " AND {$valueTable2}.store_id='{$collection->getStoreId()}'",
                    []
                );
            $valueExpr = $collection->getConnection()->getCheckSql(
                $valueTable2 . '.value_id > 0',
                $valueTable2 . '.value',
                $valueTable1 . '.value'
            );
        }

        $collection->getSelect()->order($valueExpr . ' ' . $dir);
        return $this;
    }
}
