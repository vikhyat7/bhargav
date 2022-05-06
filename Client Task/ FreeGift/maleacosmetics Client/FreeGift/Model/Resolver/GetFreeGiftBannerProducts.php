<?php
namespace Mageants\FreeGift\Model\Resolver;

use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\ValueFactory;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\Webapi\ServiceOutputProcessor;
use Magento\Framework\Api\ExtensibleDataObjectConverter;

/**
 * Customers field resolver, used for GraphQL request processing.
 */

class GetFreeGiftBannerProducts implements ResolverInterface
{
    /**
     * @var ValueFactory
     */
    private $valueFactory;    

    /**
     * @var ServiceOutputProcessor
     */
    private $serviceOutputProcessor;

    /**
     * @var ExtensibleDataObjectConverter
     */
    private $dataObjectConverter;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Mageants\FreeGift\Block\Freegift
     */
    protected $_freeGiftBlock;       

    /**
    * @var \Magento\Framework\View\Asset\
    */
    protected $_assetRepo; 

    /**
    * @var \Magento\Catalog\Model\ProductRepository 
    */
    protected $_productRepository;    

    /**
    * @var \Magento\Checkout\Model\Session 
    */
    protected $checkoutSession;

    /**
     * @var \Magento\Quote\Model\Quote\Item\Factory
     */
    protected $quoteItemFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;

    /**
     * @var \Magento\SalesRule\Model\Rule
     */
    protected $_rule;

    /**
    * @var \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory
    */
    protected $ruleCollectionFactory;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     *
     * @param ValueFactory $valueFactory     
     * @param ServiceOutputProcessor $serviceOutputProcessor
     * @param ExtensibleDataObjectConverter $dataObjectConverter
     */
    public function __construct(
        ValueFactory $valueFactory,        
        ServiceOutputProcessor $serviceOutputProcessor,
        ExtensibleDataObjectConverter $dataObjectConverter,        
        \Psr\Log\LoggerInterface $logger,
        \Mageants\FreeGift\Block\Freegift $freeGiftBlock,   
        \Magento\Framework\View\Asset\Repository $assetRepo,  
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null,
        \Magento\SalesRule\Model\Rule $rule,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory   
    ) {
        $this->valueFactory = $valueFactory;        
        $this->serviceOutputProcessor = $serviceOutputProcessor;
        $this->dataObjectConverter = $dataObjectConverter;        
        $this->logger = $logger;
        $this->_freeGiftBlock = $freeGiftBlock;    
        $this->_assetRepo = $assetRepo;    
        $this->_productRepository = $productRepository;
        $this->checkoutSession = $checkoutSession;
        $this->quoteItemFactory = $quoteItemFactory;  
        $this->productFactory = $productFactory;    
        $this->quoteFactory = $quoteFactory;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
        $this->_rule = $rule;            
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->_objectManager = $objectmanager;    
        $this->ruleCollection = null;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)  {

        if (!isset($args['productId'])) {
            throw new GraphQlAuthorizationException(
                __(
                    'Product ID should be specified',
                    [\Magento\Catalog\Model\Product::ENTITY]
                )
            );
        }
        if (!isset($args['storeId'])) {
            throw new GraphQlAuthorizationException(
                __(
                    'Store ID should be specified',
                    [\Magento\Store\Model\Store::ENTITY]
                )
            );
        }
        try {
            $data = $this->getFreeGiftBannerProducts($args['productId'],$args['storeId']);            
            
            $result = function () use ($data) {
                return !empty($data) ? $data : [];
            };            
            return $this->valueFactory->create($result);
        } catch (NoSuchEntityException $exception) {
            throw new GraphQlNoSuchEntityException(__($exception->getMessage()));
        } catch (LocalizedException $exception) {
            throw new GraphQlNoSuchEntityException(__($exception->getMessage()));
        }
    }

    /**
    * Returns product details
    *
    * @api
    * @param int productId
    * @param int storeId
    * @return array
    */
    public function getFreeGiftBannerProducts($productId,$storeId){                
        $products = [];
        if($productId){
            foreach($this->getValidRules($productId,$storeId) as $_rule){
                if($_rule->getSimpleAction() == 'add_free_item' &&  (int)$_rule->getCouponType() !== 2){
                    foreach($this->_freeGiftBlock->getProducts($_rule) as $product){
                        $product->setStoreId($storeId);
                        $products['product'][$product->getId()]['id'] = $product->getId();
                        $products['product'][$product->getId()]['title'] = $product->getName();
                        $products['product'][$product->getId()]['url'] = $product->getProductUrl();
                        $productImageUrl = $this->_freeGiftBlock->getImageHelper()
                                        ->init($product, 'product_thumbnail_image')
                                        ->keepFrame(false)
                                        ->constrainOnly(true)
                                        //->resize($block->getWidth())
                                        ->getUrl();
                        $products['product'][$product->getId()]['imageurl'] = $productImageUrl;
                        $products['product'][$product->getId()]['price'] = $this->_freeGiftBlock->getFormatCurrency($product->getPrice());
                        if($product->getTypeId() == 'configurable'){                 
                        $productTypeInstance = $this->_objectManager->get('Magento\ConfigurableProduct\Model\Product\Type\Configurable');
                        $productAttributeOptions = $productTypeInstance->getConfigurableAttributesAsArray($product);
                            if(count($productAttributeOptions)){
                                foreach($productAttributeOptions as $key=>$value){
                                    $products['product'][$product->getId()]['attribute'][$key]['product_super_attribute_id'] = $key;
                                    $products['product'][$product->getId()]['attribute'][$key]['id'] = $value['id'];
                                    $products['product'][$product->getId()]['attribute'][$key]['label'] = $value['label'];
                                    $products['product'][$product->getId()]['attribute'][$key]['use_default'] = $value['use_default'];
                                    $products['product'][$product->getId()]['attribute'][$key]['position'] = $value['position'];
                                    $products['product'][$product->getId()]['attribute'][$key]['values'] = $value['values'];
                                    $products['product'][$product->getId()]['attribute'][$key]['attribute_id'] = $value['attribute_id'];
                                    $products['product'][$product->getId()]['attribute'][$key]['attribute_code'] = $value['attribute_code'];
                                    $products['product'][$product->getId()]['attribute'][$key]['frontend_label'] = $value['frontend_label'];
                                    $products['product'][$product->getId()]['attribute'][$key]['store_label'] = $value['store_label'];
                                }
                            }                    
                        }
                    }
                }
            }
            if(count($products)){
                $products['view_imageurl'] = $this->_assetRepo->getUrl('Mageants_FreeGift::images/icon-login.png');
            }        
        }
        return $products;        
    }
    /**
     * @return array|\Magento\SalesRule\Model\ResourceModel\Rule\Collection[]
     */
    public function getValidRules($productId,$storeId){ 
        $productBasedValidRuleIds = $this->getProductBasedValidRuleIds($productId,$storeId);
        $cartBasedValidRuleIds = $this->getCartBasedValidRuleIds($productId);
        $validRulesIds = array_unique(array_merge($productBasedValidRuleIds, $cartBasedValidRuleIds));

        $validRules = $this->ruleCollectionFactory->create()
                        ->addFieldToFilter('rule_id', ['in' => $validRulesIds]);

        return $validRules;
    }

    /**
     * @return array
     */
    public function getProductBasedValidRuleIds($productId,$storeId){        
        $validRules = [];
        $currentQuote = $this->checkoutSession->getQuote();
        if($storeId){
            $currentQuote->setStoreId($storeId);    
        }        
        $quoteItem = $this->quoteItemFactory->create();
        $quoteItem->setProduct($this->getProductById($productId));        
        $quoteItem->setStoreId($currentQuote->getStoreId());
        $currentQuote->getStoreId();
        $quoteItem->setIsVirtual(false);
        $quoteItem->setQuote($currentQuote);
        $quoteItem->setAllItems([$quoteItem]);
        
        /** @var \Magento\SalesRule\Model\Rule $rule */
        foreach($this->getRulesCollection() as $rule){

            if ($rule->getActions()->validate($quoteItem)){
                $validRules[] = $rule->getId();
            }
        }        
        return $validRules;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCartBasedValidRuleIds($productId)
    {
        $validRules = [];
        $product = $this->getProductById($productId);
        
        $product = clone $product;
        if ($product->isSalable()){
                if ($product->getTypeId() == 'configurable'){
                    $configProduct = $this->productFactory->create()->load($product->getId());
              
                    $childrenProducts = $configProduct->getTypeInstance()->getUsedProductIds($configProduct);
                    if (count($childrenProducts) > 0){
                        $productId = end($childrenProducts);
                        $product = $this->productFactory->create()->load($productId);
                    }
                }
                
                $currentQuote = $this->checkoutSession->getQuote();

                $afterQuote = $this->quoteFactory->create()
                    ->merge($currentQuote);

                $afterQuote->addProduct($product);
                $afterQuote->collectTotals();
          
                $currentRules = array();

                /**
                 * validate rules according to current quote
                 */
                foreach ($this->getRulesCollection() as $rule){
                    foreach ($currentQuote->getItemsCollection() as $item){
                        if ($item->getProduct()->getId() == $product->getId()){
                            if ($rule->getActions()->validate($item)){
                                $currentRules[] = $rule->getId();
                            }
                        }
                    }
                }

                /**
                 * match with quote after add current product
                 */
                foreach ($this->getRulesCollection() as $rule){
                    if (!in_array($rule->getId(), $currentRules)){
                        foreach ($afterQuote->getItemsCollection() as $item){
                            if ($item->getProduct()->getId() == $product->getId()){
                                if ($rule->getActions()->validate($item)){
                                    $validRules[] = $rule->getId();
                                }
                            }
                        }
                    }
                }
                
                /**
                 * match qty condition
                 */
                $quoteSummeryQty = $currentQuote->getItemsSummaryQty();
                
                $ruleCollections = $this->_rule->getCollection()->addFilter('is_active',1);

            foreach($ruleCollections as $ruleCollection)
            {
                $getActionsSerialize = $this->serializer->unserialize($ruleCollection->getActionsSerialized());
                if(isset($getActionsSerialize['conditions']))
                {
                    foreach($getActionsSerialize['conditions'] as $conditions)
                    {
                        if($conditions['attribute'] == 'quote_item_qty')
                        {
                            $opt = $conditions['operator'];
                            if($opt == '==')
                            { 
                                if($conditions['value'] == $quoteSummeryQty)
                                {
                                    array_push($validRules,$ruleCollection->getRuleId());
                                }
                            } elseif($opt == '!=')
                            {
                                if($conditions['value'] != $quoteSummeryQty)
                                {
                                    array_push($validRules,$ruleCollection->getRuleId());
                                }
                            } elseif($opt == '>=')
                            {
                                if($quoteSummeryQty >= $conditions['value'])
                                {
                                    array_push($validRules,$ruleCollection->getRuleId());
                                }
                            } elseif($opt == '<=')
                            {
                              if($quoteSummeryQty <= $conditions['value'])
                              {
                                array_push($validRules,$ruleCollection->getRuleId());
                              }
                            } elseif($opt == '>')
                            {
                                if($quoteSummeryQty > $conditions['value'])
                                {
                                    array_push($validRules,$ruleCollection->getRuleId());
                                }
                            } elseif($opt == '<')
                            {
                                if($quoteSummeryQty < $conditions['value'])
                                {
                                    array_push($validRules,$ruleCollection->getRuleId());
                                }
                            } elseif($opt == '{}')
                            {
                                if(strpos($quoteSummeryQty, $conditions['value']) !== false)
                                {
                                    array_push($validRules,$ruleCollection->getRuleId());
                                }
                            } elseif($opt == '!{}')
                            {
                                if(strpos($quoteSummeryQty, $conditions['value']) == false)
                                {
                                    array_push($validRules,$ruleCollection->getRuleId());
                                }
                            } elseif($opt == '()')
                            {
                                $actionConditionValues = explode(',',$conditions['value']);
                                if(in_array($quoteSummeryQty, $actionConditionValues))
                                {
                                    array_push($validRules,$ruleCollection->getRuleId());
                                }
                            } elseif($opt == '!()')
                            {
                                $actionConditionValues = explode(',',$conditions['value']);
                                if(!in_array($quoteSummeryQty, $actionConditionValues))
                                {
                                    array_push($validRules,$ruleCollection->getRuleId());
                                }
                            } else
                            {
                            }
                        }
                    }
                }
            }
        }
        return array_unique($validRules);
    } 
    
    /**
     * @return $this|\Magento\SalesRule\Model\ResourceModel\Rule\Collection
     */
    public function getRulesCollection()
    {
        if ($this->ruleCollection === null) {
            $currentQuote = $this->checkoutSession->getQuote();

            $this->ruleCollection = $this->ruleCollectionFactory->create()
                ->setValidationFilter(
                    $currentQuote->getStore()->getWebsiteId(),
                    $currentQuote->getCustomerGroupId(),
                    $currentQuote->getCouponCode()
                )
                ->addFilter('is_active',1);
        }
        return $this->ruleCollection;
    } 

    /**
     * @return Product
     */ 
    public function getProductById($id)
    {        
        return $this->_productRepository->getById($id);
    }
}