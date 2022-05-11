<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilReport\Controller\Adminhtml\Report;

/**
 * Class AbstractReport
 *
 * Use to create abstract report
 */
abstract class AbstractReport extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magestore_FulfilReport::reports';

    /**
     * @var \Magestore\FulfilSuccess\Api\FulfilManagementInterface
     */
    protected $fulfilManagement;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $_dateFilter;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * AbstractReport constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magestore\FulfilSuccess\Api\FulfilManagementInterface $fulfilManagement
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magestore\FulfilSuccess\Api\FulfilManagementInterface $fulfilManagement,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        parent::__construct($context);
        $this->fulfilManagement = $fulfilManagement;
        $this->_fileFactory = $fileFactory;
        $this->_dateFilter = $dateFilter;
        $this->_date = $date;
    }

    /**
     * Add report breadcrumbs
     *
     * @return $this
     */
    public function _initAction()
    {
        $this->_view->loadLayout();
        $this->_addBreadcrumb(__('Reports'), __('Reports'));
        return $this;
    }

    /**
     * Report action init operations
     *
     * @param array|\Magento\Framework\DataObject $blocks
     * @return $this
     */
    public function _initReportAction($blocks)
    {
        if (!is_array($blocks)) {
            $blocks = [$blocks];
        }

        $requestData = $this->_objectManager->get(
            \Magento\Backend\Helper\Data::class
        )->prepareFilterString(
            $this->getRequest()->getParam('filter')
        );
        $inputFilter = new \Zend_Filter_Input(
            ['from' => $this->_dateFilter, 'to' => $this->_dateFilter],
            [],
            $requestData
        );
        $requestData = $inputFilter->getUnescaped();
        $requestData['store_ids'] = $this->getRequest()->getParam('store_ids');
        $params = new \Magento\Framework\DataObject();

        foreach ($requestData as $key => $value) {
            if (!empty($value)) {
                $params->setData($key, $value);
            }
        }
        $this->setDefaultParams($params);
        foreach ($blocks as $blockChild) {
            if ($blockChild) {
                $blockChild->setPeriodType($params->getData('period_type'));
                $blockChild->setFilterData($params);
            }
        }

        return $this;
    }

    /**
     * Set default params
     *
     * @param \Magento\Framework\DataObject $reportParams
     */
    public function setDefaultParams($reportParams)
    {
        $timeTo = $this->_date->gmtDate('Y-m-d');
        $timeFrom = $this->_date->gmtDate('Y-m-d', strtotime("-1 months", strtotime($timeTo)));
        if (!$reportParams->getData('from')) {
            $reportParams->setData('from', $timeFrom);
        }

        if (!$reportParams->getData('to')) {
            $reportParams->setData('to', $timeTo);
        }
    }
}
