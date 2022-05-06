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
 * Cpdeset classs for option
 */ 
class Codeset extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Code List
     *
     * @var \Mageants\GiftCertificate\Model\Codelist
     */
    protected $codelist;
    
    /**
     * Code set
     *
     * @var \Mageants\GiftCertificate\Model\Codeset
     */
    protected $_codeset;

    /**
     * @param \Mageants\GiftCertificate\Model\Codelist $codelist
     * @param \Mageants\GiftCertificate\Model\Codeset $codeset
     */
    public function __construct(
		\Mageants\GiftCertificate\Model\Codelist $codelist,
		\Mageants\GiftCertificate\Model\Codeset $codeset
    ) {
		$this->codelist = $codelist;
		$this->_codeset = $codeset;
    }

    /**
     * @return Array
     */
	public function getAllOptions()
    {
		$_codelist=$this->codelist->getCollection()->addFieldToFilter('allocate','0');
		$codesetids=array();
		foreach($_codelist as $codelist){
			$codesetids[]=$codelist->getCodeSetId();
		}
		$codesetArray=array_unique($codesetids);
		$options=array();
		foreach($codesetArray as $codeid){
			$codeset=$this->_codeset->load($codeid);
			$options[]=array('value'=>$codeset->getCodeSetId(),'label'=>$codeset->getCodeTitle());
        }  
		return $options;
	}
}

