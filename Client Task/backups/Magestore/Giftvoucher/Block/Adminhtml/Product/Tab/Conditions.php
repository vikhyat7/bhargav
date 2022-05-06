<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Block\Adminhtml\Product\Tab;

use Magento\Framework\App\ObjectManager;

/**
 * Class Conditions
 *
 * Product - Tab - Conditions block
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Conditions extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Ui\Component\Layout\Tabs\TabInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
     */
    protected $_rendererFieldset;

    /**
     * @var \Magento\Rule\Block\Conditions
     */
    protected $_conditions;

    /**
     * @var string
     */
    protected $_nameInLayout = 'conditions_apply_to';

    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    private $ruleFactory;

    protected $productFactory;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Rule\Block\Conditions $conditions
     * @param \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param \Magestore\Giftvoucher\Model\ProductFactory $productFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Rule\Block\Conditions $conditions,
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magestore\Giftvoucher\Model\ProductFactory $productFactory,
        array $data = []
    ) {
        $this->_rendererFieldset = $rendererFieldset;
        $this->_conditions = $conditions;
        $this->productFactory = $productFactory;
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * The getter function to get the new RuleFactory dependency
     *
     * @return \Magento\SalesRule\Model\RuleFactory
     *
     * @deprecated
     */
    private function getRuleFactory()
    {
        if ($this->ruleFactory === null) {
            $this->ruleFactory = ObjectManager::getInstance()->get(\Magento\SalesRule\Model\RuleFactory::class);
        }
        return $this->ruleFactory;
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function getTabClass()
    {
        return null;
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function getTabUrl()
    {
        return null;
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function isAjaxLoaded()
    {
        return false;
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function getTabLabel()
    {
        return __('Conditions');
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function getTabTitle()
    {
        return __('Conditions');
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Get Product
     *
     * @return mixed
     */
    public function getProduct()
    {
        if (!$this->getData('product') instanceof \Magento\Catalog\Model\Product) {
            $this->setData('product', $this->_coreRegistry->registry('product'));
        }
        return $this->getData('product');
    }

    /**
     * @inheritDoc
     */
    protected function _prepareForm()
    {
        $product = $this->getProduct();
        $model = $this->productFactory->create();

        if (!$model->getId() && $product->getId()) {
            $model->loadByProduct($product);
        }

        $model->setData('conditions', $model->getData('conditions_serialized'));
        $form = $this->addTabToForm($model);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Handles addition of conditions tab to supplied form.
     *
     * @param \Magento\SalesRule\Model\Rule $model
     * @param string $fieldsetId
     * @param string $formName
     * @return \Magento\Framework\Data\Form
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addTabToForm($model, $fieldsetId = 'shopping_cart_conditions_fieldset', $formName = 'product_form')
    {
        if (!$model) {
            $id = $this->getRequest()->getParam('id');
            $model = $this->getRuleFactory()->create();
            $model->load($id);
        }
        $conditionsFieldSetId = $model->getConditionsFieldSetId($formName);
        $newChildUrl = $this->getUrl(
            'sales_rule/promo_quote/newConditionHtml/form/' . $conditionsFieldSetId,
            ['form_namespace' => $formName]
        );

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');
        $renderer = $this->_rendererFieldset->setTemplate(
            'Magento_CatalogRule::promo/fieldset.phtml'
        )->setNewChildUrl(
            $newChildUrl
        )->setFieldSetId(
            $conditionsFieldSetId
        );

        $fieldset = $form->addFieldset('description_fieldset', ['legend' => __('Description')]);

        $fieldset->addField(
            'giftcard_description',
            'editor',
            [
                'name' => 'giftcard_description',
                'label' => __('Describe conditions applied to shopping cart when using this gift code'),
                'title' => __('Describe conditions applied to shopping cart when using this gift code'),
                'style' => 'height:18em',
                'config' => $this->_wysiwygConfig->getConfig(),
                'data-form-part' => $formName
            ]
        );

        $fieldset = $form->addFieldset(
            $fieldsetId,
            [
                'legend' => __(
                    'Allow using Gift Card only if the following shopping cart conditions are met'
                    .' (leave blank for all shopping carts)'
                )
            ]
        )->setRenderer(
            $renderer
        );
        $fieldset->addField(
            'conditions',
            'text',
            [
                'name'           => 'conditions',
                'label'          => __('Conditions'),
                'title'          => __('Conditions'),
                'required'       => true,
                'data-form-part' => $formName
            ]
        )->setRule(
            $model
        )->setRenderer(
            $this->_conditions
        );

        $form->setValues($model->getData());
        $this->setConditionFormName($model->getConditions(), $formName);
        return $form;
    }

    /**
     * Handles addition of form name to condition and its conditions.
     *
     * @param \Magento\Rule\Model\Condition\AbstractCondition $conditions
     * @param string $formName
     * @return void
     */
    private function setConditionFormName(\Magento\Rule\Model\Condition\AbstractCondition $conditions, $formName)
    {
        $conditions->setFormName($formName);
        if ($conditions->getConditions() && is_array($conditions->getConditions())) {
            foreach ($conditions->getConditions() as $condition) {
                $this->setConditionFormName($condition, $formName);
            }
        }
    }
}
