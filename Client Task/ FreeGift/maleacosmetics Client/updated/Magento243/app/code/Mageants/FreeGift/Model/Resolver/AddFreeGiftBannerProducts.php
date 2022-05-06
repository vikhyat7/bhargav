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

class AddFreeGiftBannerProducts implements ResolverInterface
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

        if (!isset($args['productId']) || $args['productId'] == 0) {
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
        if (!isset($args['cartId'])) {
            throw new GraphQlAuthorizationException(
                __(
                    'Quote ID should be specified',
                    '[\Magento\Store\Model\Store::ENTITY]'
                )
            );
        }
        if (!isset($args['freeGiftSkus'])) {
            throw new GraphQlAuthorizationException(
                __(
                    'SKU should be specified',
                    [\Magento\Catalog\Model\Product::ENTITY]
                )
            );
        }
        try {
            $data = $this->addFreeGiftBannerProducts($args['cartId'],$args['freeGiftSkus'],$args['freeGiftSuperAttributes'],$args['storeId'],$args['productId']);            
            
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
    * Set Free Products in cart
    *
    * @api
    * @param string cartId
    * @param string freeGiftSkus
    * @param string freeGiftSuperAttributes
    * @param int storeId
    * @param int productId
    */
    public function addFreeGiftBannerProducts($cartId,$freeGiftSkus,$freeGiftSuperAttributes,$storeId,$productId)
    {        
        $response = ['success' => true];
        $isActive = $this->_freeGiftHelper->getFreeGiftConfig('mageants_freegift/general/active');
        if($isActive){
            $validation = $this->_freeGiftHelper->getCartBasedValidRuleOnAddtoCart();            
            $selectedFreeGiftSkus = $freeGiftSkus;
            $freeGiftSuperAttrs = '';
            $selectedFreeGiftQty = 0;  
            $selectedFreeGiftSkusArray = '';                    
            
            if($freeGiftSuperAttributes && $freeGiftSuperAttributes!=''){  
                $freeGiftSuperAttributes = str_replace("'", '"', $freeGiftSuperAttributes);              
                $freeGiftSuperAttrs = json_decode($freeGiftSuperAttributes,true);    
            }            
            
            $quoteId = $this->maskedQuoteIdToQuoteId->execute($cartId);
            $cart = $this->quoteRepository->get($quoteId);            
            $allItems = $cart->getItemsCollection();
            $selectedGifts = explode(',', $selectedFreeGiftSkus);
            $addedSkus=array();
            foreach ($allItems as $addedItems) {                
                if($addedItems->getIsFreeItem() && in_array($addedItems->getSku(), $selectedGifts)){
                    if($addedItems->getIsFreeItem() && in_array($addedItems->getSku(), $selectedGifts)){
                        $addedSkus[$addedItems->getSku()] = true;
                    }
                }
            }

            $getLastItem = $allItems->addFieldToFilter('product_id',$productId)->setOrder('item_id','DESC')->getLastItem();
            
            $parentItemId = $getLastItem->getParentItemId();
            if($parentItemId)
            {
                $lastItemId = $parentItemId;
            } else {
                $lastItemId = $getLastItem->getItemId();
            }

            if(array_key_exists('free_gift_sku', $validation)){

                foreach ($validation['free_gift_sku'] as $skus) {
                    if($skus != '')
                    {                        
                        foreach(explode(',',$skus) as $sku)
                        {
                            /* check whether free gift already added or not (start)*/
                            if(array_key_exists($sku, $addedSkus)){
                                continue;
                            }
                            /* check whether free gift already added or not (end)*/
                        
                            if(strpos($selectedFreeGiftSkus, $sku) !== false){ 
                                $freeGiftProduct = $this->_productRepository->get($sku);

                                if ($freeGiftProduct->isSalable()) { 
                                    $loadProduct = $this->_productRepository->getById($freeGiftProduct->getId(), false, $storeId, true);
                                    
                                    $additionalOptions = [];
                                    $additionalOptions[] = array(
                                        'label' => "Free! ",
                                        'value' => "Product",
                                    );
                                    
                                    $loadProduct->addCustomOption('additional_options', $this->serializer->serialize($additionalOptions));
                                    
                                    $freeGiftParams = array(
                                        'product' => $freeGiftProduct->getId(),
                                        'qty' => $selectedFreeGiftQty
                                    );

                                    if(isset($freeGiftSuperAttrs[$sku]))
                                    {
                                        $freeGiftParams = array(
                                            'product' => $loadProduct->getId(),
                                            'qty' => $selectedFreeGiftQty,
                                            'super_attribute' => $freeGiftSuperAttrs[$sku]
                                        );
                                    }
                                    
                                    $request = $this->_objectManager->create('Magento\Framework\DataObject');
                                    $request->setData($freeGiftParams);
                                    $cart->addProduct($loadProduct,$request);

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
                                }else{                                    
                                    $response['error_message'] = _('freegift product is out of stock');
                                }
                            }                           
                        }
                        $cart->save();
                        $this->_freeGiftHelper->updateConfigFreeGiftItem();
                    }   
                }
            }       
        }
        return $response;        
    }
}