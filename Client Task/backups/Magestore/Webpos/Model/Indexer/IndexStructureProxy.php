<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Indexer;

use Magento\Framework\Indexer\IndexStructureInterface;

/**
 * Catalog search index structure proxy.
 *
 * Follow Magento\CatalogSearch\Model\Indexer\IndexStructureProxy
 */
class IndexStructureProxy implements IndexStructureInterface
{
    /**
     * @var IndexStructureInterface
     */
    private $indexStructureEntity;

    /**
     * @var IndexStructureFactory
     */
    private $indexStructureFactory;

    /**
     * @param IndexStructureFactory $indexStructureFactory
     */
    public function __construct(
        IndexStructureFactory $indexStructureFactory
    ) {
        $this->indexStructureFactory = $indexStructureFactory;
    }

    /**
     * @inheritdoc
     */
    public function delete(
        $index,
        array $dimensions = []
    ) {
        return $this->getEntity()->delete($index, $dimensions);
    }

    /**
     * @inheritdoc
     */
    public function create(
        $index,
        array $fields,
        array $dimensions = []
    ) {
        return $this->getEntity()->create($index, $fields, $dimensions);
    }

    /**
     * Get instance of current index structure
     *
     * @return IndexStructureInterface
     */
    private function getEntity()
    {
        if (empty($this->indexStructureEntity)) {
            $this->indexStructureEntity = $this->indexStructureFactory->create();
        }
        return $this->indexStructureEntity;
    }
}
