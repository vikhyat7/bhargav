<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCertificate\Model;
use Magento\Checkout\Model\ConfigProviderInterface;

/**
 *  Gift Certificate config provider
 */
class GiftCertificateConfigProvider implements ConfigProviderInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Psr\Log\LoggerInterface $logger

    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->checkoutSession = $checkoutSession;
        $this->cart = $cart;
        $this->logger = $logger;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $_gcstatus=$this->scopeConfig->getValue('giftcertificate/general/statusgc', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $status = ($_gcstatus == 1) ? true : false;

        $GiftCertificate = [];
        $GiftCertificate['giftcertificatestatus'] = $status;
        $GiftCertificate['giftcategoryids'] = $this->getCategories();
        $GiftCertificate['giftcertificatecode'] = $this->checkoutSession->getGiftCertificateCode();
       /* var_dump($this->checkoutSession->getGiftCertificateCode());
        echo "string";exit();*/
        return $GiftCertificate;
    }

    public function getCategories()
    {
        $items = $this->cart->getQuote()->getAllVisibleItems();
        $cat_ids = [];
        if ($items) {
            foreach ($items as $item) {
                $cat_id = "";
                foreach ($item->getProduct()->getCategoryIds() as $categoryid) {
                    if ($cat_id == "") {
                        $cat_id = $categoryid;
                    }
                    else{
                        $cat_id = $cat_id.",".$categoryid;                        
                    }
                }
                $cat_ids[] = $cat_id;
            }
        }
        return $cat_ids;
    }
}
