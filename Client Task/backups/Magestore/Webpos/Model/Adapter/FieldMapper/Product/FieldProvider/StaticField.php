<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magestore\Webpos\Model\Adapter\FieldMapper\Product\FieldProvider;

use Magento\Framework\App\ObjectManager;
use Magento\Eav\Model\Config;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Elasticsearch\Model\Adapter\FieldMapper\Product\AttributeProvider;
use Magento\Elasticsearch\Model\Adapter\FieldMapper\Product\FieldProviderInterface;
use Magento\Elasticsearch\Model\Adapter\FieldMapper\Product\FieldProvider\FieldType\ConverterInterface
    as FieldTypeConverterInterface;
use Magento\Elasticsearch\Model\Adapter\FieldMapper\Product\FieldProvider\FieldIndex\ConverterInterface
    as IndexTypeConverterInterface;
use Magento\Elasticsearch\Model\Adapter\FieldMapper\Product\FieldProvider\FieldType\ResolverInterface
    as FieldTypeResolver;
use Magento\Elasticsearch\Model\Adapter\FieldMapper\Product\FieldProvider\FieldIndex\ResolverInterface
    as FieldIndexResolver;
use Magento\Elasticsearch\Model\Adapter\FieldMapperInterface;
use Magento\Elasticsearch\Model\Adapter\FieldMapper\Product\FieldProvider\FieldName\ResolverInterface
    as FieldNameResolver;

/**
 * Provide static fields for mapping of product.
 *
 * Follow Magento\Elasticsearch\Model\Adapter\FieldMapper\Product\FieldProvider\StaticField
 */
class StaticField implements FieldProviderInterface
{
    /**
     * @var Config
     */
    private $eavConfig;

    /**
     * @var FieldTypeConverterInterface
     */
    private $fieldTypeConverter;

    /**
     * @var IndexTypeConverterInterface
     */
    private $indexTypeConverter;

    /**
     * @var AttributeProvider
     */
    private $attributeAdapterProvider;

    /**
     * @var FieldTypeResolver
     */
    private $fieldTypeResolver;

    /**
     * @var FieldIndexResolver
     */
    private $fieldIndexResolver;

    /**
     * @var FieldNameResolver
     */
    private $fieldNameResolver;

    /**
     * @var array
     */
    private $excludedAttributes;

    private $allowAttributes = [
        'sku',
        'name',
        'webpos_visible',
        'status'
    ];

    /**
     * @param Config $eavConfig
     * @param FieldTypeConverterInterface $fieldTypeConverter
     * @param IndexTypeConverterInterface $indexTypeConverter
     * @param FieldTypeResolver $fieldTypeResolver
     * @param FieldIndexResolver $fieldIndexResolver
     * @param AttributeProvider $attributeAdapterProvider
     * @param FieldNameResolver|null $fieldNameResolver
     * @param array $excludedAttributes
     */
    public function __construct(
        Config $eavConfig,
        FieldTypeConverterInterface $fieldTypeConverter,
        IndexTypeConverterInterface $indexTypeConverter,
        FieldTypeResolver $fieldTypeResolver,
        FieldIndexResolver $fieldIndexResolver,
        AttributeProvider $attributeAdapterProvider,
        FieldNameResolver $fieldNameResolver = null,
        array $excludedAttributes = []
    ) {
        $this->eavConfig = $eavConfig;
        $this->fieldTypeConverter = $fieldTypeConverter;
        $this->indexTypeConverter = $indexTypeConverter;
        $this->fieldTypeResolver = $fieldTypeResolver;
        $this->fieldIndexResolver = $fieldIndexResolver;
        $this->attributeAdapterProvider = $attributeAdapterProvider;
        $this->fieldNameResolver = $fieldNameResolver ?: ObjectManager::getInstance()
            ->get(FieldNameResolver::class);
        $this->excludedAttributes = $excludedAttributes;
    }

    /**
     * Get static fields.
     *
     * @param array $context
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getFields(array $context = []): array
    {
        $attributes = $this->eavConfig->getEntityAttributes(ProductAttributeInterface::ENTITY_TYPE_CODE);
        $allAttributes = [];

        foreach ($attributes as $attribute) {
            if (!in_array($attribute->getAttributeCode(), $this->allowAttributes, true)) {
                continue;
            }
            if (in_array($attribute->getAttributeCode(), $this->excludedAttributes, true)) {
                continue;
            }
            $attributeAdapter = $this->attributeAdapterProvider->getByAttributeCode($attribute->getAttributeCode());
            $fieldName = $this->fieldNameResolver->getFieldName($attributeAdapter);

            $allAttributes[$fieldName] = [
                'type' => $this->fieldTypeResolver->getFieldType($attributeAdapter),
            ];

            $index = $this->fieldIndexResolver->getFieldIndex($attributeAdapter);
            if (null !== $index) {
                $allAttributes[$fieldName]['index'] = $index;
            }

            if ($attributeAdapter->isSortable()) {
                $sortFieldName = $this->fieldNameResolver->getFieldName(
                    $attributeAdapter,
                    ['type' => FieldMapperInterface::TYPE_SORT]
                );
                $allAttributes[$fieldName]['fields'][$sortFieldName] = [
                    'type' => $this->fieldTypeConverter->convert(
                        FieldTypeConverterInterface::INTERNAL_DATA_TYPE_KEYWORD
                    ),
                    'index' => $this->indexTypeConverter->convert(
                        IndexTypeConverterInterface::INTERNAL_NO_ANALYZE_VALUE
                    )
                ];
            }

            if (method_exists($attributeAdapter, 'isTextType') && $attributeAdapter->isTextType()) {
                $keywordFieldName = FieldTypeConverterInterface::INTERNAL_DATA_TYPE_KEYWORD;
                $index = $this->indexTypeConverter->convert(
                    IndexTypeConverterInterface::INTERNAL_NO_ANALYZE_VALUE
                );
                $allAttributes[$fieldName]['fields'][$keywordFieldName] = [
                    'type' => $this->fieldTypeConverter->convert(
                        FieldTypeConverterInterface::INTERNAL_DATA_TYPE_KEYWORD
                    )
                ];
                if ($index) {
                    $allAttributes[$fieldName]['fields'][$keywordFieldName]['index'] = $index;
                }
            }

            if ($attributeAdapter->isComplexType()) {
                $childFieldName = $this->fieldNameResolver->getFieldName(
                    $attributeAdapter,
                    ['type' => FieldMapperInterface::TYPE_QUERY]
                );
                $allAttributes[$childFieldName] = [
                    'type' => $this->fieldTypeConverter->convert(FieldTypeConverterInterface::INTERNAL_DATA_TYPE_STRING)
                ];
            }
        }

        $allAttributes['store_id'] = [
            'type' => $this->fieldTypeConverter->convert(FieldTypeConverterInterface::INTERNAL_DATA_TYPE_STRING),
            'index' => $this->indexTypeConverter->convert(IndexTypeConverterInterface::INTERNAL_NO_INDEX_VALUE),
        ];
        $allAttributes['entity_id'] = [
            'type' => $this->fieldTypeConverter->convert(FieldTypeConverterInterface::INTERNAL_DATA_TYPE_INT)
        ];
        $allAttributes['type_id'] = [
            'type' => $this->fieldTypeConverter->convert(FieldTypeConverterInterface::INTERNAL_DATA_TYPE_STRING)
        ];
        $allAttributes['attribute_set_id'] = [
            'type' => $this->fieldTypeConverter->convert(FieldTypeConverterInterface::INTERNAL_DATA_TYPE_INT)
        ];
        $allAttributes['barcode'] = [
            'type' => $this->fieldTypeConverter->convert(FieldTypeConverterInterface::INTERNAL_DATA_TYPE_STRING)
        ];
        $allAttributes['stock_id'] = [
            'type' => $this->fieldTypeConverter->convert(FieldTypeConverterInterface::INTERNAL_DATA_TYPE_STRING)
        ];

        return $allAttributes;
    }
}
