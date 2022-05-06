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

class AddCouponCode implements ResolverInterface
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
    * @var \Magento\Catalog\Model\ProductRepository 
    */
    protected $_productRepository;                
    
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;    
    
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Mageants\FreeGift\Helper\Data
     */
    protected $_freeGiftHelper;

     /**
     * @var MaskedQuoteIdToQuoteIdInterface
     */
    private $maskedQuoteIdToQuoteId;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

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
        \Magento\Catalog\Model\ProductRepository $productRepository,                                
        \Magento\Framework\Serialize\Serializer\Json $serializer = null,  
        \Mageants\FreeGift\Helper\Data $freeGiftHelper,      
        \Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,   
        \Magento\Framework\ObjectManagerInterface $objectmanager
    ) {
        $this->valueFactory = $valueFactory;        
        $this->serviceOutputProcessor = $serviceOutputProcessor;
        $this->dataObjectConverter = $dataObjectConverter;        
        $this->logger = $logger;                  
        $this->_productRepository = $productRepository;                                  
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);    
        $this->_freeGiftHelper = $freeGiftHelper;    
        $this->maskedQuoteIdToQuoteId = $maskedQuoteIdToQuoteId;
        $this->quoteRepository = $quoteRepository;       
        $this->_objectManager = $objectmanager;            
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)  {        
        
        if (!isset($args['storeId'])) {
            throw new GraphQlAuthorizationException(
                __(
                    'Store ID should be specified',
                    [\Magento\Store\Model\Store::ENTITY]
                )
            );
        }
        if (!isset($args['cartId'])) {
            throw new GraphQlAuthorizationException(
                __(
                    'Quote ID should be specified',
                    '[\Magento\Store\Model\Store::ENTITY]'
                )
            );
        }
        if (!isset($args['couponCode'])) {
            throw new GraphQlAuthorizationException(
                __(
                    'Coupon Code should be specified',
                    [\Magento\Catalog\Model\Product::ENTITY]
                )
            );
        }
        try {
            $data = $this->addCouponCode($args['cartId'],$args['couponCode'],$args['storeId']);            
                   
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
    * Add free gift product when Coupon Code in cart
    *
    * @api
    * @param string cartId
    * @param string couponCode    
    * @param int storeId    
    */
    public function addCouponCode($cartId,$couponCode,$storeId){        
        $isActive = $this->_freeGiftHelper->getFreeGiftConfig('mageants_freegift/general/active');
        $quoteId = $this->maskedQuoteIdToQuoteId->execute($cartId);
        $cart = $this->quoteRepository->get($quoteId);  
        $response = ['success' => false];          
        if($isActive){ 
            $helper = $this->_objectManager->get('\Magento\Catalog\Helper\Product\Configuration');        
            $coupon = $this->_objectManager->create('\Magento\SalesRule\Model\CouponFactory')->create();            
            
            $couponcodes = $coupon->load($couponCode, 'code');
            $rule = $this->_objectManager->create('\Magento\SalesRule\Model\Rule');
            if($couponcodes->getRuleId()!=null){
                if(strpos($cart->getAppliedRuleIds(), $couponcodes->getRuleId())!==false)
                {                    
                    $rules = $rule->load($couponcodes->getRuleId());
                    if($rules->getSimpleAction()=='add_free_item' && (int)$rules->getCouponType()== 2)
                    {
                        $freeGiftSkus = explode(',', $rules->getFreeGiftSku());
                        $qty = $rules->getDiscountAmount();
                    }
                    if (isset($freeGiftSkus)) {
                        if(is_array($freeGiftSkus)){                          
                            foreach($freeGiftSkus as $sku)
                            {
                                $freeGiftItem = $cart->getAllItems();                                
                                foreach($freeGiftItem as $freeItem)
                                {                                                              
                                    $options=$helper->getCustomOptions($freeItem);                                    
                                    if ($options) 
                                    {
                                        foreach ($options as $option) {
                                            if ($option['label'] == "Free! " && $option['value'] == "Product") {
                                                $response = ['success' => true];                                                
                                                return $response;
                                            }
                                        }
                                    }                                    
                                }

                                $freeGiftProduct = $this->_productRepository->get($sku);
                                $loadProduct = $this->_productRepository->getById($freeGiftProduct->getId(), false, $storeId, true);
                                
                                $additionalOptions = [];
                                $additionalOptions[] = array(
                                    'label' => "Free! ",
                                    'value' => "Product",
                                );
                                
                                $loadProduct->addCustomOption('additional_options', $this->serializer->serialize($additionalOptions));
                                
                                $freeGiftParams = array(
                                    'product' => $freeGiftProduct->getId(),
                                    'qty' => $qty
                                );
                                $getLastItem = $cart->getItemsCollection()->getLastItem();
                                
                                $request = $this->_objectManager->create('Magento\Framework\DataObject');
                                $request->setData($freeGiftParams);
                                $cart->addProduct($loadProduct,$request); 
                                $parentItemId = $getLastItem->getParentItemId();
                                if($parentItemId)
                                {
                                    $lastItemId = $parentItemId;
                                } else {
                                    $lastItemId = $getLastItem->getItemId();
                                }
                                $lastFreeItem = $cart->getItemsCollection()->getLastItem();
                                $lastFreeItem->setParentProductId($lastItemId);
                                $lastFreeItem->setIsFreeItem(1);
                                $lastFreeItem->setPrice(0);
                                $lastFreeItem->setBasePrice(0);
                                $lastFreeItem->setCustomPrice(0);
                                $lastFreeItem->setOriginalCustomPrice(0);
                                $lastFreeItem->setPriceInclTax(0);
                                $lastFreeItem->setBasePriceInclTax(0);
                                $lastFreeItem->getProduct()->setIsSuperMode(true);
                                $lastFreeItem->save();
                                $response = ['success' => true];
                            }       
                            $cart->save();
                            $this->updateFreeGifts($couponcodes->getRuleId(),$cart);
                        }                       
                    }
                }
            }
        }
        return $response;        
    }
    
    public function updateFreeGifts($ruleid,$cart)
    {
        $allitems = $cart->getItemsCollection();
        
        $rule = $this->_objectManager->create('\Magento\SalesRule\Model\Rule');
        $rules = $rule->load($ruleid);
        foreach ($allitems as $cartitems) 
        {
            if(strpos($rules->getFreeGiftSku(), $cartitems->getSku())!==false)
            {
                $cartitems->setPrice(0);
                $cartitems->setIsFreeItem(1);
                $cartitems->setPrice(0);
                $cartitems->setBasePrice(0);
                $cartitems->setCustomPrice(0);
                $cartitems->setOriginalCustomPrice(0);
                $cartitems->setPriceInclTax(0);
                $cartitems->setBasePriceInclTax(0);
                $cartitems->getProduct()->setIsSuperMode(true);
                $cartitems->save();
                $cart->save();
           }
        }            
        return;       
    }   

}