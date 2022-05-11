<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCertificate\Model\Config\Source;
use Magento\Framework\Controller\ResultFactory;
/**
 * Gift Image template class
 */ 
class Giftimages extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * model Templates
     *
     * @var \Mageants\GiftCertificate\Model\Templates
     */
    protected $_modelTemplates;

    /**
     * @param \Mageants\GiftCertificate\Model\Templates $modelTemplates
     */
    public function __construct(
		\Mageants\GiftCertificate\Model\Templates $modelTemplates
    ) {
		$this->_modelTemplates = $modelTemplates;
    }

    /**
     * @return Array
     */
    public function getAllOptions()
    {      
		$_templateCollection=$this->_modelTemplates->getCollection()->addFieldToFilter('status','1');
		$options=array();
		foreach($_templateCollection as $template){
       		$options[$template->getImageId()]=array('value'=>$template->getImageId(),'label'=>$template->getImageTitle());
		}
		return $options;
    }
}
