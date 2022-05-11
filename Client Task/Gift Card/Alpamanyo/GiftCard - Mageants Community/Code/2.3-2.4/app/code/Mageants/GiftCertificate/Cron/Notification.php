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
class Notification extends \Magento\Framework\View\Element\Template
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
    }
    
    /**
     * Method executed when cron runs in server
     */
    public function execute() {
        
        if($this->_helper->isNotify()){
            $accountCollection = $this->_account->getCollection()->addFieldToFilter('expire_at',['neq' => '0000-00-00']);
            $notifyBefore = $this->_helper->notifyBefore();
            $orderIds= array();
             $accountCollection->getSelect()->join(
                'gift_code_customer',
            // note this join clause!
                'main_table.order_id = gift_code_customer.customer_id',
                array('*')
            );
             $daylen = 60*60*24;
            if($accountCollection){
                foreach ($accountCollection as $account) {
                    
                    $currentDate = strtotime($this->date->gmtDate('Y-m-d'));
                    $expirydate = strtotime($account->getExpireAt());

                    $len = ($expirydate-$currentDate)/$daylen;
                    if(((int)$notifyBefore == (int)$len) && !$account->getNotified()){
                        $orderIds[]=$account->getOrderId();
                        $this->inlineTranslation->suspend();
                        try {
                            
                            $postObject = new \Magento\Framework\DataObject();
                            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                            $currencysymbol = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
                            $currency = $currencysymbol->getStore()->getCurrentCurrencyCode(); 
                            $codevalue = $currency.$account->getCodeValue();
                            $current_balance = $currency.$account->getCurrentBalance();
                            $template_image = $account->getTemplate();
                     
                     if($account->getCustomUpload()){
                        $mediapath = $currencysymbol->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                        $template_image = $mediapath."giftcertificate/".$account->getTemplate();
                     }
                       $_data = array('name'=>$account->getRecipientName(),'code'=>$account->getGiftCode(), 'expire_at'=>$account->getExpireAt(),'template'=>$template_image, 'current_balance'=>$current_balance, 'code_value'=>$codevalue, 'sender_name'=>$account->getSenderName()); 
                       $postObject->setData($_data);

                        $error = false;

                        $sender = [
                        'name' => $this->_escaper->escapeHtml($account->getSenderName()),
                        'email' => $this->_escaper->escapeHtml($account->getSenderEmail()),
                        ];

                        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE; 
                        $transport = $this->_transportBuilder
                        ->setTemplateIdentifier($this->_helper->getNotifyTemplate()) 
                        ->setTemplateOptions(
                        [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                        ]
                        )
                        ->setTemplateVars(['data' => $postObject])
                        ->setFrom($sender)
                        ->addTo($account->getRecipientEmail())
                        ->getTransport();

                        $transport->sendMessage();
                        $updateNotified = array('notified'=>1);

                        $this->_account->setData($updateNotified);
                        $this->_account->setAccountId($account->getAccountId());
                        $this->_account->save();
                        $this->inlineTranslation->resume();
                        } catch (\Exception $e) {
                            $this->inlineTranslation->resume();
                            $this->_logger->debug('Email can\'t be sent'.$e->getMessage());        
                        }
                    }    
                }
            }
         
          }  
         
        $this->_logger->debug('Running Cron from Test class');
        return $this;
    }
}