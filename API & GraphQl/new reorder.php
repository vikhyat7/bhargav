<?php
namespace Mageants\Reorder\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\QuoteGraphQl\Model\Cart\AddProductsToCart;
use Magento\QuoteGraphQl\Model\Cart\GetCartForUser;
use Magento\Quote\Api\CartRepositoryInterface;


/**
 * Add simple products to cart GraphQl resolver
 * {@inheritdoc}
 */
class AddToCart implements ResolverInterface
{
    /**
     * @var GetCartForUser
     */
    private $getCartForUser;
    /**
     * Quote repository.
     *
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var AddProductsToCart
     */
    private $addProductsToCart;

    /**
     * @param GetCartForUser $getCartForUser
     * @param AddProductsToCart $addProductsToCart
     * @param CartRepositoryInterface $quoteRepository
     * 
     */
    public function __construct(
        GetCartForUser $getCartForUser,
        AddProductsToCart $addProductsToCart,
        CartRepositoryInterface $quoteRepository

    ) {
        $this->getCartForUser = $getCartForUser;
        $this->addProductsToCart = $addProductsToCart;
        $this->quoteRepository = $quoteRepository;

    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (empty($args['input']['customer_id'])) {
            throw new GraphQlInputException(__('Required parameter "customer_id" is missing'));
        }
        $customerId = $args['input']['customer_id'];

        if (empty($args['input']['cart_items'])
            || !is_array($args['input']['cart_items'])
        ) {
            throw new GraphQlInputException(__('Required parameter "cart_items" is missing'));
        }
        $cartItems = $args['input']['cart_items'];

        // $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();
        $cart = $this->quoteRepository->getActiveForCustomer($customerId);
        
        $this->addProductsToCart->execute($cart, $cartItems);

        return [
            'cart' => [
                'model' => $cart,
            ],
        ];
    }
}


********************************************************************************

<?php
namespace Mageants\Reorder\Model;

use Mageants\Reorder\Api\ReorderRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\Cart\CartInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Checkout\Model\Session;


/**
 * Class OrderService
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ReorderRepository extends DataObject implements ReorderRepositoryInterface
{
    protected $orderRepository;
    protected $cart;
    protected $_checkoutSession;
    protected $_productIds;
    protected $productRepository;
    
    protected $_resourceCart;
    protected $_customerSession;
    protected $messageManager;
    protected $stockRegistry;
    protected $stockState;
    protected $quoteRepository;
    private $requestInfoFilter;
    protected $orderFactory;
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

        /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    protected $customerId;
     /**
     * @var SearchCriteriaBuilder
     */
    protected $SearchCriteriaBuilder;
     /**
     * @var collectionFactory
     */
    private $collectionFactory;
    /**
     * @param SearchCriteriaBuilder $SearchCriteriaBuilder
    */


    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $SearchCriteriaBuilder,
        \Magento\Checkout\Model\Cart $cart,
        CollectionFactory $collectionFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\ResourceModel\Cart $resourceCart,
        Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockStateInterface $stockState,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        ProductRepositoryInterface $productRepository,
        \Magento\Sales\Api\Data\OrderInterfaceFactory $orderFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Quote\Model\QuoteFactory $quoteFactory
    ){
        $this->orderRepository = $orderRepository;
        $this->cart = $cart;
        $this->SearchCriteriaBuilder = $SearchCriteriaBuilder;
        $this->_eventManager = $eventManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_resourceCart = $resourceCart;
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        $this->messageManager = $messageManager;
        $this->stockRegistry = $stockRegistry;
        $this->stockState = $stockState;
        $this->quoteRepository = $quoteRepository;
        $this->productRepository = $productRepository;
        $this->orderFactory = $orderFactory;
        $this->customerRepository = $customerRepository;
        $this->quoteFactory = $quoteFactory;
        $this->collectionFactory = $collectionFactory;

     
    }

    /**
     * Add order item for the customer
     * @param int $customerId
     * @param string $orderId
     * @return array|bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function reorderItem($customerId, $orderId)
    {   


        // $customerOrder = $this->collectionFactory->create()
        //     ->addFieldToFilter('customer_id', $customerId);
        // return $customerOrder->getData();







        $items1 = array();
        $this->customerId = $customerId;
        
        $searchCriteria1 = $this->SearchCriteriaBuilder
                ->addFilter('increment_id', $orderId )->create();
    
    

     $Oid = $this->orderRepository->getList($searchCriteria1);
     

     


            foreach($Oid as $orderData)
                {

                $items1['entity_id'] =  $orderData->getId();
                
                }
            $order1 = $items1['entity_id'];

            $order = $this->orderRepository->get($order1);

            $cus = $order->getCustomerId();
            
            

     if($cus == $customerId){

        $cart = $this->cart;
        $items = $order->getItemsCollection();
            // return $items;

       foreach ($items as $item) {
        //echo '<pre>';print_r($item->getData());
            try {
                $cart = $this->addOrderItem($item);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                if ($cart->getUseNotice(true)) {
                    return $e->getMessage();
                } else {
                    return $e->getMessage();
                }
            } catch (\Exception $e) {
                echo $e->getMessage();
                return "We can't add this item to your shopping cart right now";
            }
        }

        
    

        $cart = $this->save();
        return "Product added to your Cart ";
    }
    else
    {
        
        return "Requested Order-Id doesn't match with your previous Orders!!!!!!";   
    }

    }

    /**
     * Convert order item to quote item
     *
     * @param \Magento\Sales\Model\Order\Item $orderItem
     * @param true|null $qtyFlag if is null set product qty like in order
     * @return $this
     */
    public function addOrderItem($orderItem, $qtyFlag = null)
    {
        
        /* @var $orderItem \Magento\Sales\Model\Order\Item */
        if ($orderItem->getParentItem() === null) {
            $storeId = $this->_storeManager->getStore()->getId();
            try {
                /**
                 * We need to reload product in this place, because products
                 * with the same id may have different sets of order attributes.
                 */
                $product = $this->productRepository->getById($orderItem->getProductId(), false, $storeId, true);
            } catch (NoSuchEntityException $e) {
                return $this;
            }
            $info = $orderItem->getProductOptionByCode('info_buyRequest');
            $info = new \Magento\Framework\DataObject($info);
            if ($qtyFlag === null) {
                $info->setQty(1);
                
                // $info->setQty($orderItem->getQtyOrdered());
            } 
            // else {
            //     $info->setQty(1);
            // }

            $this->addProduct($product, $info);
        }
        return $this;
    }

    /**
     * Add product to shopping cart (quote)
     *
     * @param int|Product $productInfo
     * @param \Magento\Framework\DataObject|int|array $requestInfo
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function addProduct($productInfo, $requestInfo = null)
    {
        $product = $this->_getProduct($productInfo);
        $request = $this->_getProductRequest($requestInfo);
        $productId = $product->getId();

        if ($productId) {
            $stockItem = $this->stockRegistry->getStockItem($productId, $product->getStore()->getWebsiteId());
            $minimumQty = $stockItem->getMinSaleQty();
            //If product quantity is not specified in request and there is set minimal qty for it
            if ($minimumQty
                && $minimumQty > 0
                && !$request->getQty()
            ) {
                $request->setQty($minimumQty);
            }

            try {
                $result = $this->getQuote()->addProduct($product, $request);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->_checkoutSession->setUseNotice(false);
                $result = $e->getMessage();
            }
            
        
        }

         else {
            throw new \Magento\Framework\Exception\LocalizedException(__('The product does not exist.'));
        }

        $this->_checkoutSession->setLastAddedProductId($productId);
        return $this;
    }

     /**
     * Get quote object associated with cart. By default it is current customer session quote
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        $storeId = $this->_storeManager->getStore()->getStoreId();
        $quote = $this->createCustomerCart($this->customerId, $storeId);

        if (!$this->hasData('quote')) {
            $this->setData('quote', $quote);
        }
        return $this->_getData('quote');
    }

     protected function createCustomerCart($customerId, $storeId)
    {
        try {
            $quote = $this->quoteRepository->getActiveForCustomer($customerId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $customer = $this->customerRepository->getById($customerId);
            /** @var \Magento\Quote\Model\Quote $quote */
            $quote = $this->quoteFactory->create();
            $quote->setStoreId($storeId);
            $quote->setCustomer($customer);
            // $quote->setCustomerIsGuest(0);
        }
        return $quote;
    }

    /**
     * Set quote object associated with the cart
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return $this
     * @codeCoverageIgnore
     */
    public function setQuote(\Magento\Quote\Model\Quote $quote)
    {
        $this->setData('quote', $quote);
        return $this;
    }

    /**
     * Get product object based on requested product information
     *
     * @param   Product|int|string $productInfo
     * @return  Product
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getProduct($productInfo)
    {
        $product = null;
        if ($productInfo instanceof Product) {
            $product = $productInfo;
            if (!$product->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __("The product wasn't found. Verify the product and try again.")
                );
            }
        } elseif (is_int($productInfo) || is_string($productInfo)) {
            $storeId = $this->_storeManager->getStore()->getId();
            try {
                $product = $this->productRepository->getById($productInfo, false, $storeId);
            } catch (NoSuchEntityException $e) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __("The product wasn't found. Verify the product and try again."),
                    $e
                );
            }
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(
                __("The product wasn't found. Verify the product and try again.")
            );
        }
        $currentWebsiteId = $this->_storeManager->getStore()->getWebsiteId();
        if (!is_array($product->getWebsiteIds()) || !in_array($currentWebsiteId, $product->getWebsiteIds())) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __("The product wasn't found. Verify the product and try again.")
            );
        }
        return $product;
    }

    /**
     * Get request for product add to cart procedure
     *
     * @param   \Magento\Framework\DataObject|int|array $requestInfo
     * @return  \Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getProductRequest($requestInfo)
    {
        if ($requestInfo instanceof \Magento\Framework\DataObject) {
            $request = $requestInfo;
        } elseif (is_numeric($requestInfo)) {
            $request = new \Magento\Framework\DataObject(['qty' => $requestInfo]);
        } elseif (is_array($requestInfo)) {
            $request = new \Magento\Framework\DataObject($requestInfo);
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('We found an invalid request for adding product to quote.')
            );
        }
        $this->getRequestInfoFilter()->filter($request);

        return $request;
    }

    /**
     * Getter for RequestInfoFilter
     *
     * @deprecated 100.1.2
     * @return \Magento\Checkout\Model\Cart\RequestInfoFilterInterface
     */
    private function getRequestInfoFilter()
    {
        if ($this->requestInfoFilter === null) {
            $this->requestInfoFilter = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Checkout\Model\Cart\RequestInfoFilterInterface::class);
        }
        return $this->requestInfoFilter;
    }

    /**
     * Save cart
     *
     * @return $this
     */
    public function save()
    {
        $this->_eventManager->dispatch('checkout_cart_save_before', ['cart' => $this]);

        $this->getQuote()->getBillingAddress();
        $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
        $this->getQuote()->collectTotals();
        $this->quoteRepository->save($this->getQuote());
        $this->_checkoutSession->setQuoteId($this->getQuote()->getId());
        /**
         * Cart save usually called after changes with cart items.
         */
        $this->_eventManager->dispatch('checkout_cart_save_after', ['cart' => $this]);
        
        return $this;
    }

    

    /**
     * Save cart (implement interface method)
     *
     * @return void
     * @codeCoverageIgnore
     */
    public function saveQuote()
    {
        $this->save();
    }


    




   
}

*************************************************************************************************


<?php
namespace Mageants\Reorder\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GraphQl\Model\Query\ContextInterface;
use Magento\Sales\Model\Reorder\Data\Error;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Sales\Model\OrderFactory;
use Magento\Quote\Api\Data\CartItemInterface; 
use Magento\Framework\GraphQl\Exception\GraphQlInputException;

/**
 * ReOrder customer order
 */
class SingleReorder implements ResolverInterface
{
    
    /**
     * Quote repository.
     *
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;
    /**
     * @var OrderFactory
     */
    private $orderFactory;
    /**
     * @var OrderItemInterface
     */
    private $OrderItemInterface;
    /**
     * @var OrderItem
     */
    private $OrderItem;
    /**
     * @var cart
     */
    private $cart;

    /**
     * @var \Magento\Sales\Model\Reorder\Reorder
     */
    private $reorder;
    /**
     *  Order repository.
     *
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @param \Magento\Sales\Model\Reorder\Reorder $reorder
     * @param CartRepositoryInterface $quoteRepository
     * @param CartItemInterface $OrderItem
     * @param OrderItemInterface $OrderItemInterface
     * @param OrderFactory $orderFactory
     */
    public function __construct(
        \Magento\Sales\Model\Reorder\Reorder $reorder,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        CartRepositoryInterface $quoteRepository,
        OrderFactory $orderFactory,
        CartItemInterface $OrderItem,
        Quote $cart,
        OrderItemInterface $OrderItemInterface
    ) {
        $this->orderFactory = $orderFactory;
        $this->orderRepository = $orderRepository;
        $this->reorder = $reorder;
        $this->cart = $cart;
        $this->OrderItemInterface = $OrderItemInterface;
        $this->OrderItem = $OrderItem;
        $this->quoteRepository = $quoteRepository;

    }

    /**
     * @inheritDoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $OrderItem=[];
        $orderNumber = $args['orderNumber'];
        $customerId =  $args['customerId'];
        $OrderItem =   $args['sku'];
        $OrderItem =   $args['qty'];
 
        $order = $this->orderFactory->create()->loadByIncrementId($orderNumber);
        $storeId = $order->getStoreId();
        // $Item = $order->getItemsCollection()->addFieldToFilter('sku',$args['sku'])->getData();
        
        if ($order->getCustomerId() == $customerId) 
        {   
           // $product = $this->OrderItemInterface->getProductId($orderItem);
           $items = $order->getItemsCollection()->addFieldToFilter("sku",$args['sku'])->getData();
           // print_r($items);
           if($items)
           {
                 
                    $quote = $this->quoteRepository->getActiveForCustomer($customerId);
                    $quoteItems = $quote->getItems();

                    $quoteItems[]=$OrderItem;
                    $quote->setItems($quoteItems);
                    $this->quoteRepository->save($quote);
                
            }
            else
            {
                 throw new GraphQlInputException(
                __('Product Sku "%1" doesn\'t belong to the current Order Id', $args['sku'])
                 );
            }

        }
        else
        {
            throw new GraphQlInputException(
                __('Order number "%1" doesn\'t belong to the current customer', $orderNumber)
            );
        }

    }
}


 
 \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\




 <?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mageants\Reorder\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GraphQl\Model\Query\ContextInterface;
use Magento\Quote\Model\Cart\AddProductsToCart as AddProductsToCartService;
use Magento\Quote\Model\Cart\Data\AddProductsToCartOutput;
use Magento\Quote\Model\Cart\Data\CartItemFactory;
use Magento\QuoteGraphQl\Model\Cart\GetCartForUser;
use Magento\Quote\Model\Cart\Data\Error;
use Magento\QuoteGraphQl\Model\CartItem\DataProvider\Processor\ItemDataProcessorInterface;

/**
 * Resolver for addProductsToCart mutation
 *
 * @inheritdoc
 */
class AddProductsToCart implements ResolverInterface
{
    /**
     * @var GetCartForUser
     */
    private $getCartForUser;

    /**
     * @var AddProductsToCartService
     */
    private $addProductsToCartService;

    /**
     * @var ItemDataProcessorInterface
     */
    private $itemDataProcessor;

    /**
     * @param GetCartForUser $getCartForUser
     * @param AddProductsToCartService $addProductsToCart
     * @param  ItemDataProcessorInterface $itemDataProcessor
     */
    public function __construct(
        GetCartForUser $getCartForUser,
        AddProductsToCartService $addProductsToCart,
        ItemDataProcessorInterface $itemDataProcessor
    ) {
        $this->getCartForUser = $getCartForUser;
        $this->addProductsToCartService = $addProductsToCart;
        $this->itemDataProcessor = $itemDataProcessor;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (empty($args['cartId'])) {
            throw new GraphQlInputException(__('Required parameter "cartId" is missing'));
        }
        if (empty($args['cartItems']) || !is_array($args['cartItems'])
        ) {
            throw new GraphQlInputException(__('Required parameter "cartItems" is '));
        }

        $maskedCartId = $args['cartId'];
        $cartItemsData = $args['cartItems'];
        $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();

        // Shopping Cart validation
        $this->getCartForUser->execute($maskedCartId, $context->getUserId(), $storeId);

        $cartItems = [];
        foreach ($cartItemsData as $cartItemData) {
            if (!$this->itemIsAllowedToCart($cartItemData, $context)) {
                continue;
            }
            $cartItems[] = (new CartItemFactory())->create($cartItemData);
        }

        /** @var AddProductsToCartOutput $addProductsToCartOutput */
        $addProductsToCartOutput = $this->addProductsToCartService->execute($maskedCartId, $cartItems);

        return [
            'cart' => [
                'model' => $addProductsToCartOutput->getCart(),
            ],
            'user_errors' => array_map(
                function (Error $error) {
                    return [
                        'code' => $error->getCode(),
                        'message' => $error->getMessage(),
                        'path' => [$error->getCartItemPosition()]
                    ];
                },
                $addProductsToCartOutput->getErrors()
            )
        ];
    }

    /**
     * Check if the item can be added to cart
     *
     * @param array $cartItemData
     * @param ContextInterface $context
     * @return bool
     */
    private function itemIsAllowedToCart(array $cartItemData, ContextInterface $context): bool
    {
        $cartItemData = $this->itemDataProcessor->process($cartItemData, $context);
        if (isset($cartItemData['grant_checkout']) && $cartItemData['grant_checkout'] === false) {
            return false;
        }

        return true;
    }
}
