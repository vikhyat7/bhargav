<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCertificate\Model\Product\Type;
/**
 * Gift class for create Gift Type product
 */
class Gift extends \Magento\Catalog\Model\Product\Type\AbstractType 
{
  public function isVirtual($product)
  {
      $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    
      $gifttype = $objectManager->get('Magento\Catalog\Model\Product')->load($product->getId());

      $giftquote = $objectManager->get('\Mageants\GiftCertificate\Model\Giftquote');

      $customer_id = $objectManager->get('\Mageants\GiftCertificate\Helper\Data')->getCustomerId();
      if($customer_id != null)
      {
        $gift_data = $giftquote->getCollection()->addFieldToFilter('customer_id',$customer_id)->getData();
      }
      else
      {
        $gift_data = $giftquote->getCollection()->addFieldToFilter('customer_id',0)->getData();
      }
      $giftdata_cardtype = 'test';
      if(!empty($product->getId())){
        foreach ($gift_data as $key => $value) {
          if($value['product_id'] == $product->getId()){
              $giftdata_cardtype = $value['card_types'];
          }
        }
      }

      /* Checked Gift Type is Virtual */
      if($gifttype->getGifttype() == 0)
      {
        return true;
      }   
      /* When Select Gift Type is Virtual From Frontend */
      elseif (isset($giftdata_cardtype)) 
      {
        if($giftdata_cardtype == 0)
        {
          return true;
        }else{
          return false;
        }
      }else
      {
        return false;
      }
  }

    /**
     * Check that product of this type has weight
     *
     * @return bool
     */
    public function hasWeight()
    {
        return false;
    }
  /**
   * For Delete type related Data
   */
    public function deleteTypeSpecificData(\Magento\Catalog\Model\Product $product)
    {
      
    }
}
