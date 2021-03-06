<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Indexer\Mview;

use Magestore\Webpos\Model\Indexer\Fulltext;
use Magento\Framework\Mview\ActionInterface;
use Magento\Framework\Indexer\IndexerInterfaceFactory;

/**
 * Catalog search materialized view index action.
 */
class Action implements ActionInterface
{
    /**
     * @var IndexerInterfaceFactory
     */
    private $indexerFactory;

    /**
     * @param IndexerInterfaceFactory $indexerFactory
     */
    public function __construct(IndexerInterfaceFactory $indexerFactory)
    {
        $this->indexerFactory = $indexerFactory;
    }

    /**
     * Execute materialization on ids entities
     *
     * @param int[] $ids
     * @return void
     * @api
     */
    public function execute($ids)
    {
        /** @var \Magento\Framework\Indexer\IndexerInterface $indexer */
        $indexer = $this->indexerFactory->create()->load(Fulltext::INDEXER_ID);
        $indexer->reindexList($ids);
    }
}
