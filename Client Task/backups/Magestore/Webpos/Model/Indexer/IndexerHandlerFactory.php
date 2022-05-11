<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Indexer;

use Magento\Framework\Indexer\SaveHandler\IndexerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Search\EngineResolverInterface;

/**
 * Generator of Indexer handler factory
 *
 * Follow Magento\CatalogSearch\Model\Indexer\IndexerHandlerFactory
 */
class IndexerHandlerFactory
{
    /**
     * Object Manager instance
     *
     * @var ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $handlers = null;

    /**
     * @var EngineResolverInterface
     */
    private $engineResolver;

    /**
     * Factory constructor
     *
     * @param ObjectManagerInterface $objectManager
     * @param EngineResolverInterface $engineResolver
     * @param string[] $handlers
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        EngineResolverInterface $engineResolver,
        array $handlers = []
    ) {
        $this->_objectManager = $objectManager;
        $this->handlers = $handlers;
        $this->engineResolver = $engineResolver;
    }

    /**
     * Create indexer handler
     *
     * @param array $data
     * @return IndexerInterface
     */
    public function create(array $data = [])
    {
        $currentHandler = $this->engineResolver->getCurrentSearchEngine();
        if (!isset($this->handlers[$currentHandler])) {
            throw new \LogicException(
                'There is no such indexer handler: ' . $currentHandler
            );
        }
        $indexer = $this->_objectManager->create($this->handlers[$currentHandler], $data);

        if (!$indexer instanceof IndexerInterface) {
            throw new \InvalidArgumentException(
                $currentHandler . ' indexer handler doesn\'t implement ' . IndexerInterface::class
            );
        }

        if ($indexer && !$indexer->isAvailable()) {
            throw new \LogicException(
                'Indexer handler is not available: ' . $currentHandler
            );
        }
        return $indexer;
    }
}
