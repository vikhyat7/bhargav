<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Model\ResourceModel\Db;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magestore\SupplierSuccess\Model\Db\QueryProcessorInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class QueryProcessor
 *
 * @package Magestore\SupplierSuccess\Model\ResourceModel\Db
 */
class QueryProcessor extends AbstractDb
{
    /**
     * Init require function
     */
    protected function _construct()
    {
        /* do nothing */
        $this->getConnection();
    }
    
    /**
     * Process queries
     *
     * @param array $queries
     * @return \Magestore\SupplierSuccess\Model\ResourceModel\Db\QueryProcessor
     * @throws LocalizedException
     */
    public function processQueries($queries)
    {
        if (!count($queries)) {
            return $this;
        }
        $connection = $this->getConnection();
        try {
            $connection->beginTransaction();
            foreach ($queries as $queryData) {
                switch ($queryData['type']) {
                    case QueryProcessorInterface::QUERY_TYPE_INSERT:
                        $connection->insertMultiple($queryData['table'], $queryData['values']);
                        break;
                    case QueryProcessorInterface::QUERY_TYPE_UPDATE:
                        $connection->update($queryData['table'], $queryData['values'], $queryData['condition']);
                        break;
                    case QueryProcessorInterface::QUERY_TYPE_DELETE:
                        $connection->delete($queryData['table'], $queryData['condition']);
                        break;
                }
            }
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw new LocalizedException(__($e->getMessage()), $e);
        }
        return $this;
    }
}
