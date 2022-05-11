<?php
/**
 * @category Mageants MaintenanceMode
 * @package Mageants_MaintenanceMode
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\MaintenanceMode\Block\Adminhtml\System;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Url;

/**
 * Class PreviewButton
 *
 * @package Mageants\MaintenanceMode\Block\Adminhtml\System
 */
class PreviewButton extends Field
{
    /**
     * @var string
     */
    protected $_template = 'system/config/previewbutton.phtml';

    /**
     * @var Url
     */
    protected $_frontendUrl;

    /**
     * PreviewButton constructor.
     *
     * @param Url $frontendUrl
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Url $frontendUrl,
        Context $context,
        array $data = []
    ) {
        $this->_frontendUrl = $frontendUrl;

        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope();

        return parent::render($element);
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $originalData = $element->getOriginalData();

        $this->addData([
            'button_label' => $originalData['button_label'],
            'html_id'      => $element->getHtmlId()
        ]);

        return $this->_toHtml();
    }

    /**
     * @param $url
     *
     * @return string
     */
    public function getDataUrl($url)
    {
        return $this->_frontendUrl->getUrl(
            $url,
            ['_nosid' => true, 'form_key' => $this->getFormKey()]
        );
    }

    /**
     * Copy from the Magento core.
     *
     * @param string $string
     * @param bool $escapeSingleQuote
     *
     * @return string
     */
    public function escapeHtmlAttr($string, $escapeSingleQuote = true)
    {
        return $this->_escaper->escapeHtmlAttr($string, $escapeSingleQuote);
    }

    /**
     * Copy from the Magento core.
     *
     * @param string $string
     *
     * @return string
     */
    public function escapeJs($string)
    {
        return $this->_escaper->escapeJs($string);
    }
}
