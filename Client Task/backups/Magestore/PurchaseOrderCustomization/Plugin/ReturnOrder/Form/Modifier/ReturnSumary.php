<?php

namespace Magestore\PurchaseOrderCustomization\Plugin\ReturnOrder\Form\Modifier;

use Magestore\PurchaseOrderCustomization\Block\Adminhtml\ReturnOrder\Edit\Fieldset\ReturnSummary\Total;

/**
 * Class ReturnSumary
 *
 * @package Magestore\PurchaseOrderCustomization\Plugin\ReturnOrder\Form\Modifier
 */
class ReturnSumary
{
    /**
     * After get purchase sumary children
     *
     * @param \Magestore\PurchaseOrderSuccess\Ui\DataProvider\ReturnOrder\Form\Modifier\ReturnSumary $subject
     * @param mixed $result
     * @return mixed
     */
    public function afterGetPurchaseSumaryChildren(
        \Magestore\PurchaseOrderSuccess\Ui\DataProvider\ReturnOrder\Form\Modifier\ReturnSumary $subject,
        $result
    ) {
        $result['return_order_sumary_total'] = $this->getReturnSumaryTotal($subject);
        return $result;
    }

    /**
     * Get return sumary total
     *
     * @param mixed $subject
     * @return mixed
     */
    public function getReturnSumaryTotal($subject)
    {
        return $subject->addHtmlContentContainer(
            'return_sumary_total_container',
            Total::class
        );
    }
}
