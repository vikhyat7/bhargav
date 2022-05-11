<?php

namespace Magestore\PurchaseOrderCustomization\Model\Supplier\Transaction\Options;

/**
 * Class DescriptionOptions
 *
 * @package Magestore\PurchaseOrderCustomization\Model\Supplier\Transaction\Options
 */
class DescriptionOptions implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * DescriptionOptions constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Serialize\SerializerInterface $serializer
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
    }

    /**
     * To Option Type Array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $descriptionOptionsValue = $this->scopeConfig->getValue('suppliersuccess/description_config/title');
        $descriptionOptionsValue =
            $descriptionOptionsValue ? $this->serializer->unserialize($descriptionOptionsValue) : [];
        if (is_array($descriptionOptionsValue)) {
            foreach ($descriptionOptionsValue as $value) {
                $options[] = [
                    'value' => $value['title'],
                    'label' => $value['title']
                ];
            }
        }
        return $options;
    }
}
