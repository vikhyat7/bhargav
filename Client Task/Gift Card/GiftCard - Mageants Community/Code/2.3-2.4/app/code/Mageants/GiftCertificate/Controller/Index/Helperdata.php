<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\GiftCertificate\Controller\Index;
/**
 * Check gift code Details
 */
class Helperdata extends \Magento\Framework\App\Action\Action
{
    /**
     * helper object
     *
     * @var \Mageants\GiftCertificate\Helper\Data
     */
    // private $jsonResultFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Mageants\GiftCertificate\Helper\Data $helper
     */
    public function __construct
	(	
		\Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
	)
    {
	    $this->resultJsonFactory = $resultJsonFactory;
         $this->_resultLayoutFactory = $resultLayoutFactory;
    	parent::__construct($context);          
	}

    /**
     *  chek gift code and return detail of the code
     */
    public function execute()
    {
        $resultLayout = $this->_resultLayoutFactory->create();
        $data = $resultLayout->getLayout()->getBlock('gift_certificate')->getMinPrice();
        return $data;
        // var_dump($data);
       	/*$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
        
        $collection = $productCollection->create()->addAttributeToFilter('type_id', 'giftcertificate');
        foreach ($collection as $value) {

            echo "<pre>";
            // print_r(get_class_methods($value)); 
            // print_r($value->getAttribute());
            print_r($value->getData()); 
            print_r($value->getAttributeCode()()->getCustomAttributeName());
            foreach ($value->getAttributes() as $value) {
                
                if($value->getAttributeCode() == "minprice")
                {
                    $entityType = 'catalog_product';
                    $attribute = $objectManager->get(\Magento\Eav\Model\Entity\Attribute::class)->loadByCode($entityType,$value->getAttributeCode()); 
                    echo $attribute->getData($value->getAttributeCode());
                }
            }
        }*/
                
        // exit();
  //      	$result = $this->resultJsonFactory->create();
		// return $result->setData(['value' => $data]);
 	}
}
