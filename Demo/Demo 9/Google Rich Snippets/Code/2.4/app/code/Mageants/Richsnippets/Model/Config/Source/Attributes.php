<?php
/**
 * @category Mageants Richsnippets
 * @package Mageants_Richsnippets
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\Richsnippets\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Attributes implements OptionSourceInterface
{
    /**
     * Get Grid row status type labels array.
     * @return array
     */
    protected $product;

    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection $attributeCollection
    ) {
        $this->attributeCollection = $attributeCollection;
    }
    public function toOptionArray()
    {
        $attribute = [];
        $attributefactory = $this->attributeCollection->addFieldToSelect('attribute_id')->addFieldToSelect('attribute_code')->load()->getData();
        
        foreach ($attributefactory as $attributes) {
            $attribute[$attributes['attribute_id']] = ['value' => $attributes['attribute_code'],'label'=>$attributes['attribute_code']];
        }
        return $attribute;
    }
}
