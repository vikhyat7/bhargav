<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Module\Dir;
use Magento\Framework\Filesystem\Driver\File as DriverInterface;
use Magento\Framework\Filesystem;
use Psr\Log\LoggerInterface;

/**
 * Class UpgradeData
 *
 * Used to upgrade data for module
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magento\Eav\Model\Entity\Type
     */
    protected $_entityTypeModel;
    /**
     * @var \Magento\Eav\Model\Entity\Attribute
     */
    protected $_catalogAttribute;
    /**
     * @var \Magento\Eav\Setup\EavSetup
     */
    protected $_eavSetup;
    /**
     * @var \Magestore\Giftvoucher\Model\Templateoptions
     */
    protected $templateOptions;
    /**
     * @var
     */
    protected $directory;
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $directoryWrite;
    /**
     * @var
     */
    protected $moduleReader;
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    protected $dataSetup;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var []
     */
    protected $_calculators;

    /**
     * @var \Magento\Framework\Math\CalculatorFactory
     */
    protected $_calculatorFactory;

    /**
     * @var \Magento\Framework\App\ProductMetadata
     */
    protected $productMetadata;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * @var DriverInterface
     */
    protected $driver;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * EAV setup factory
     *
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * UpgradeData constructor.
     *
     * @param \Magento\Eav\Setup\EavSetup $eavSetup
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     * @param \Magento\Eav\Model\Entity\Type $entityType
     * @param \Magento\Eav\Model\Entity\Attribute $catalogAttribute
     * @param \Magestore\Giftvoucher\Model\Source\TemplateOptions $templateoptions
     * @param Filesystem\DirectoryList $directoryList
     * @param Filesystem $filesystem
     * @param Dir\Reader $moduleReader
     * @param ModuleDataSetupInterface $dataSetup
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Framework\Math\CalculatorFactory $_calculatorFactory
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param \Magento\Framework\App\State $appState
     * @param DriverInterface $driver
     * @param LoggerInterface $logger
     * @throws \Magento\Framework\Exception\FileSystemException
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Eav\Setup\EavSetup $eavSetup,
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \Magento\Eav\Model\Entity\Type $entityType,
        \Magento\Eav\Model\Entity\Attribute $catalogAttribute,
        \Magestore\Giftvoucher\Model\Source\TemplateOptions $templateoptions,
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        \Magento\Framework\Setup\ModuleDataSetupInterface $dataSetup,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\Math\CalculatorFactory $_calculatorFactory,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\App\State $appState,
        DriverInterface $driver,
        LoggerInterface $logger
    ) {
        $this->_eavSetup = $eavSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->_entityTypeModel = $entityType;
        $this->_catalogAttribute = $catalogAttribute;
        $this->templateOptions = $templateoptions;
        $this->directory = $directoryList;
        $this->moduleReader = $moduleReader;
        $this->dataSetup = $dataSetup;
        $this->directoryWrite = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $this->orderFactory = $orderFactory;
        $this->_calculatorFactory = $_calculatorFactory;
        $this->productMetadata = $productMetadata;
        $this->_appState = $appState;
        $this->fileSystem = $filesystem;
        $this->driver = $driver;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $entityTypeModel = $this->_entityTypeModel;
        $catalogAttributeModel = $this->_catalogAttribute;
        $installer = $this->_eavSetup;

        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $defaultData = $this->templateOptions->getDefaultData();
            $data = [
                'group' => 'General',
                'type' => 'varchar',
                'input' => 'multiselect',
                'label' => 'Select Gift Card Templates ',
                'backend' => \Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend::class,
                'frontend' => '',
                'source' => \Magestore\Giftvoucher\Model\Source\TemplateOptions::class,
                'visible' => 1,
                'required' => 1,
                'user_defined' => 1,
                'used_for_price_rules' => 1,
                'position' => 2,
                'unique' => 0,
                'default' => $defaultData,
                'sort_order' => 100,
                'apply_to' => 'giftvoucher',
                'is_global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'is_required' => 0,
                'is_configurable' => 1,
                'is_searchable' => 0,
                'is_visible_in_advanced_search' => 0,
                'is_comparable' => 0,
                'is_filterable' => 0,
                'is_filterable_in_search' => 1,
                'is_used_for_promo_rules' => 1,
                'is_html_allowed_on_front' => 0,
                'is_visible_on_front' => 0,
                'used_in_product_listing' => 1,
                'used_for_sort_by' => 0,
            ];
            $installer->removeAttribute(
                $entityTypeModel->loadByCode('catalog_product')->getData('entity_type_id'),
                'gift_template_ids'
            );
            $data['required'] = 1;
            $data['is_required'] = 1;
            $installer->addAttribute(
                $entityTypeModel->loadByCode('catalog_product')->getData('entity_type_id'),
                'gift_template_ids',
                $data
            );
            $giftTemplateIds = $catalogAttributeModel->loadByCode(
                'catalog_product',
                'gift_template_ids'
            );
            $giftTemplateIds->addData($data)->save();

            $data['input'] = 'select';
            $data['label'] = __('Select The Gift Code Sets');
            $data['source'] = \Magestore\Giftvoucher\Model\Source\GiftCodeSetsOptions::class;
            $data['sort_order'] = 110;
            $data['default'] = '';
            $data['required'] = 0;

            $installer->addAttribute(
                $entityTypeModel->loadByCode('catalog_product')->getData('entity_type_id'),
                'gift_code_sets',
                $data
            );
            $giftCodeSets = $catalogAttributeModel->loadByCode('catalog_product', 'gift_code_sets');
            $giftCodeSets->addData($data)->save();

            $data['label'] = __('Select Gift Card Type');
            $data['source'] = \Magestore\Giftvoucher\Model\Source\GiftCardTypeOptions::class;
            $data['sort_order'] = 14;
            $data['default'] = '';
            $data['is_required'] = 1;
            $data['required'] = 1;

            $installer->addAttribute(
                $entityTypeModel->loadByCode('catalog_product')->getData('entity_type_id'),
                'gift_card_type',
                $data
            );
            $giftCardType = $catalogAttributeModel->loadByCode('catalog_product', 'gift_card_type');
            $giftCardType->addData($data)->save();
        }

        if (version_compare($context->getVersion(), '2.0.0', '<')) {
            // Update Attributes
            $this->_eavSetup->updateAttribute(
                'catalog_product',
                'gift_code_sets',
                'source_model',
                \Magestore\Giftvoucher\Model\Source\GiftCodeSetsOptions::class
            );
            $this->_eavSetup->updateAttribute(
                'catalog_product',
                'gift_card_type',
                'source_model',
                \Magestore\Giftvoucher\Model\Source\GiftCardTypeOptions::class
            );
            $this->_eavSetup->updateAttribute(
                'catalog_product',
                'gift_type',
                'source_model',
                \Magestore\Giftvoucher\Model\Source\GiftType::class
            );
            $this->_eavSetup->updateAttribute(
                'catalog_product',
                'gift_price_type',
                'source_model',
                \Magestore\Giftvoucher\Model\Source\GiftPriceType::class
            );
            $this->_eavSetup->updateAttribute(
                'catalog_product',
                'gift_template_ids',
                'source_model',
                \Magestore\Giftvoucher\Model\Source\TemplateOptions::class
            );

            $this->dataSetup->deleteTableRow(
                'eav_entity_attribute',
                'attribute_id',
                $installer->getAttributeId('catalog_product', 'gift_code_sets'),
                'attribute_set_id',
                $installer->getAttributeSetId('catalog_product', 'Default')
            );

            try {
                $giftVoucherViewDir = $this->moduleReader->getModuleDir(
                    Dir::MODULE_VIEW_DIR,
                    'Magestore_Giftvoucher'
                );
                $fromPath = $giftVoucherViewDir . '/frontend/web/images/template/images/default.png';
                $mediaGiftvoucherImageDir = $this->fileSystem
                    ->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath(
                        'giftvoucher/template/images'
                    );
                // Make destination directory
                if (!$this->driver->isDirectory($mediaGiftvoucherImageDir)) {
                    $this->driver->createDirectory($mediaGiftvoucherImageDir);
                }

                $this->driver->copy($fromPath, $mediaGiftvoucherImageDir . '/default.png');
            } catch (\Exception $exception) {
                $this->logger->error($exception->getMessage());
            }
        }

        if (version_compare($context->getVersion(), '2.2.0', '<')) {
            $version = $this->productMetadata->getVersion();
            try {
                if (version_compare($version, '2.2.0', '>=')) {
                    $this->_appState->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
                } else {
                    $this->_appState->setAreaCode('admin');
                }
            } catch (\Exception $e) {
                $this->_appState->getAreaCode();
            }
            $this->convertOrder($setup);
        }

        if (version_compare($context->getVersion(), '2.2.1', '<')) {
            $attributeArray = [
                'gift_type',
                'gift_value',
                'gift_from',
                'gift_to',
                'gift_dropdown',
                'gift_price_type',
                'gift_price',
                'gift_template_ids',
                'gift_card_type',
                'gift_code_sets'
            ];
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            foreach ($attributeArray as $attributeCode) {
                $this->disableSearchOfAttribute($attributeCode, $eavSetup);
            }
        }
    }

    /**
     * Disable search of attribute
     *
     * @param string $attributeCode
     * @param \Magento\Eav\Setup\EavSetup $eavSetup
     */
    public function disableSearchOfAttribute($attributeCode, $eavSetup)
    {
        $eavSetup->updateAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            $attributeCode,
            [
                'is_searchable' => 0,
                'searchable' => false,
                'used_in_product_listing' => false,
                'is_visible_in_advanced_search' => 0,
                'is_filterable' => 0,
                'is_filterable_in_search' => 0
            ]
        );
    }

    /**
     * Convert order
     *
     * @param ModuleDataSetupInterface $setup
     * @throws \Exception
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function convertOrder(ModuleDataSetupInterface $setup)
    {
        $orderTable = $setup->getTable('sales_order');
        $select = $setup->getConnection()->select();
        $select->from(['main_table' => $orderTable], ['entity_id'])
            ->where('base_gift_voucher_discount > ?', 0);
        $data = $setup->getConnection()->fetchAll($select);
        foreach ($data as $item) {
            /** @var \Magento\Sales\Model\Order $order */
            $order = $this->orderFactory->create()->load($item['entity_id']);
            $orderItems = $order->getAllItems();
            $store = $order->getStore();
            $totalItemsBaseGiftVoucherDiscountInvoiced = $totalItemsGiftVoucherDiscountInvoiced
                = $totalItemsBaseGiftVoucherDiscountRefunded = $totalItemsGiftVoucherDiscountRefunded = 0;
            foreach ($orderItems as $orderItem) {
                $qtyOrdered = $orderItem->getQtyOrdered();
                $qtyInvoiced = $orderItem->getQtyInvoiced();
                $qtyRefunded = $orderItem->getQtyRefunded();
                $baseGiftVoucherDiscount = $this->roundPrice(
                    $orderItem->getBaseGiftVoucherDiscount(),
                    true,
                    $store
                );
                $giftVoucherDiscount = $this->roundPrice($orderItem->getGiftVoucherDiscount(), true, $store);
                $baseDiscountAmount = $orderItem->getBaseDiscountAmount();
                $discountAmount = $orderItem->getDiscountAmount();
                $baseDiscountInvoiced = $orderItem->getBaseDiscountInvoiced();
                $discountInvoiced = $orderItem->getDiscountInvoiced();
                $baseDiscountRefunded = $orderItem->getBaseDiscountRefunded() ?
                    $orderItem->getBaseDiscountRefunded() : 0;
                $discountRefunded = $orderItem->getDiscountRefunded() ? $orderItem->getDiscountRefunded() : 0;
                $baseGiftvoucherDiscountInvoiced = $baseGiftVoucherDiscount / $qtyOrdered * $qtyInvoiced;
                $giftvoucherDiscountInvoiced = $giftVoucherDiscount / $qtyOrdered * $qtyInvoiced;
                $baseGiftvoucherDiscountRefunded = $baseGiftVoucherDiscount / $qtyOrdered * $qtyRefunded;
                $giftvoucherDiscountRefunded = $giftVoucherDiscount / $qtyOrdered * $qtyRefunded;
                $orderItem->setBaseDiscountAmount(
                    $baseDiscountAmount + $this->roundPrice($baseGiftVoucherDiscount, true, $store)
                );
                $orderItem->setDiscountAmount(
                    $discountAmount + $this->roundPrice($giftVoucherDiscount, true, $store)
                );
                $orderItem->setBaseDiscountInvoiced(
                    $baseDiscountInvoiced + $this->roundPrice(
                        $baseGiftvoucherDiscountInvoiced,
                        true,
                        $store
                    )
                );
                $orderItem->setDiscountInvoiced(
                    $discountInvoiced + $this->roundPrice(
                        $giftvoucherDiscountInvoiced,
                        true,
                        $store
                    )
                );
                $orderItem->setBaseDiscountRefunded(
                    $baseDiscountRefunded + $this->roundPrice(
                        $baseGiftvoucherDiscountRefunded,
                        true,
                        $store
                    )
                );
                $orderItem->setBaseDiscountRefunded(
                    $discountRefunded + $this->roundPrice(
                        $giftvoucherDiscountRefunded,
                        true,
                        $store
                    )
                );
                $orderItem->setMagestoreBaseDiscount($orderItem->getMagestoreBaseDiscount() + $baseGiftVoucherDiscount);
                $orderItem->setMagestoreDiscount($orderItem->getMagestoreDiscount() + $giftVoucherDiscount);
                $totalItemsBaseGiftVoucherDiscountInvoiced += $baseGiftvoucherDiscountInvoiced;
                $totalItemsGiftVoucherDiscountInvoiced += $giftvoucherDiscountInvoiced;
                $totalItemsBaseGiftVoucherDiscountRefunded += $baseGiftvoucherDiscountRefunded;
                $totalItemsGiftVoucherDiscountRefunded += $giftvoucherDiscountRefunded;
                $orderItem->save();
            }
            $baseDiscountAmount = $order->getBaseDiscountAmount();
            $discountAmount = $order->getDiscountAmount();
            $baseDiscountInvoiced = $order->getBaseDiscountInvoiced();
            $discountInvoiced = $order->getDiscountInvoiced();
            $baseDiscountRefunded = $order->getBaseDiscountRefunded() ? $order->getBaseDiscountRefunded() : 0;
            $discountRefunded = $order->getDiscountRefunded() ? $order->getDiscountRefunded() : 0;
            $baseGiftVoucherDiscount = $order->getBaseGiftVoucherDiscount();
            $giftVoucherDiscount = $order->getGiftVoucherDiscount();
            $order->setBaseDiscountAmount($baseDiscountAmount - $baseGiftVoucherDiscount);
            $order->setDiscountAmount($discountAmount - $giftVoucherDiscount);
            $order->setBaseDiscountInvoiced(
                $baseDiscountInvoiced - $this->roundPrice($totalItemsBaseGiftVoucherDiscountInvoiced, true, $store)
            );
            $order->setDiscountInvoiced(
                $discountInvoiced - $this->roundPrice($totalItemsGiftVoucherDiscountInvoiced, true, $store)
            );
            $order->setBaseDiscountRefunded(
                $baseDiscountRefunded - $this->roundPrice($totalItemsBaseGiftVoucherDiscountRefunded, true, $store)
            );
            $order->setDiscountRefunded(
                $discountRefunded - $this->roundPrice($totalItemsGiftVoucherDiscountRefunded, true, $store)
            );
            $order->save();
        }
    }

    /**
     * Round price
     *
     * @param float $price
     * @param bool $negative
     * @param \Magento\Store\Model\Store $store
     * @return mixed
     */
    public function roundPrice($price, $negative, $store)
    {
        if ($price) {
            if (!isset($this->_calculators[$store->getStoreId()])) {
                $this->_calculators[$store->getStoreId()] = $this->_calculatorFactory->create(['scope' => $store]);
            }
            $price = $this->_calculators[$store->getStoreId()]->deltaRound($price, $negative);
        }
        return $price;
    }
}
