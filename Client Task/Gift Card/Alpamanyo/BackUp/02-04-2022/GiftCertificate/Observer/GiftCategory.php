<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCertificate\Observer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/*
 * RemoveBlock Observer before render block
 */
class GiftCategory implements ObserverInterface
{   
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $registry;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface 
     */ 


    public function __construct(
        \Magento\Framework\Registry $registry
    ) {

        $this->registry = $registry;
    }

    public function execute(Observer $observer)
    {     
        //var_dump(get_class_methods($observer->getEvent()->getData()));exit;
        $category = $this->registry->registry('current_category');

        if($category){
            if($category->getName()=='Gift Card'){
                $layout = $observer->getLayout();
                /**
                 * For Product list page
                 */
                $blocklist = $layout->getBlock('category.products.list');
                if ($blocklist) {                
                     
                            $blocklist->setTemplate('Mageants_GiftCertificate::product/list.phtml');
                   
          
                }       
             }   
          }   
    }
}