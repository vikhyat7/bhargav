<?php
/**
 * @category Mageants CustomStockStatus
 * @package Mageants_CustomStockStatus
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <info@mageants.com>
 */

namespace Mageants\CustomStockStatus\Helper;

use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku;
use Magento\Framework\App\RequestInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public $adapterFactory;

    public $uploader;

    public $filesystem;

    public $messageManager;

    public $scopeConfig;

    public $customIconManage;

    public $customRuleManage;

    public $stockItemRepository;

    public $eavConfig;

    public $objectManager;

    public $productloader;

    public $date;

    public $productRepository;

    public $postHelper;

    public $jsonHelper;

    const ICON_PATH = 'mageants/customStatusIcon/images/';

    const CUSTOM_STOCK_STATUS_ATTRIBUTE = 'mageants_custom_stock_status';
    
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        \Mageants\CustomStockStatus\Model\File\UploaderFactory $uploader,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Message\Manager $messageManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Mageants\CustomStockStatus\Model\CustomStockStFactory $CustomIconManage,
        \Mageants\CustomStockStatus\Model\CustomStockRuleFactory $CustomRuleManage,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Model\ProductFactory $productloader,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\Data\Helper\PostHelper $postHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        GetSalableQuantityDataBySku $getSalableQuantityDataBySku
    ) {
        $this->request = $request;
        $this->adapterFactory = $adapterFactory;
        $this->uploader = $uploader;
        $this->filesystem = $filesystem;
        $this->messageManager   = $messageManager;
        $this->scopeConfig = $scopeConfig;
        $this->customIconManage = $CustomIconManage;
        $this->customRuleManage = $CustomRuleManage;
        $this->stockItemRepository = $stockItemRepository;
        $this->eavConfig = $eavConfig;
        $this->objectManager = $objectManager;
        $this->productloader = $productloader;
        $this->date = $date;
        $this->productRepository = $productRepository;
        $this->postHelper = $postHelper;
        $this->jsonHelper = $jsonHelper;
        $this->getSalableQuantityDataBySku = $getSalableQuantityDataBySku;
    }

    public function getVisibleBothStatus()
    {
        $visibleStatus = $this->scopeConfig->getValue(
            'CustomStockSt/general/outofstockitem',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $visibleStatus;
    }

    public function getHideStockStatus()
    {
        $hideStatus = $this->scopeConfig->getValue(
            'CustomStockSt/general/hidestockst',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $hideStatus;
    }

    public function getUseQtyRange()
    {
        $useQtyRange = $this->scopeConfig->getValue(
            'CustomStockSt/general/useqtyrange',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $useQtyRange;
    }

    public function getRuleQtyRange()
    {
        $ruleQtyRange = $this->scopeConfig->getValue(
            'CustomStockSt/general/ruleqtyrange',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $ruleQtyRange;
    }

    public function getDisplayIcon()
    {
        $displayIcon = $this->scopeConfig->getValue(
            'CustomStockSt/general/displayicon',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $displayIcon;
    }
    public function getDisplayOrderItemGrid()
    {
        $displayStatusandIcon = $this->scopeConfig->getValue(
            'CustomStockSt/display_setting/displayoncustomeraccountadmin',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $displayStatusandIcon;
    }

    public function getBackInStockAlert()
    {
        $stockAlert = $this->scopeConfig->getValue(
            'CustomStockSt/general/backinstockconfig',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $stockAlert;
    }

    public function getOutOfStockAttribute()
    {
        $configAttribute = $this->scopeConfig->getValue(
            'CustomStockSt/general/outofstockconfigattribute',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $configAttribute;
    }

    public function getChangeConfigProduct()
    {
        $changeConfigPro = $this->scopeConfig->getValue(
            'CustomStockSt/general/changeconfigproductst',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $changeConfigPro;
    }

    public function getLoadProduct($id)
    {
        return $this->productloader->create()->load($id);
    }

    public function getCustomStockLabel($productOptionId, $productOptionRule, $productOptionQtyRule, $productId)
    {
        
        $useQtyRange = $this->getUseQtyRange();
        $ruleQtyRange = $this->getRuleQtyRange();
        $customRule = $this->customRuleManage->create();

        // $productLoad = $this->stockItemRepository->get($productId);
        $productType = $this->getLoadProduct($productId);
        // $productLoad = $this->getSalableQuantityDataBySku->execute($productType->getSku());
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productRepository = $objectManager->create('\Magento\Catalog\Api\ProductRepositoryInterface');
        $product = $productRepository->getById($productId);
        $productStock = $product->getExtensionAttributes()->getStockItem();
        // echo $productStock['backorders'];
        $backorderRule = $productStock['backorders'];
        // $backorderRule = $productLoad->getBackorders();
        // configurable , grouped , bundle
        if ($productType->getTypeId() == 'simple') {
            $stockItem = $this->getSalableQuantityDataBySku->execute($productType->getSku());
            $defaultStockItem = 0;
            if (isset($stockItem) && !empty($stockItem)) {
                foreach ($stockItem as $stockItems) {
                    if (isset($stockItems['qty'])) {
                        $defaultStockItem = $stockItems['qty'];
                    }
                }
            }

            $fullActionName  = $this->request->getFullActionName();

            if ($fullActionName == 'sales_order_view') {
                // echo "if 1";
                // $productQty = $productLoad->getQty();
     
                $product_salable_qty = "";
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $StockState = $objectManager->get(\Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku::class);
                $order_id = $objectManager->get(\Magento\Framework\App\RequestInterface::class)->getParam("order_id");
                $getOrderData = $objectManager->get(\Magento\Sales\Api\OrderRepositoryInterface::class)->get($order_id);
                foreach ($getOrderData->getAllVisibleItems() as $_item) {
                    $qty = $StockState->execute($_item->getSku());
                    $product_salable_qty = $qty[0]['qty'];
                }
                if($productOptionRule == null){
                    $productQty = $product_salable_qty;
                }
                else{
                    // $productQty = $productLoad->getQty();
                    $productQty = 0;
                }
            } 
            else {
                if ($defaultStockItem > 0) {
                    // echo "if 2";
                    // $product_salable_qty = "";
                    // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    // $current_product = $objectManager->get(\Magento\Framework\Registry::class)->registry('current_product');
                    // $StockState = $objectManager->get(\Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku::class);
                    // if($current_product !== null){
                    //     $qty = $StockState->execute($current_product->getSku());
                    //     $product_salable_qty = $qty[0]['qty'];
                    // }
                    
                    if($productOptionRule == null){
                        $productQty = $defaultStockItem;
                    }
                    else{
                        // $productQty = $productLoad[0]['qty'];
                        $productLoad = $this->stockItemRepository->get($productId);
                        $productQty = $productLoad->getQty();
                        // $productQty = $productLoad[0]['qty'];
                    }
                    
                } else {
                    // echo "else 1";
                    $productQty = 0;
                }
            }
        } else {
            // echo "else 2";exit();
            // $productQty = $productLoad->getQty();
            // var_dump($productQty);exit();
            // print_r($productLoad);
            // exit();
            $productQty = 0;
        }
        $customStockSt = [];
        $foundRule = 0;

        $customStatus = $customRule->getCollection()->addFieldToFilter('option_id', $productOptionId);

        if ($productOptionId && !$productOptionQtyRule && !$productOptionRule) {
            // echo "if";
            $customStockSt = $this->getAttributeOption($productOptionId, $productId, $productQty);
          
            return $customStockSt;
        } elseif ($productOptionId && !$productOptionQtyRule && $productOptionRule) {
            $customStockSt = $this->getAttributeOption($productOptionId, $productId, $productQty);
          
            return $customStockSt;
        }


        if ($useQtyRange && $productOptionQtyRule && $ruleQtyRange && $productOptionRule) {
            $foundRule = 1;
            $customStatus = $customStatus->addFieldToFilter('rule_id', $productOptionRule);
        } elseif ($backorderRule && $ruleQtyRange && $useQtyRange) {
            $foundRule = 1;
        } else {
            $customStatus = $customStatus->addFieldToFilter('rule_id', $backorderRule);
        }
        if (count($customStatus) >= 1) {
            foreach ($customStatus as $statusData) {
                if (!$ruleQtyRange && $productOptionRule && $foundRule == 0) {
                    continue;
                }

                if ($productQty >= $statusData['from'] && $productQty <= $statusData['to'] &&
                    $useQtyRange && $productOptionQtyRule) {
                    if ($productOptionQtyRule && $useQtyRange) {
                        $customStockSt = $this->getAttributeOption(
                            $statusData['option_id'],
                            $productId,
                            $productQty
                        );
                        return $customStockSt;
                    } else {
                        continue;
                    }
                } elseif ($productOptionId && !$productOptionQtyRule && !$productOptionRule) {
                    $customStockSt = $this->getAttributeOption($statusData['option_id'], $productId, $productQty);
                    return $customStockSt;
                } else {
                    if ($productOptionId && $productOptionQtyRule && $productOptionRule) {
                        $customStockSt = [];
                        $customRuleData = $customRule->getCollection()->load()->getData();
                        if (!empty($customRuleData)) {
                            foreach ($customRuleData as $customRulevalue) {
                                $min = $customRulevalue['from'];
                                $max = $customRulevalue['to'];
                                if ($productQty >= $min && $productQty <= $max) {
                                    $customStockSt = $this->getAttributeOption($customRulevalue['option_id'], $productId, $productQty);
                                    break;
                                }
                            }
                        }
 
                        return $customStockSt;
                    }
                }
            }
        } else {
            // echo "else";exit();
            $customStockSt = [];
            $customRuleData = $customRule->getCollection()->load()->getData();
            if (!empty($customRuleData) && $productOptionQtyRule) {
                foreach ($customRuleData as $customRulevalue) {
                    $min = $customRulevalue['from'];
                    $max = $customRulevalue['to'];
                    if ($productQty >= $min && $productQty <= $max) {
                        $customStockSt = $this->getAttributeOption($customRulevalue['option_id'], $productId, $productQty);
                        break;
                    }
                }
            }
            //var_dump($customStockSt);exit();
            return $customStockSt;
        }
        
       // return $customStockSt;
    }

    public function getAttributeOption($optionId, $productId, $productQty)
    {
        $attributeDetails = $this->eavConfig->getAttribute("catalog_product", self::CUSTOM_STOCK_STATUS_ATTRIBUTE);
        $allOptions = $attributeDetails->getSource()->getAllOptions();

        $customStockSt = [];
        if (count($allOptions) >= 1) {
            foreach ($allOptions as $options) {
                if ($options['value'] == $optionId) {
                    $customIcon = $this->customIconManage->create();

                    $optionIconCollection = $customIcon->getCollection()->addFieldToFilter(
                        'option_id',
                        $optionId
                    )->getFirstItem();

                    $mediaPath= $this->getMediaImagePath();
                    if ($optionIconCollection->getData('icon')) {
                        $customStockSt['icon'] = $mediaPath.$optionIconCollection->getData('icon');
                    } else {
                        $customStockSt['icon'] = '';
                    }
                               
                    $customStockSt['label'] = $this->getOptionVariable($options['label'], $productId, $productQty);

                    return $customStockSt;
                }
            }
        }
        
        return $customStockSt;
    }

    public function getOptionVariable($option, $productId, $productQty)
    {   
        if (strpos($option, '{qty}') !== false) {
            $option = str_replace('{qty}',$productQty,$option);
        } elseif (strpos($option, '{special_price}') !== false) {
            $product = $this->productRepository->getById($productId);
            $productSpacialPrice = number_format($product->getSpecialPrice(), 2);

            $option = str_replace('{special_price}', $productSpacialPrice, $option);
        } elseif (strpos($option, '{day-after-tomorrow}') !== false) {
            $date = $this->date->date();
            $newDate = date("M j, Y", strtotime($date.'+2 day'));
            $option = str_replace('{day-after-tomorrow}', $newDate, $option);
        } elseif (strpos($option, '{tomorrow}') !== false) {
            $date = $this->date->date();
            $newDate = date("M j, Y", strtotime($date.'+1 day'));
            $option = str_replace('{tomorrow}', $newDate, $option);
        } 
        else {
            return $option;
        }
        // var_dump($option);
        return $option;
    }

    public function getConfigurableAttribute($attributesData)
    {   
        echo "call";
// echo '<pre>';        
        $i=1;
        foreach ($attributesData['attributes'] as $productAttribute) {
            
            foreach ($productAttribute['options'] as $key => $attribute) {
                
                $optionId[$i][$attribute['id']]= $attribute['products'];
                
            }
            
            $i++;
        }
    // print_r($optionId);
        $optionIdArray = array_shift($optionId);
// print_r($optionIdArray);
        $mergeOptionIdArray = $optionId;

        $configAttributeData = [];
        $removeKey =[];

// print_r($optionIdArray);

        foreach ($optionIdArray as $key => $mainOptVal) {
            $mainOptKey = $key;
            $getSize = sizeof($mainOptVal);
            // print_r($mergeOptionIdArray);
            // foreach ($mergeOptionIdArray as $mergeOptionId) {
            //     $j=0;
            //     foreach ($mergeOptionId as $childKey => $optionValue) {
            //         if (in_array($mainOptVal[$j], $optionValue)) {
            //             $product = $this->getLoadProduct($mainOptVal[$j]);
                        
            //             $productOptionId = $product->getMageantsCustomStockStatus();
                        
            //             $productOptionRule = $product->getMageantsCustomStockRule();
            //             $productOptionQtyRule = $product->getMageantsQtyBaseRuleStatus();
            //             $productId = $product->getId();

            //             $customLable = $this->getCustomStockLabel(
            //                 $productOptionId,
            //                 $productOptionRule,
            //                 $productOptionQtyRule,
            //                 $productId
            //             );
            //             $productLoad = $this->stockItemRepository->get($productId);
            //             $productInStock = $productLoad->getIsInStock();

            //             $hideDefaultSt = $this->getHideStockStatus();
            //             $productStockAlert = ' ';
            //             $productStockLb = ' ';
            //             if ($productInStock) {
            //                 $productInStock = 1;

            //                 if (!$hideDefaultSt) {
            //                     $productStockLb = "In stock";
            //                 }
            //             } else {
            //                 $productInStock = 0;

            //                 if (!$hideDefaultSt) {
            //                     $productStockLb = "Out of stock";
            //                 }

            //                 $StockAlertEnable = $this->getBackInStockAlert();

            //                 if ($StockAlertEnable) {
            //                     $baseUrl = $this->objectManager->get('Magento\Store\Model\StoreManagerInterface')
            //                     ->getStore()
            //                     ->getBaseUrl();

            //                     $productAlert = $baseUrl."productalert/add/stock/product_id/".$productId;
                                    
            //                     $productAlertData = $this->postHelper->getPostData($productAlert);

            //                     $productAlertArray = $this->jsonHelper->jsonDecode($productAlertData);

            //                     $productAlertEncodeUrl = $productAlert."/uenc/".$productAlertArray['data']['uenc'];

            //                     $productAlertUrl = $this->postHelper->getPostData($productAlertEncodeUrl);

            //                     $stockAlertLabel = 'Sign up to get notified when this configuration is back in stock';

            //                     $productStockAlert = '<div class="product alert alert stock link-stock-alert">
            //                             <a href="#" data-post='.$productAlertUrl.'
            //                                title='.$stockAlertLabel.' class="action alert">'.$stockAlertLabel.'
            //                             </a>
            //                         </div>';
            //                 }
            //             }

            //             $customStatusText = '';
            //             if (is_array($customLable)) {
            //                 if (array_key_exists("label", $customLable)) {
            //                     $customStatusText = $customLable['label'];
            //                     $removeKey[$mainOptKey] = "yes";
            //                 }
            //             }

            //             $customStatus = '';
            //             $customStatusOptionClass= 'customstockstatus status_'.$mainOptKey;

            //             if (is_array($customLable)) {
            //                 if (array_key_exists("label", $customLable) && array_key_exists("icon", $customLable)) {
            //                     if (!empty($customLable['icon'])) {
            //                         $customStatus = $productStockLb.'<img class="custom_stock_status_icon"
            //                     src='.$customLable['icon'].' alt="" title=""/>';
            //                     }
            //                     if (!empty($customLable['label'])) {
            //                         $customStatus = $productStockLb.'<span 
            //                     class="'.$customStatusOptionClass.'">'.$customLable['label'].'</span>';
            //                     }
            //                 } elseif ($customStatusText != null) {
            //                     $customStatus = $productStockLb.'<span 
            //                     class="'.$customStatusOptionClass.'">'.$customLable['label'].'</span>';
            //                 }
            //             }
                       
            //             $customStatusIcon= '';
            //             if (is_array($customLable)) {
            //                 if (array_key_exists("icon", $customLable)) {
            //                     if (!empty($customLable['icon'])) {
            //                         $customStatusIcon ='<img src='.$customLable['icon'].'
            //                     class="custom_stock_status_icon" alt="" title="">';
            //                     }
            //                 }
            //             }

            //             $customStatusIconOnly = $this->getDisplayIcon();
     
            //             // $changeConfigurableStatus = $this->getChangeConfigProduct();
                        
            //             if ($j==0) {
            //                 $configAttributeData[$mainOptKey] = [

            //                     'is_in_stock' => $productInStock,
            //                     'custom_stock_status_text' => $customStatusText,
            //                     'custom_stock_status' => $customStatus,
            //                     'custom_stock_status_icon' => $customStatusIcon,
            //                     'custom_stock_status_icon_only' => $customStatusIconOnly,
            //                     'product_id' => $productId,
            //                   ];
            //             }

            //             if ($productStockAlert != null) {
            //                 $configAttributeData[$mainOptKey.",".$childKey] = [

            //                 'is_in_stock' => $productInStock,
            //                 'custom_stock_status_text' => $customStatusText,
            //                 'custom_stock_status' => $customStatus,
            //                 'custom_stock_status_icon' => $customStatusIcon,
            //                 'custom_stock_status_icon_only' => $customStatusIconOnly,
            //                 'product_id' => $productId,
            //                 'stockalertmessage' => $productStockAlert,

            //                 ];
            //             } else {
            //                 $configAttributeData[$mainOptKey.",".$childKey] = [

            //                 'is_in_stock' => $productInStock,
            //                 'custom_stock_status_text' => $customStatusText,
            //                 'custom_stock_status' => $customStatus,
            //                 'custom_stock_status_icon' => $customStatusIcon,
            //                 'custom_stock_status_icon_only' => $customStatusIconOnly,
            //                 'product_id' => $productId,

            //                 ];
            //             }
            //             $j++;
            //         }
            //     }
            // }
            if ($getSize > 1) { 
                foreach ($mergeOptionIdArray as $mergeOptionId) {
                    $j=0;
                    foreach ($mergeOptionId as $childKey => $optionValue) {

                        if (in_array($mainOptVal[$j], $optionValue)) {
                            // var_dump($mainOptVal[$j]);
                            $product = $this->getLoadProduct($mainOptVal[$j]);
                            
                            $productOptionId = $product->getMageantsCustomStockStatus();
                            $productOptionRule = $product->getMageantsCustomStockRule();
                            $productOptionQtyRule = $product->getMageantsQtyBaseRuleStatus();
                            $productId = $product->getId();

                            $customLable = $this->getCustomStockLabel(
                                $productOptionId,
                                $productOptionRule,
                                $productOptionQtyRule,
                                $productId
                            );
                            $productLoad = $this->stockItemRepository->get($productId);
                            $productInStock = $productLoad->getIsInStock();

                            $hideDefaultSt = $this->getHideStockStatus();
                            $productStockAlert = ' ';
                            $productStockLb = ' ';
                            if ($productInStock) {
                                $productInStock = 1;

                                if (!$hideDefaultSt) {
                                    $productStockLb = "In stock";
                                }
                            } else {
                                $productInStock = 0;

                                if (!$hideDefaultSt) {
                                    $productStockLb = "Out of stock";
                                }

                                $StockAlertEnable = $this->getBackInStockAlert();

                                if ($StockAlertEnable) {
                                    $baseUrl = $this->objectManager->get('Magento\Store\Model\StoreManagerInterface')
                                    ->getStore()
                                    ->getBaseUrl();

                                    $productAlert = $baseUrl."productalert/add/stock/product_id/".$productId;
                                        
                                    $productAlertData = $this->postHelper->getPostData($productAlert);

                                    $productAlertArray = $this->jsonHelper->jsonDecode($productAlertData);

                                    $productAlertEncodeUrl = $productAlert."/uenc/".$productAlertArray['data']['uenc'];

                                    $productAlertUrl = $this->postHelper->getPostData($productAlertEncodeUrl);

                                    $stockAlertLabel = 'Sign up to get notified when this configuration is back in stock';

                                    $productStockAlert = '<div class="product alert alert stock link-stock-alert">
                                            <a href="#" data-post='.$productAlertUrl.'
                                               title='.$stockAlertLabel.' class="action alert">'.$stockAlertLabel.'
                                            </a>
                                        </div>';
                                }
                            }

                            $customStatusText = '';
                            if (is_array($customLable)) {
                                if (array_key_exists("label", $customLable)) {
                                    $customStatusText = $customLable['label'];
                                    $removeKey[$mainOptKey] = "yes";
                                }
                            }

                            $customStatus = '';
                            $customStatusOptionClass= 'customstockstatus status_'.$mainOptKey;

                            if (is_array($customLable)) {
                                if (array_key_exists("label", $customLable) && array_key_exists("icon", $customLable)) {
                                    if (!empty($customLable['icon'])) {
                                        $customStatus = $productStockLb.'<img class="custom_stock_status_icon"
                                    src='.$customLable['icon'].' alt="" title=""/>';
                                    }
                                    if (!empty($customLable['label'])) {
                                        $customStatus = $productStockLb.'<span 
                                    class="'.$customStatusOptionClass.'">'.$customLable['label'].'</span>';
                                    }
                                } elseif ($customStatusText != null) {
                                    $customStatus = $productStockLb.'<span 
                                    class="'.$customStatusOptionClass.'">'.$customLable['label'].'</span>';
                                }
                            }
                           
                            $customStatusIcon= '';
                            if (is_array($customLable)) {
                                if (array_key_exists("icon", $customLable)) {
                                    if (!empty($customLable['icon'])) {
                                        $customStatusIcon ='<img src='.$customLable['icon'].'
                                    class="custom_stock_status_icon" alt="" title="">';
                                    }
                                }
                            }

                            $customStatusIconOnly = $this->getDisplayIcon();
         
                            // $changeConfigurableStatus = $this->getChangeConfigProduct();
                            
                            if ($j==0) {
                                $configAttributeData[$mainOptKey] = [

                                    'is_in_stock' => $productInStock,
                                    'custom_stock_status_text' => $customStatusText,
                                    'custom_stock_status' => $customStatus,
                                    'custom_stock_status_icon' => $customStatusIcon,
                                    'custom_stock_status_icon_only' => $customStatusIconOnly,
                                    'product_id' => $productId,
                                  ];
                            }

                            if ($productStockAlert != null) {
                                $configAttributeData[$mainOptKey.",".$childKey] = [

                                'is_in_stock' => $productInStock,
                                'custom_stock_status_text' => $customStatusText,
                                'custom_stock_status' => $customStatus,
                                'custom_stock_status_icon' => $customStatusIcon,
                                'custom_stock_status_icon_only' => $customStatusIconOnly,
                                'product_id' => $productId,
                                'stockalertmessage' => $productStockAlert,

                                ];
                            } else {
                                $configAttributeData[$mainOptKey.",".$childKey] = [

                                'is_in_stock' => $productInStock,
                                'custom_stock_status_text' => $customStatusText,
                                'custom_stock_status' => $customStatus,
                                'custom_stock_status_icon' => $customStatusIcon,
                                'custom_stock_status_icon_only' => $customStatusIconOnly,
                                'product_id' => $productId,

                                ];
                            }
                            $j++;
                        }
                    }
                }
            }
            else{
                foreach ($mainOptVal as $value) {
                    // var_dump($value);
                    $product = $this->getLoadProduct($value);
                    // var_dump($product->getId());
                            
                    $productOptionId = $product->getMageantsCustomStockStatus();
                    $productOptionRule = $product->getMageantsCustomStockRule();
                    $productOptionQtyRule = $product->getMageantsQtyBaseRuleStatus();
                    $productId = $product->getId();

                    $customLable = $this->getCustomStockLabel(
                        $productOptionId,
                        $productOptionRule,
                        $productOptionQtyRule,
                        $productId
                    );
                    // var_dump($customLable);
                    $productLoad = $this->stockItemRepository->get($productId);
                    $productInStock = $productLoad->getIsInStock();
                    $productStockLb = "";
                    if ($productInStock == true) {
                        $productStockLb = "In Stock";
                    }
                    else{
                        $productStockLb = "Out of Stock";    
                    }
                    $customStatusIconOnly = $this->getDisplayIcon();
                    /*$customStatus = $productStockLb.'<img class="custom_stock_status_icon"
                                    src='.$customLable['icon'].' alt="" title=""/>';*/
                    if (!empty($customLable)) {
                        $configAttributeData[$mainOptKey] = [
                            'is_in_stock' => $productStockLb,
                            'custom_stock_status_text' => $customLable['label'],
                            'custom_stock_status' => $customLable['label'],
                            'custom_stock_status_icon' => '<img class="custom_stock_status_icon"
                                        src='.$customLable['icon'].' alt="" title=""/>',
                            'custom_stock_status_icon_only' => $customStatusIconOnly,
                            'product_id' => $productId,
                        ];                    
                    }
                } 
            }
        }
// exit;
        foreach ($configAttributeData as $key => $value) {
            if (array_key_exists($key, $removeKey)) {
                $configAttributeData[$key] = null;
            }
        }
       

        $configAttributeData['changeConfigurableProductStatus'] = $this->getChangeConfigProduct();
        $configAttributeData['type'] = "product.info.options.swatches";

        return $configAttributeData;
    }

    public function getConfigurableAttributeDropdown($simpleProductId, $attributeData)
    {
        // print_r($simpleProductId);
        // print_r($attributeData->getData('options'));
        
        foreach ($attributeData as $attribute) {
            foreach ($attribute->getData('options') as $key => $optioId) {
                $productId = $simpleProductId[$key];
                $customLable['label'] = '';
                $customLable['icon'] = '';
                $product = $this->getLoadProduct($productId);


                $productOptionId = $product->getMageantsCustomStockStatus();
                
                $productOptionRule = $product->getMageantsCustomStockRule();
                $productOptionQtyRule = $product->getMageantsQtyBaseRuleStatus();
                

                $getCustomStock = $this->getCustomStockLabel(
                    $productOptionId,
                    $productOptionRule,
                    $productOptionQtyRule,
                    $productId
                );

                if (!empty($getCustomStock)) {
                    $customLable = $getCustomStock;
                }
                // echo $productId;
// $objectManager =  \Magento\Framework\App\ObjectManager::getInstance(); 
// $stockItem = $objectManager->get('\Magento\CatalogInventory\Model\Stock\StockItemRepository');
// $productStockInfo = $stockItem->get($productId);
// print_r($productStockInfo->getData());
                // exit;
 $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
 $StockState = $objectManager->get('\Magento\CatalogInventory\Api\StockStateInterface');
//  echo $StockState->getStockQty($product->getId(), $product->getStore()->getWebsiteId());
 $productId = $product->getId();
 
                // exit();
                // $productLoad = $this->stockItemRepository->get($productId);
                // $productLoad = $this->stockItemRepository->get(13474);
                
                
// print_r($productLoad->getData());
// exit;
                // $productInStock = $productLoad->getIsInStock();
                $productInStock = 1;
                $hideDefaultSt = $this->getHideStockStatus();

                $productStockAlert = ' ';
                $productStockLb = ' ';
                if ($productInStock) {
                    $productInStock = 1;

                    if (!$hideDefaultSt) {
                        $productStockLb = "In stock";
                    }
                } else {
                    $productInStock = 0;

                    if (!$hideDefaultSt) {
                        $productStockLb = "Out of stock";
                    }

                    $StockAlertEnable = $this->getBackInStockAlert();

                    if ($StockAlertEnable) {
                        $baseUrl = $this->objectManager->get('Magento\Store\Model\StoreManagerInterface')
                        ->getStore()
                        ->getBaseUrl();

                        $productAlert = $baseUrl."productalert/add/stock/product_id/".$productId;
                            
                        $productAlertData = $this->postHelper->getPostData($productAlert);

                        $productAlertArray = $this->jsonHelper->jsonDecode($productAlertData);

                        $productAlertEncodeUrl = $productAlert."/uenc/".$productAlertArray['data']['uenc'];

                        $productAlertUrl = $this->postHelper->getPostData($productAlertEncodeUrl);

                        $stockAlertLabel = 'Sign up to get notified when this configuration is back in stock';

                        $productStockAlert = '<div class="product alert alert stock link-stock-alert">
                                    <a href="#" data-post='.$productAlertUrl.'
                                   title='.$stockAlertLabel.' class="action alert">'.$stockAlertLabel.'
                                </a>
                            </div>';
                    }
                }

                $customStatusText = '';
                if (isset($customLable['label']) && $customLable['label'] != ' ') {
                    $customStatusText = $customLable['label'];
                }

                $customStatus = '';
                $customStatusOptionClass= 'customstockstatus status_'.$optioId['value_index'];

                if ($customLable['label'] && $customLable['icon']) {
                    $customStatus = $productStockLb.'<img class="custom_stock_status_icon"
                    src='.$customLable['icon'].' alt="" title=""/>
                    <span class="'.$customStatusOptionClass.'">'.$customLable['label'].'</span>';
                } elseif ($customStatusText != null) {
                    $customStatus = $productStockLb.'<span
                    class="'.$customStatusOptionClass.'">'.$customLable['label'].'</span>';
                }
            
                $customStatusIcon= '';
                if ($customLable['icon']) {
                    $customStatusIcon ='<img src='.$customLable['icon'].
                    'class="custom_stock_status_icon" alt="" title="">';
                }
            
                $customStatusIconOnly = $this->getDisplayIcon();

                $changeConfigurableStatus = $this->getChangeConfigProduct();

                if ($productStockAlert != null) {
                    $configAttributeData[$optioId['value_index']] = [

                        'is_in_stock' => $productInStock,
                        'custom_stock_status_text' => $customStatusText,
                        'custom_stock_status' => $customStatus,
                        'custom_stock_status_icon' => $customStatusIcon,
                        'custom_stock_status_icon_only' => $customStatusIconOnly,
                        'product_id' => $productId,
                        'stockalertmessage' => $productStockAlert,

                      ];
                } else {
                    $configAttributeData[$optioId['value_index']] = [

                        'is_in_stock' => $productInStock,
                        'custom_stock_status_text' => $customStatusText,
                        'custom_stock_status' => $customStatus,
                        'custom_stock_status_icon' => $customStatusIcon,
                        'custom_stock_status_icon_only' => $customStatusIconOnly,
                        'product_id' => $productId,

                      ];
                }
            }

            $configAttributeData['changeConfigurableProductStatus'] = $changeConfigurableStatus;
            $configAttributeData['type'] = "product.info.options.swatches";
        }

        return $configAttributeData;
    }

    public function getMediaImagePath()
    {
        $mediapath = $this->objectManager->get('Magento\Store\Model\StoreManagerInterface')
                ->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        return $mediapath;
    }

    public function iconUpload($icon, $optionId)
    {
        /*  Save image upload */
        $optionIcon = '';
      
        try {
            $imageName = 'manage_icon['.$optionId.']';

            $mediaDirectory = $this->filesystem->getDirectoryRead(
                \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
            );

            if (file_exists($mediaDirectory->getAbsolutePath(self::ICON_PATH.$icon))) {
                unlink($mediaDirectory->getAbsolutePath(self::ICON_PATH.$icon));
            }

            $uploader = $this->uploader->create(['fileId' => $imageName]);
          
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);

            $imageAdapter = $this->adapterFactory->create();

            $uploader->addValidateCallback('image', $imageAdapter, 'validateUploadFile');

            $uploader->setAllowRenameFiles(true);

            $uploader->setFilesDispersion(false);

            $result = $uploader->save($mediaDirectory->getAbsolutePath(self::ICON_PATH));

            $optionIcon = self::ICON_PATH.$icon;
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
       
        return $optionIcon;
    }

    public function getStockItem($productId)
    {
        return $this->stockItemRepository->get($productId);
    }

    public function getProductBySku($sku)
    {   
        return $this->productRepository->get($sku);
    }
}
