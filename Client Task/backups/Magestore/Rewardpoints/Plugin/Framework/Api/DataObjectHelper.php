<?php

namespace Magestore\Rewardpoints\Plugin\Framework\Api;

class DataObjectHelper
{
    public function beforePopulateWithArray(
        \Magento\Framework\Api\DataObjectHelper $subject,
        $dataObject, array $data, $interfaceName
    ) {
        if ($interfaceName == \Magento\Quote\Api\Data\TotalsInterface::class) {
            if ($dataObject instanceof \Magento\Quote\Model\Cart\Totals) {
                if (
                    isset($data['extension_attributes']) &&
                    ($data['extension_attributes'] instanceof \Magento\Quote\Api\Data\AddressExtension)
                ) {
                    unset($data['extension_attributes']);
                }
            }
        }
        return [$dataObject, $data, $interfaceName];
    }
}