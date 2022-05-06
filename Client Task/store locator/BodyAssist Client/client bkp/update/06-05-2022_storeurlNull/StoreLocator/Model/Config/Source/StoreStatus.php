<?php

namespace Mageants\StoreLocator\Model\Config\Source;

class StoreStatus implements \Magento\Framework\Data\OptionSourceInterface
{
    public $manageStore;

    public function toOptionArray()
    {
        $instance = \Magento\Framework\App\ObjectManager::getInstance();
        $product_collections = $instance ->get('\Mageants\StoreLocator\Model\ResourceModel\ManageStore\CollectionFactory');
        $collections = $product_collections->create();
        $options = [];
        if (empty($collections)) {
            $options[] = ['label' => __('-- Please Select a Status --'), 'value' => ''];
        }
        $statusArr = [];
        foreach ($collections as $Status) {
            $sttatusType = $Status->getData();
            $statusArr[$sttatusType['store_type_status']]=$sttatusType['store_type_status'];
        }
        foreach ($statusArr as $key => $value) {
            $options[] = [
                'value' => $key,
                'label' => $value
            ];
        }
        return $options;
    }
}
