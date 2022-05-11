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
 * Categories classs for category array
 */ 
class Categories extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{ 
	/**
     * Category factory
     *
     * @var \Magento\Catalog\Model\CategoryFactory
     */
	protected $_categoryFactory;
  
	/**
     * category
     *
     * @var \Magento\Catalog\Model\Category
     */
	protected $_categories;

	/**
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Model\Category $categories
     */
	public function __construct(
		\Magento\Catalog\Model\CategoryFactory $categoryFactory,
		\Magento\Catalog\Model\Category $categories
	) {
		$this->_categoryFactory = $categoryFactory;
		$this->_categories = $categories;
	}
  
	/**
	 * @return Array
     */
	public function getAllOptions()
	{
		$collection = $this->_categoryFactory->create()->getCollection()->addFieldToFilter('is_active',1);
		$options=array();
		if ($collection->getSize()) {
			foreach($collection as $template){
				$cat=$this->_categories->load($template->getEntityId());
				if($cat->getName()!='Gift Card'):
					$options[$template->getEntityId()]=array('value'=>$template->getEntityId(),'label'=>$cat->getName());
				endif;  
			}
		}
		return $options;
	}
}
