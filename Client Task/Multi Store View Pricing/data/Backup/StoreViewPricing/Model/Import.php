<?php
/**
 * @category Mageants StoreViewPricing
 * @package Mageants_StoreViewPricing
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\StoreViewPricing\Model;

use Mageants\StoreViewPricing\Model\Import\RowValidatorInterface as ValidatorInterface;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\Framework\App\ResourceConnection;

/*
 * for import product store view pricing
 */
class Import extends \Mageants\StoreViewPricing\Model\Import\AbstractEntity
{
    const ID = 'id';
    const ENTITY_ID = 'entity_id';
    const PRICE = 'price';
    const STORE_ID = 'store_id';
    const SPECIAL_PRICE = 'special_price';
    const MSRP = 'msrp';
    const COST = 'cost';
    const SPECIAL_FROM_DATE = 'special_from_date';
    const SPECIAL_TO_DATE = 'special_to_date';
    const MSRP_DISPLAY_ACTUAL_PRICE_TYPE = 'msrp_display_actual_price_type';
    const TABLE_ENTITY = 'store_view_pricing';
    const TIER_PRICE = 'tier_price';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    // @codingStandardsIgnoreLine
    public $_messageTemplates = [
    ValidatorInterface::ERROR_TITLE_IS_EMPTY => 'Table is empty',
    ];

    // @codingStandardsIgnoreLine
    public $_permanentAttributes = [self::ID];
    /**
     * If we should check column names
     *
     * @var bool
     */
    public $needColumnCheck = true;
    public $groupFactory;
    /**
     * Valid column names
     *
     * @array
     */
    public $validColumnNames = [
    self::ID,
    'sku',
    self::PRICE,
    self::SPECIAL_PRICE,
    self::MSRP,
    self::COST,
    self::SPECIAL_FROM_DATE,
    self::SPECIAL_TO_DATE,
    self::MSRP_DISPLAY_ACTUAL_PRICE_TYPE,
    self::TIER_PRICE,
    self::STORE_ID,
    ];

    /**
     * Need to log in import history
     *
     * @var bool
     */
    public $logInHistory = true;

    // @codingStandardsIgnoreLine
    public $_validators = [];

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    // @codingStandardsIgnoreLine
    public $_connection;

    // @codingStandardsIgnoreLine
    public $_resource;

    // @codingStandardsIgnoreLine
    public $_productRepository;

    /**
     * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
     */
    /**
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper,
     * @param \Magento\ImportExport\Helper\Data $importExportData,
     * @param \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
     * @param \Magento\Framework\App\ResourceConnection $resource,
     * @param \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
     * @param \Magento\Framework\Stdlib\StringUtils $string,
     * @param ProcessingErrorAggregatorInterface $errorAggregator,
     * @param \Magento\Customer\Model\GroupFactory $groupFactory
     */
    // @codingStandardsIgnoreLine
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\ImportExport\Helper\Data $importExportData,
        \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Stdlib\StringUtils $string,
        ProcessingErrorAggregatorInterface $errorAggregator,
        \Magento\Customer\Model\GroupFactory $groupFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->_dataSourceModel = $importData;
        $this->_resource = $resource;
        $this->_connection = $resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $this->errorAggregator = $errorAggregator;
        $this->groupFactory = $groupFactory;
        $this->string = $string;
        $this->_productRepository = $productRepository;
    }
    public function getValidColumnNames()
    {
        return $this->validColumnNames;
    }

    /**
     * Entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'store_view_pricing';
    }

    /**
     * Entity storeview entity
     *
     * @return string
     */
    public function getStoreViewEntity()
    {
        return $this->_resource->getTableName('store_view_pricing');
    }

    /**
     * Row validation.
     *
     * @param array $rowData
     * @param int $rowNum
     * @return bool
     */
    public function validateRow(array $rowData, $rowNum)
    {
        $title = false;
    
        if (isset($this->_validatedRows[$rowNum])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }

        $this->_validatedRows[$rowNum] = true;
        // BEHAVIOR_DELETE use specific validation logic
        if (!isset($rowData[self::ID]) || empty($rowData[self::ID])) {
            $this->addRowError(ValidatorInterface::ERROR_TITLE_IS_EMPTY, $rowNum);
            return false;
        }

        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }

    /**
     * Create Advanced price data from raw data.
     *
     * @throws \Exception
     * @return bool Result of operation.
     */
    public function _importData()
    {
        if (\Magento\ImportExport\Model\Import::BEHAVIOR_DELETE == $this->getBehavior()) {
            $this->deleteEntity();
        } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $this->getBehavior()) {
            $this->replaceEntity();
        } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_APPEND == $this->getBehavior()) {
            $this->saveEntity();
        } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_ADD_UPDATE == $this->getBehavior()) {
            $this->saveEntity();
        }

        return true;
    }
    /**
     * Save newsletter subscriber
     *
     * @return $this
     */
    public function saveEntity()
    {
        $this->saveAndReplaceEntity();
        return $this;
    }
    /**
     * Replace newsletter subscriber
     *
     * @return $this
     */
    public function replaceEntity()
    {
        $this->saveAndReplaceEntity();
        return $this;
    }
    /**
     * Deletes newsletter subscriber data from raw data.
     *
     * @return $this
     */
    public function deleteEntity()
    {
        $listTitle = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNum => $rowData) {
                $this->validateRow($rowData, $rowNum);
                if (!$this->getErrorAggregator()->isRowInvalid($rowNum)) {
                    $rowTtile = $rowData[self::ID];
                    $listTitle[] = $rowTtile;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                }
            }
        }
        if ($listTitle) {
            $this->deleteEntityFinish(array_unique($listTitle), $this->getStoreViewEntity());
        }
        return $this;
    }
    /**
     * Save and replace newsletter subscriber
     *
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    // @codingStandardsIgnoreLine
    public function saveAndReplaceEntity()
    {
        $behavior = $this->getBehavior();
        $listTitle = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $entityList = [];
            foreach ($bunch as $rowNum => $rowData) {
                if (!$this->validateRow($rowData, $rowNum)) {
                    $this->addRowError(ValidatorInterface::ERROR_TITLE_IS_EMPTY, $rowNum);
                    continue;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }
                $product = $this->_productRepository->get($rowData['sku']);
                
                if ($product->getId()) {
                    $rowTtile= $rowData[self::ID];

                    $listTitle[] = $rowTtile;
                    $entityList[$rowTtile][] = [
                        self::ID => $rowData[self::ID],
                        self::ENTITY_ID => $product->getId(),
                        self::PRICE => $rowData[self::PRICE],
                        self::STORE_ID => $rowData[self::STORE_ID],
                        self::SPECIAL_PRICE => $rowData[self::SPECIAL_PRICE],
                        self::MSRP => $rowData[self::MSRP],
                        self::COST => $rowData[self::COST],
                        self::SPECIAL_FROM_DATE => $rowData[self::SPECIAL_FROM_DATE],
                        self::SPECIAL_TO_DATE => $rowData[self::SPECIAL_TO_DATE],
                        self::MSRP_DISPLAY_ACTUAL_PRICE_TYPE => $rowData[self::MSRP_DISPLAY_ACTUAL_PRICE_TYPE],
                        self::TIER_PRICE => $rowData[self::TIER_PRICE],
                    ];
                    
                    $fromDate = date_create($rowData[self::SPECIAL_FROM_DATE]);
                    $fromDateFormat = date_format($fromDate,"Y-m-d H:i:s");
                    $toDate = date_create($rowData[self::SPECIAL_TO_DATE]);
                    $toDateFormat = date_format($toDate,"Y-m-d H:i:s");
                    $storeId = $rowData[self::STORE_ID];
                    $array_product = [$product->getId()]; //product Ids

                    if (!empty($rowData[self::SPECIAL_FROM_DATE])) {

                        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                        $productActionObject = $objectManager->create('Magento\Catalog\Model\Product\Action');
                        $productActionObject->updateAttributes($array_product, 
                            array(
                                'special_to_date'                => $toDateFormat,
                                'special_from_date'              => $fromDateFormat,
                                // 'price'                          => $price,
                                // 'special_price'                  => $special_price,
                                // 'cost'                           => $cost,
                                // 'msrp'                           => $msrp,
                                // 'msrp_display_actual_price_type' => $msrp_display_actual_price_type
                        ), $storeId);
                    }
                }
            }
            if (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $behavior) {
                if ($listTitle) {
                    if ($this->deleteEntityFinish(array_unique($listTitle), $this->getStoreViewEntity())) {
                        $this->saveEntityFinish($entityList, $this->getStoreViewEntity());
                    }
                }
            } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_APPEND == $behavior) {
                $this->saveEntityFinish($entityList, $this->getStoreViewEntity());
            } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_ADD_UPDATE == $behavior) {
                $this->saveEntityFinish($entityList, $this->getStoreViewEntity());
            }
        }
        return $this;
    }
    /**
     * Save product prices.
     *
     * @param array $priceData
     * @param string $table
     * @return $this
     */
    public function saveEntityFinish(array $entityData, $table)
    {
        if ($entityData) {
            $tableName = $this->_connection->getTableName($table);
            $entityIn = [];
            foreach ($entityData as $id => $entityRows) {
                foreach ($entityRows as $row) {
                    $entityIn[] = $row;
                }
            }
            if ($entityIn) {
                $this->_connection->insertOnDuplicate($tableName, $entityIn, [
                self::ID,
                self::ENTITY_ID,
                self::PRICE,
                self::SPECIAL_PRICE,
                self::MSRP,
                self::COST,
                self::SPECIAL_FROM_DATE,
                self::SPECIAL_TO_DATE,
                self::MSRP_DISPLAY_ACTUAL_PRICE_TYPE,
                self::TIER_PRICE,
                self::STORE_ID,
                ]);
            }
        }
        return $this;
    }

    /**
     * delete entity finished
     *
     * @param array $listTitle
     * @param string $table
     * @return $this
     */
    public function deleteEntityFinish(array $listTitle, $table)
    {
        if ($table && $listTitle) {
            try {
                $this->countItemsDeleted += $this->_connection->delete(
                    $this->_connection->getTableName($table),
                    $this->_connection->quoteInto('id IN (?)', $listTitle)
                );
                return true;
            } catch (\Exception $e) {
                return false;
            }
        } else {
            return false;
        }
    }
}
