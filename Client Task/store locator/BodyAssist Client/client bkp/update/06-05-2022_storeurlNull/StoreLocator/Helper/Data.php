<?php
/**
 * @category Mageants StoreLocator
 * @package Mageants_StoreLocator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@Mageants.com>
 */
namespace Mageants\StoreLocator\Helper;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;
use Magento\Framework\Controller\ResultFactory;
//use Psr\Log\LoggerInterface;
use Magento\Framework\App\Area;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\MailException;
use Magento\Framework\App\Helper\AbstractHelper;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;

/**
 * Helper Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const EMAIL_TEMPLATE = 'StoreLocator/dealer/template_notification';

    const EMAIL_SERVICE_ENABLE = 'StoreLocator/module/storelocator';

    const EMAIL_RECEIVER = 'StoreLocator/dealer/customer_email_receiver';
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    public $backendUrl;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;
    public $logger;

    /**
     * @param \Magento\Framework\App\Helper\Context
     * @param \Magento\Backend\Model\UrlInterface
     * @param \Magento\Store\Model\StoreManagerInterface
     * @param \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Mageants\StoreLocator\Model\Config\Source\StoreList $storeList,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        TypeListInterface $typeListInterface,
        Pool $pool,
        ResultFactory $resultFactory,
        \Magento\Customer\Model\Session $customerSession,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        //LoggerInterface $logger,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Request\Http $request
    ) {
        parent::__construct($context);
        $this->_backendUrl = $backendUrl;
        $this->storeManager = $storeManager;
        $this->_allStoreList=$storeList;
        $this->scopeConfig = $context->getScopeConfig();
        $this->typeListInterface = $typeListInterface;
        $this->pool = $pool;
        $this->resultFactory = $resultFactory;
        $this->customerSession = $customerSession;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->logger = $context->getLogger();
        $this->messageManager = $messageManager;
        $this->request = $request;
    }

    /**
     * get products tab Url in admin
     *
     * @return string
     */
    public function getProductsGridUrl()
    {
        return $this->_backendUrl->getUrl(
            'storelocator/storelocator/products',
            ['_current' => true]
        );
    }

    /**
    * get StoreLocator enable value
    *
    * @return boolean
    */
    public function getEnableStoreLocator()
    {
        return $this->scopeConfig->getValue(
            "StoreLocator/module/storelocator",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * get StoreLocator Title
     *
     * @return string
     */
    public function getStoreLocatorTitle()
    {
        if ($this->getEnableStoreLocator() == 1) {
            return $this->scopeConfig->getValue(
                "StoreLocator/general/title",
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        } else {
            return null;
        }
    }
    
    /**
     * Get Store Config Value
     *
     * @return string
     */

    public function getConfigValue($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function getCustomerGroupList()
    {
        $list = $this->scopeConfig->getValue(
            "StoreLocator/dealer/selectcustomer",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $list !== null ? explode(',', $list) : [];
    }

    /**
     * Get Dealer Config Value
     *
     * @return string
     */

    public function getDealerConfigValue()
    {
        return $this->scopeConfig->getValue(
            "StoreLocator/dealer/dealer_store",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getFrontName()
    {
        return $this->scopeConfig->getValue(
            "StoreLocator/general/fronturl",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Store List
     * @return Array
     */
    public function getStoreList()
    {
        return $this->_allStoreList->toOptionArray();
    }

    public function getOpen()
    {
        $ret  =  [];
        $ret[0] = ['value' => '0','label' => 'No'];
        $ret[1] = ['value' => '1','label' => 'Yes'];
        return $ret;
    }

    public function cachePrograme()
    {
        $_cacheTypeList = $this->typeListInterface;
        $_cacheFrontendPool = $this->pool;
         
        $types = ['full_page'];
        foreach ($types as $type) {
            $_cacheTypeList->cleanType($type);
        }
        foreach ($_cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
    }

    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }
 
    /*
     * get Current store Info
     */
    public function getStore()
    {
        return $this->storeManager->getStore();
    }

    /**
     * Get Destination Path
     *
     * @return $storename
     */
    public function getStoreName()
    {
        $data = $this->request->getPostValue();
        return $data['sname'];
    }

    /**
     * Send Mail
     *
     * @return $this
     *
     * @throws LocalizedException
     * @throws MailException
     */
    /*
    * get Current store id
    */
    public function sendMail()
    {
        $storeName = "created new store ".$this->getStoreName();
        if ($this->scopeConfig->getValue(
            'StoreLocator/module/storelocator' != 1
        )) {
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('customer/account');
            return $resultRedirect;
        }
        if ($this->customerSession->isLoggedIn()) {
            $customeremail = $this->customerSession->getCustomerData()->getEmail();
            $this->inlineTranslation->suspend();
            $storeId = $this->getStoreId();
            
            $receiver = $this->scopeConfig->getValue(
                'StoreLocator/dealer/customer_email_receiver',
                ScopeInterface::SCOPE_STORE,
                $this->getStoreId()
            );
            $email = $receiver; //set receiver mail

            /* email template */
            $template = $this->scopeConfig->getValue(
                self::EMAIL_TEMPLATE,
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
            $vars = ['mail' => $customeremail,
                    'storename' => $storeName,
                       'store' => $this->getStoreId()
                     ];
            $sender = $this->scopeConfig->getValue(
                'StoreLocator/dealer/sender',
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
            $transport = $this->transportBuilder->setTemplateIdentifier($template)
            ->setTemplateOptions(
                [
                    'area' => Area::AREA_FRONTEND,
                    'store' => $this->getStoreId()
                ]
            )
            ->setTemplateVars(
                $vars
            )->setFrom(
                $sender
            )->addTo(
                $email
            )->getTransport();
            try {
                $transport->sendMessage();
                $this->messageManager->addSuccess(__('Your store has been created please wait for admin approval.'));
            } catch (Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __("The mail functionality not working at the moment."));
            }
            $this->inlineTranslation->resume();
            return $this;
        } else {
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('customer/account/login');
            return $resultRedirect;
        }
    }

    public function sendEditMail()
    {
        $storeName = "edit store ".$this->getStoreName();
        if ($this->scopeConfig->getValue(
            'StoreLocator/module/storelocator' != 1
        )) {
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('customer/account');
            return $resultRedirect;
        }
        if ($this->customerSession->isLoggedIn()) {
            $customeremail = $this->customerSession->getCustomerData()->getEmail();
            $this->inlineTranslation->suspend();
            $storeId = $this->getStoreId();
            
            $receiver = $this->scopeConfig->getValue(
                'StoreLocator/dealer/customer_email_receiver',
                ScopeInterface::SCOPE_STORE,
                $this->getStoreId()
            );
            $email = $receiver; //set receiver mail

            /* email template */
            $template = $this->scopeConfig->getValue(
                self::EMAIL_TEMPLATE,
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
            $vars = ['mail' => $customeremail,
                    'storename' => $storeName,
                       'store' => $this->getStoreId()
                     ];
            $sender = $this->scopeConfig->getValue(
                'StoreLocator/dealer/sender',
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
            $transport = $this->transportBuilder->setTemplateIdentifier($template)
            ->setTemplateOptions(
                [
                    'area' => Area::AREA_FRONTEND,
                    'store' => $this->getStoreId()
                ]
            )
            ->setTemplateVars(
                $vars
            )->setFrom(
                $sender
            )->addTo(
                $email
            )->getTransport();
            try {
                $transport->sendMessage();
                $this->messageManager->addSuccess(__('Your store edit successfully please wait for admin approval.'));
            } catch (Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __("The mail functionality not working at the moment."));
            }
            $this->inlineTranslation->resume();
            return $this;
        } else {
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('customer/account/login');
            return $resultRedirect;
        }
    }

    public function sendEditMailAdmin()
    {
        $storeName = "Your Store ".$this->getStoreName()." is now Enable";
        $storeId = $this->getStoreId();
        $data = $this->request->getPostValue();
        $email = $data['email'];
        $vars = ['storename' => $storeName,
                 'store' => $this->getStoreId()
                ];
        $sender = $this->scopeConfig->getValue(
            'StoreLocator/dealer/sender',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $transport = $this->transportBuilder
            ->setTemplateIdentifier('email_template')
            ->setTemplateOptions(
                [
                    'area' => Area::AREA_FRONTEND,
                    'store' => $this->getStoreId()
                ]
            )
            ->setTemplateVars(
                $vars
            )->setFrom(
                $sender
            )->addTo(
                $email
            )->getTransport();
        try {
            $transport->sendMessage();
            $this->messageManager->addSuccess(__('Store has been approval and mail send to customer.'));
        } catch (Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\RuntimeException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __("The mail functionality not working at the moment."));
        }
        $this->inlineTranslation->resume();
        return $this;
    }

    public function sendAddMailAdmin()
    {
        $storeName = "New Store ".$this->getStoreName()." created by admin";
        $storeId = $this->getStoreId();
        $data = $this->request->getPostValue();
        $email = $data['email'];
        $vars = ['storename' => $storeName,
                 'store' => $this->getStoreId()
                ];
        $sender = $this->scopeConfig->getValue(
            'StoreLocator/dealer/sender',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $transport = $this->transportBuilder
            ->setTemplateIdentifier('email_template')
            ->setTemplateOptions(
                [
                    'area' => Area::AREA_FRONTEND,
                    'store' => $this->getStoreId()
                ]
            )
            ->setTemplateVars(
                $vars
            )->setFrom(
                $sender
            )->addTo(
                $email
            )->getTransport();
        try {
            $transport->sendMessage();
            $this->messageManager->addSuccess(__('New Store has been created and mail send to customer.'));
        } catch (Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\RuntimeException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __("The mail functionality not working at the moment."));
        }
        $this->inlineTranslation->resume();
        return $this;
    }
}
