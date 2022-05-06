<?php
/**
 * Copyright © Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderCustomization\Model\ResourceModel\Supplier\Transaction\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Monolog\Handler\IFTTTHandler;
use Psr\Log\LoggerInterface as Logger;

/**
 * Class Collection
 *
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD)
 * @package Magestore\PurchaseOrderCustomization\Model\ResourceModel\Supplier\Transaction\Grid
 */
class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    /**
     * @var array $mapingFields
     */
    protected $mapingFields = [
        'supplier_id' => 'main_table.supplier_id',
    ];
    
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;
    /**
     * Collection constructor.
     *
     * @param EntityFactory                           $entityFactory
     * @param Logger                                  $logger
     * @param FetchStrategy                           $fetchStrategy
     * @param EventManager                            $eventManager
     * @param \Magento\Framework\App\RequestInterface $request
     * @param string                                  $mainTable
     * @param string                                  $resourceModel
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        \Magento\Framework\App\RequestInterface $request,
        $mainTable = 'os_supplier_transactions',
        $resourceModel = \Magestore\PurchaseOrderCustomization\Model\ResourceModel\Supplier\Transaction::class
    ) {
        $this->mapingFields = [
            'supplier_id' => 'main_table.supplier_id',
            'description_edited' => new \Zend_Db_Expr(
                "CONCAT(
                            main_table.description_option, 
                            ' - ', 
                            supplier_name, 
                            ' - ', 
                            main_table.doc_no,
                            ' - ',
                            main_table.chq_no,
                            ' - ',
                            main_table.amount
                        )"
            ),
            'credit_amount' => new \Zend_Db_Expr("
                        IF(main_table.type = '".\Magestore\PurchaseOrderCustomization\Model\Supplier\Transaction\Options\Type::TYPE_CREDIT."',main_table.amount,'')
                    "),
            'debit_amount' => new \Zend_Db_Expr("
                        IF(main_table.type = '".\Magestore\PurchaseOrderCustomization\Model\Supplier\Transaction\Options\Type::TYPE_DEBIT."',main_table.amount,'')
                    ")

        ];
        $this->request = $request;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }
    
    /**
     * Add field to filter
     *
     * @param array|string $field
     * @param null|string|array $condition
     *
     * @return \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if (in_array($field, array_keys($this->mapingFields))) {
            $field = $this->mapingFields[$field];
        }
        return parent::addFieldToFilter($field, $condition);
    }
    
    /**
     * Init select
     *
     * @return $this
     */
    public function _initSelect()
    {
        parent::_initSelect();
        $supplierId = $this->request->getParam('supplier_id');
        $this->addFieldToFilter('supplier_id', $supplierId);

//        echo $this->getSelect()->__toString();
//        die();

        $this->getSelect()
            ->joinLeft(
                ['supplier' => $this->getTable('os_supplier')],
                'main_table.supplier_id = supplier.supplier_id',
                []
            )->columns(
                [
                    'supplier_name' => new \Zend_Db_Expr("supplier.supplier_name"),
                ]
            )
            ->columns(
                [
                    'description_edited' => $this->mapingFields['description_edited'],
                    'credit_amount' => $this->mapingFields['credit_amount'],
                    'debit_amount' => $this->mapingFields['debit_amount']
                ]
            );
        return $this;
    }
}
