<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
 
namespace Mageants\GiftCertificate\Block\Adminhtml\Account\Edit\Tabs\View;
use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

/**
 * Order classs for Fetch order for Grid
 */ 
class Order extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * account 
     *
     * @var \Magento\Sales\Model\Order
     */
    protected $_account;

    /**
     * Session for Get order Id
     *
     * @var  \Magento\Backend\Model\Session
     */
    protected $_sessionId;  

   /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Sales\Model\Order $account
     * @param \Magento\Backend\Model\Session $session
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Sales\Model\Order $account,
        array $data = []
    ) {
        $this->_account = $account;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * _construct
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('id');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * add Column Filter To Collection
     */
     protected function _addColumnFilterToCollection($column)
    {
        $this->getCollection()->addFieldToFilter($column->getId(), $column->getFilter()->getValue());
        return $this;
    }

    /**
     * prepare collection
     */
    protected function _prepareCollection()
    {
       $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $_sessionId=$objectManager->create("\Magento\Backend\Model\Session");    
        $orderid=$_sessionId->getOrderId();
        $collection = $this->_account->getCollection();
        $collection->addFieldToFilter('increment_id',$orderid);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare column for Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('id'),
                'index' => 'entity_id',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );
        
        $this->addColumn(
            'created_at',
            [
                'header' => __('Purchase On'),
                'index' => 'created_at',
                'type' => 'date',
            ]
        );
        
        $this->addColumn(
            'createcustomer_firstname',
            [
                'header' => __('Customer Name'),
                'index' => 'customer_firstname',
                'type' => 'text',
            ]
        );
        
        $this->addColumn(
            'base_grand_total',
            [
                'header' => __('Total Amount'),
                'index' => 'base_grand_total',
                'type' => 'text',
            ]
        );
        

        return parent::_prepareColumns();
    }

    /**
     * @return $string
     */     
    public function getGridUrl()
    {
        return $this->getUrl('*/*/order', ['_current' => true]);
    }

    /**
     * @param  object $row
     * @return string=null
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
