<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCertificate\Model\Source;
use Magento\Framework\Option\ArrayInterface;

class Website implements ArrayInterface
{
    /**
     * website
     *
     * @var \Magento\Store\Model\Website
     */
    protected $_website;

    /**
     * @param \Magento\Store\Model\Website $website
     */
    public function __construct(
        \Magento\Store\Model\Website $website
    ){     
          $this->_website = $website;
    }

    /**
     * @return Array
     */  
    public function toOptionArray()
    {
        $websites=$this->_website->getCollection();
        $options=array();
        foreach($websites as $website){
            $options[$website->getWebsiteId()]=['label'=>$website->getName(),'value'=>$website->getWebsiteId()];
        }
        return $options;
    }
}