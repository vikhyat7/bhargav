<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCertificate\Cron;

/**
 * Notification class to send notification of expiration
 */ 
class Sendgiftcard
{   
    protected $_logger;

    /**
     * customer
     *
     * @var \Mageants\GiftCertificate\Model\Customer
     */
    protected $_customer;
    
    /**
     * account
     *
     * @var \Mageants\GiftCertificate\Model\Account
     */
    protected $_account;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Mageants\GiftCertificate\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * timezone
     *
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface 
     */
    protected $timezone; 

    protected $date;

    /**
     * @param \Mageants\GiftCertificate\Helper\Data $helper
     * @param \Mageants\GiftCertificate\Model\Customer $customer
     * @param \Mageants\GiftCertificate\Model\Account $account
     */
    public function __construct(
        \Mageants\GiftCertificate\Helper\Data $helper,
        \Mageants\GiftCertificate\Model\Customer $customer,
        \Mageants\GiftCertificate\Model\Account $account,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Escaper $escaper,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Catalog\Api\ProductRepositoryInterface $productrepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_logger = $logger;
        $this->_helper =$helper;
        $this->_customer = $customer;
        $this->_account = $account;
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->_escaper = $escaper;
        $this->date = $date;
        $this->timezone = $timezone;
        $this->orderFactory = $orderFactory;
        $this->_categoryFactory=$categoryFactory;
        $this->_assetRepo = $assetRepo;
        $this->productrepository = $productrepository;
        $this->_storeManager = $storeManager;
    }
    
    /**
     * Method executed when cron runs in server
     */
    public function execute() {
         $customerData = $this->_customer->getCollection()->addFieldToFilter('timezone',['neq' => '']);
         $customerData->getSelect()->join(
                'gift_code_account',
                'main_table.customer_id = gift_code_account.order_id',
                array('*')
            );
            $this->_logger->debug('Running Cron from send class');
            $sendtime = str_replace(",",":",$this->_helper->getSendTime());
            
            $time = date("H:i", strtotime($sendtime));
            
         foreach ($customerData->getData() as $customers) {
             
            $orderIncrementId = $customers['order_increment_id'];
            $order = $this->orderFactory->create()->loadByIncrementId($orderIncrementId);                
            $time_in_24_hour_format  = date("H:i", strtotime($this->timezone->formatDateTime($customers['timezone'])));
            if (!$customers['sentgiftcard']) {

                if ($customers['emailtime']) {

                    /*$this->_logger->debug('condition start');
                    $this->_logger->debug($orderIncrementId);
                    $this->_logger->debug($order->getStatus());
                    $this->_logger->log('600', print_r($customers, true));
                    $this->_logger->debug('end');*/

                    if ($time_in_24_hour_format >= $time && ($order->getStatus() != "canceled" && $order->getStatus() != "closed" && $order->getStatus() != "pending") ) {
                        if ($this->_helper->isallowtimezone() == 1 && $this->_helper->isAllowDeliveryDate() == 1) {
                            $this->_logger->debug('templateData_if_condition');
                            if ($customers['date_of_delivery'] == $this->timezone->date()->setTimezone(new \DateTimeZone($customers['timezone']))->format('Y-m-d')) {
                                $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
                                $priceHelper = $objectManager->create('Magento\Framework\Pricing\Helper\Data'); 
                            
                                $formattedPrice = $priceHelper->currency($customers['current_balance'], true, false);
                                $cats = explode(',', $customers['categories']);
                                $categoryName='';
                                foreach ($cats as $cat) {
                                    $_category = $this->_categoryFactory->create()->load($cat);    
                                    $categoryName.=$_category->getName().",";
                                }
                                $templateVariable['message'] = "";
                                
                                $templateVariable['left'] = '0px';
                                $templateVariable['top'] = '96px';
                                $templateVariable['bgcolor'] = '#f00';
                                $templateVariable['color'] = '#fff';

                                if ($customers['sendtemplate_id']) {
                                    $templateData = $objectManager->get('Mageants\GiftCertificate\Model\Templates')->load($customers['sendtemplate_id']);
                                    if ($templateData->getPositionleft()) {
                                        $templateVariable['left'] = $templateData->getPositionleft().'px';
                                    }
                                    if ($templateData->getPositiontop()) {
                                        $templateVariable['top'] = $templateData->getPositiontop().'px';
                                    }
                                    if ($templateData->getColor()) {
                                        $templateVariable['bgcolor'] = $templateData->getColor() ;
                                    }
                                    if ($templateData->getForecolor()) {
                                        $templateVariable['color'] = $templateData->getForecolor() ;
                                    }
                                    if ($templateData->getMessage()) { 
                                        $templateVariable['message'] = $templateData->getMessage();
                                    }
                                }

                                if ($customers['message'] != "") {
                                    $templateVariable['message'] = $customers['message'];    
                                }

                                $items = $order->getAllVisibleItems();
                                foreach ($items as $item) {
                                    $productid = $item->getProductId();
                                }

                                if ($customers['custom_upload']) {
                                    $store = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
                                    $mediapath = $store->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                                    $template_image = $mediapath."giftcertificate/".$customers['template'];
                                } else {
                                    $template_image = $customers['template'];       
                                }

                                $items = $order->getAllVisibleItems();
                                foreach ($items as $item) {
                                    $productid = $item->getProductId();
                                }

                                if ($template_image=="") {
                                    $store = $this->_storeManager->getStore();
                                    $product = $this->productrepository->getById($productid);
                                    $mediaUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                                    if($product->getImage()) {
                                        $template_image = $mediaUrl . 'catalog/product' .$product->getImage();
                                    } else {
                                        $template_image =  $this->_assetRepo->getUrl('Magento_Catalog::images/product/placeholder/image.jpg');
                                    }
                                }

                                $validity = $customers['expire_at'];
                                if ($validity == '0000-00-00') {
                                    $validity = 'Unlimited';
                                }
                                
                                $templateVariable['recipient_name'] = $customers['recipient_name'];
                                $templateVariable['template'] = $template_image;
                                $templateVariable['code'] = $customers['gift_code'];
                                $templateVariable['sender_email'] = $customers['sender_email'];
                                $templateVariable['sender_name'] = $customers['sender_name'];
                                $templateVariable['current_balance'] = $formattedPrice;
                                $templateVariable['category_name'] = $categoryName;
                                $templateVariable['validity'] = $validity;
                                $templateVariable['recipient_email'] = $customers['recipient_email'];
                                
                                $this->_helper->sendTemplate($templateVariable);
                                
                                $cust_collection = $this->_customer->load($customers['order_id']);
                                $this->_customer->load($customers['order_id'])->setSentgiftcard(1)->save();
                            }
                        } else {
                            $this->_logger->debug('templateData_else_condition');
                            $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
                            $priceHelper = $objectManager->create('Magento\Framework\Pricing\Helper\Data'); 
                        
                            $formattedPrice = $priceHelper->currency($customers['current_balance'], true, false);
                            $cats = explode(',', $customers['categories']);
                            $categoryName='';
                            foreach ($cats as $cat) {
                                $_category = $this->_categoryFactory->create()->load($cat);    
                                $categoryName.=$_category->getName().",";
                            }
                            $templateVariable['message'] = "";
                            
                            $templateVariable['left'] = '0px';
                            $templateVariable['top'] = '96px';
                            $templateVariable['bgcolor'] = '#f00';
                            $templateVariable['color'] = '#fff';

                            if ($customers['sendtemplate_id']) {
                                $templateData = $objectManager->get('Mageants\GiftCertificate\Model\Templates')->load($customers['sendtemplate_id']);
                                if ($templateData->getPositionleft()) { 
                                    $templateVariable['left'] = $templateData->getPositionleft().'px';
                                }
                                if ($templateData->getPositiontop()) {
                                    $templateVariable['top'] = $templateData->getPositiontop().'px';
                                }
                                if ($templateData->getColor()) {
                                    $templateVariable['bgcolor'] = $templateData->getColor() ;
                                }
                                if ($templateData->getForecolor()) {
                                    $templateVariable['color'] = $templateData->getForecolor() ;
                                }
                                if ($templateData->getMessage()) { 
                                    $templateVariable['message'] = $templateData->getMessage();
                                }
                            }
                            
                            if ($customers['comment']) {
                                $templateVariable['message'] = $customers['comment'];
                            }

                            if ($customers['custom_upload']) {
                                $store = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
                                $mediapath = $store->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                                $template_image = $mediapath."giftcertificate/".$customers['template'];
                            } else {
                                $template_image = $customers['template'];       
                            }

                            if ($template_image=="") {
                                $store = $this->_storeManager->getStore();
                                $product = $this->productrepository->getById($productid);
                                $mediaUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                                if ($product->getImage()) {
                                    $template_image = $mediaUrl . 'catalog/product' .$product->getImage();
                                } else {
                                    $template_image =  $this->_assetRepo->getUrl('Magento_Catalog::images/product/placeholder/image.jpg');
                                }
                            }

                            $validity=$customers['expire_at'];
                            if ($validity=='0000-00-00') {
                                $validity='Unlimited';
                            }
                            
                            $templateVariable['recipient_name'] = $customers['recipient_name'];
                            $templateVariable['template'] = $template_image;
                            $templateVariable['code'] = $customers['gift_code'];
                            $templateVariable['sender_email'] = $customers['sender_email'];
                            $templateVariable['sender_name'] = $customers['sender_name'];
                            $templateVariable['current_balance'] = $formattedPrice;
                            $templateVariable['category_name'] = $categoryName;
                            $templateVariable['validity'] = $validity;
                            $templateVariable['recipient_email'] = $customers['recipient_email'];
                            
                            $this->_helper->sendTemplate($templateVariable);
                            
                            $cust_collection = $this->_customer->load($customers['order_id']);
                            $this->_customer->load($customers['order_id'])->setSentgiftcard(1)->save();
                        }
                    }
                }
            }
        }
        return $this;
    }
}