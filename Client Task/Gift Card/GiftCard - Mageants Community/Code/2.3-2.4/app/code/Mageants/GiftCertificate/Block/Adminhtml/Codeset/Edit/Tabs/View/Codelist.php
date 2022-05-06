<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\GiftCertificate\Block\Adminhtml\Codeset\Edit\Tabs\View;
use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
/**
 * Codelist class
 */ 
class Codelist extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var  \Mageants\GiftCertificate\Model\ResourceModel\Codelist\CollectionFactory
     */
    protected $_codesetFacotory;

    /**
     * @var  \Mageants\GiftCertificate\Model\Codelist
     */    
    protected $_codesetModel;

    /**
     * @var  \Magento\Backend\Model\Session
     */    
    protected $_sessionId;  

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Mageants\GiftCertificate\Model\ResourceModel\Codelist\CollectionFactory $codesetFacotory
     * @param \Mageants\GiftCertificate\Model\Codelist $codesetModel
     * @param \Magento\Backend\Model\Session $session,
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Mageants\GiftCertificate\Model\ResourceModel\Codelist\CollectionFactory $codesetFacotory,
        \Mageants\GiftCertificate\Model\Codelist $codesetModel,
        array $data = []
    ) {
        $this->_codesetFacotory = $codesetFacotory;
        $this->_codesetModel = $codesetModel;
        //$this->_sessionId = $session;
        parent::__construct($context, $backendHelper, $data);
    }

	/**
     * _construct
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('code_list_id');
        $this->setDefaultSort('code_list_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * add Column Filter To Collection
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        $this->getCollection()->addFieldToFilter($column->getId(), $column->getFilter()->getValue());
        return $this;
    }

    /**
     * prepare collection
     * @return $this
     */
    protected function _prepareCollection()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $session=$objectManager->create("\Magento\Backend\Model\Session");
        
        $codesetid=$session->getCodeId();
        $collection = $this->_codesetFacotory->create();
        $collection->addFieldToFilter('code_set_id',$codesetid);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare column
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'code',
            [
                'header' => __('code'),
                'index' => 'code',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );
        
        $this->addColumn(
            'allocate',
            [
                'header' => __('Allocated'),
                'index' => 'allocate',
                'type' => 'options',
                'options' => array(0=>"No", 1=>"Yes"),
            ]
        );
        
        $this->addColumn('action_edit', array(
            'header'   =>__('Delete'),
            'width'    => 15,
            'sortable' => false,
            'filter'   => false,
            'type'     => 'action',
            'renderer'=> 'Mageants\GiftCertificate\Block\Adminhtml\Codeset\Edit\Tabs\View\Deletelinks',        
        ));
        return parent::_prepareColumns();
    }
   
    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/codelist', ['_current' => true]);
    }

    /**
     * @param  object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return true;
    }
}
