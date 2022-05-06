<?php
/**
 * @category Mageants MaintenanceMode
 * @package Mageants_MaintenanceMode
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\MaintenanceMode\Model\Config\Source\System;

use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as PageFactory;
use Magento\Framework\Option\ArrayInterface;

/**
 * Class RedirectTo
 *
 * @package Mageants\MaintenanceMode\Model\Config\Source\System
 */
class RedirectTo implements ArrayInterface
{
    const MAINTENANCE_PAGE = 'maintenance_page';

    /**
     * @var PageFactory
     */
    protected $_pageFactory;

    /**
     * RedirectTo constructor.
     *
     * @param PageFactory $pageFactory
     */
    public function __construct(PageFactory $pageFactory)
    {
        $this->_pageFactory = $pageFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $maintenance = [
            'value' => self::MAINTENANCE_PAGE,
            'label' => __('Maintenance Page')
        ];

        $pageList   = $this->_pageFactory->create()->toOptionIdArray();
        $pageList[] = $maintenance;        

        return $pageList;
    }
}
