<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
 
namespace Mageants\GiftCertificate\Block\Adminhtml\Template\Edit;
/**
 * Template class for template of GiftCertificate
 */ 
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) 
    {
        parent::__construct($context, $registry, $formFactory, $data);
    } 

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('template_data');

        
        $form = $this->_formFactory->create(
            ['data' => [
                            'id' => 'edit_form', 
                            'enctype' => 'multipart/form-data', 
                            'action' => $this->getData('action'), 
                            'method' => 'post'
                        ]
            ]
        );
 
        $form->setHtmlIdPrefix('rock_');
        if ($model->getImageId()) {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Edit Template'), 'class' => 'fieldset-wide']
            );
            $fieldset->addField('rocktechtemplate_id', 'hidden', ['name' => 'rocktechtemplate_id']);
        } else {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Add Template'), 'class' => 'fieldset-wide']
            );
        }
        if($model->getImageId())
        {
            $fieldset->addField(
                'image_id',
                'hidden',
                [
                    'name' => 'image_id',
                    'id' => 'image_id',
                ]
            );
        }   
       
        $fieldset->addField(
            'image_title',
            'text',
            [
                'name' => 'image_title',
                'label' => __('Image Title'),
                'id' => 'image_title',
                'title' => __('Image Title'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );
        $fieldset->addField(
            'image_title_upoad',
            'hidden',
            [
                'name' => 'image_title_upoad',
                'label' => __('Image Title'),
                'id' => 'image_title',
                'title' => __('Image Title'),
             
            ]
        );
        
        
        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'id' => 'status',
                'title' => __('Status'),
                'class' => 'required-entry',
                'required' => true,
                'value'=>'1',
                 'values' => array('0'=>'Inactive','1'=>'Active'),
            ]
        );
    
        $fieldset->addField(
            'image',
            'image',
            [
                'name' => 'image',
                'label' => __('Image'),
                'title' => __('Image'),
                'id' => 'image',
                'required' => true,
                'note' => 'Allow image type: jpg, jpeg, gif, png',
            ]
        );  
         $fieldset->addType(
          'mycustomfield',
            '\Mageants\GiftCertificate\Block\Adminhtml\Template\Edit\Renderer\Template'
        );    
          $fieldset->addField(
        'message',
        'mycustomfield',
        [
            'name'  => 'message',
            'label' => __('Message'),
            'title' => __('Message'),
            
        ]
        );
         $field = $fieldset->addField(
           'color',
           'text',
           [
              'name' => 'color',
              'label' => __('Background Color'),
              'class'  => 'jscolor {hash:true,refine:false}',
              'title' => __('Color')
            ]
        );

         $field = $fieldset->addField(
           'forecolor',
           'text',
           [
              'name' => 'forecolor',
              'label' => __('Text Color'),
              'class'  => 'jscolor {hash:true,refine:false}',
              'title' => __('Color')
            ]
        );
         
        $fieldset->addField('temp_url', 'hidden', ['name' => 'temp_url']);
        /*$renderer = $this->getLayout()->createBlock('\Mageants\GiftCertificate\Block\Adminhtml\Template\Edit\Renderer\Color'); 
        $field->setRenderer($renderer);*/
        $fieldset->addField('positionid', 'hidden', ['name' => 'positionleft']);
        $fieldset->addField('topposition', 'hidden', ['name' => 'positiontop']);
        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
