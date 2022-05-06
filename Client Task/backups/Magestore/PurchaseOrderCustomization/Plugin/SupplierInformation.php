<?php

namespace Magestore\PurchaseOrderCustomization\Plugin;

use Magento\Ui\Component\Form\Field;

/**
 * Class SupplierInformation
 *
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD)
 * @package Magestore\PurchaseOrderCustomization\Plugin
 */
class SupplierInformation
{

    /**
     * After Get Supplier Infor Children
     *
     * @param \Magestore\SupplierSuccess\Ui\DataProvider\Supplier\DataForm\Modifier\Information $subject
     * @param $result
     * @return mixed
     */
    public function afterGetSupplierInformationChildren(
        \Magestore\SupplierSuccess\Ui\DataProvider\Supplier\DataForm\Modifier\Information $subject,
        $result
    ) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $paymentTermService = $objectManager->get(\Magestore\PurchaseOrderCustomization\Service\PaymentTermService::class);
        $result['payment_term'] = $subject->getField(
            __('Payment Term'),
            Field::NAME,
            true,
            'text',
            'select',
            [],
            null,
            $paymentTermService->toPaymentTermOptionArray()
        );
        $result['payment_term']['arguments']['data']['config']['default'] =
            \Magestore\PurchaseOrderCustomization\Service\PaymentTermService::TERM_90_DAY;
        return $result;
    }
}