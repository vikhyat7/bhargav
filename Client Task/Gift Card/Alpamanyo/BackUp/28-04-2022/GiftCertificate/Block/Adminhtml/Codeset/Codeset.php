<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author mageants Team <support@mageants.com>
 */
 
namespace Mageants\GiftCertificate\Block\Adminhtml\Codeset;

/**
 * codeset class for codelist grid
 */ 
class Codeset extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * _construct
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'code_set_id';
        $this->_blockGroup = 'Mageants_GiftCertificate';
        $this->_controller = 'adminhtml_codeset';
        parent::_construct();
        $this->buttonList->update('back', 'onclick', "setLocation('" . $this->getUrl('giftcertificate/index/') . "')");
        $this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ]
                ],
                -100
            );
        $this->buttonList->remove('reset'); 
    }
 
    /**
     * 
     * @return string
     */   
    public function getHeaderText()
    {
        return __('Add Codeset');
    }
 
    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
    
    /**
     * 
     * @return string
     */
    public function getFormActionUrl()
    {
        if ($this->hasFormActionUrl()) {
            return $this->getData('form_action_url');
        }
 
        return $this->getUrl('*/*/save');
    }

    /**
     * save and continue url 
     * @return string
     */  
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '']);
    }
}
