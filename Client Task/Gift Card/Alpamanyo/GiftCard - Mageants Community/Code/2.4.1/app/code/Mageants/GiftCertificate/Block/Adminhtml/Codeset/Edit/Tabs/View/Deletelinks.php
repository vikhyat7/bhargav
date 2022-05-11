<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\GiftCertificate\Block\Adminhtml\Codeset\Edit\Tabs\View;

/**
 * DeleteLinks class
 */ 
class Deletelinks extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
	 /**
	  * @param  object $row
      * @return string
      */
	 public function render(\Magento\Framework\DataObject $row){
		$rowId = $row->getData($this->getColumn()->getIndex());
		$link="<a href='".$this->getUrl('giftcertificate/index/delete/',array('id'=>$row->getId()))."' >Delete </a>";
		return html_entity_decode($link);
	 }
}
