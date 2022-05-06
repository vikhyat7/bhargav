<?php
/**
 * @category Mageants BarcodeGenerator
 * @package Mageants_BarcodeGenerator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\BarcodeGenerator\Model\Config\Source;

class BarcodeTypes implements \Magento\Framework\Data\OptionSourceInterface
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'ean8',  'label' => __('EAN-8')],
            ['value' => 'ean13',  'label' => __('EAN-13')],
            ['value' => 'upca',  'label' => __('UPC-A')],
            ['value' => 'upce',  'label' => __('UPC-E')],
            ['value' => 'code128',  'label' => __('Code 128')],
            ['value' => 'code39',  'label' => __('Code 39 (Code 3 of 9)')],
            ['value' => 'code25interleaved',  'label' => __('Interleaved 2 of 5')],
            ['value' => 'code25',  'label' => __('Standard 2 of 5')],
            ['value' => 'royalmail',  'label' => __('Royal Mail 4-state Customer barcode')],
            ['value' => 'identcode',  'label' => __('Ident Code')],
            ['value' => 'itf14',  'label' => __('Interleaved Two of Five 14 Code')],
            ['value' => 'postnet',  'label' => __('POSTNET')],
            ['value' => 'planet',  'label' => __('PLANET')],
            ['value' => 'leitcode',  'label' => __('Leit Code')],
        ];
    }
}
