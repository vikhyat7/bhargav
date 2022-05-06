<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Sales;

use Magestore\Webpos\Helper\Profiler;
use Magestore\Webpos\Model\Source\Adminhtml\Since;
use Magestore\Webpos\Api\Sales\OrderSearchRepositoryInterface;

/**
 * Model OrderSearchRepository
 */
class OrderSearchRepository extends OrderRepository implements OrderSearchRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function search(\Magestore\Webpos\Api\SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->getOrderCollection($searchCriteria);
        $searchResult = $this->searchResultFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setTotalCount($collection->getSize());
        $searchResult->setItems($collection->getItems());
        return $searchResult;
    }

    /**
     * Get Order Collection
     *
     * @param \Magestore\Webpos\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\Webpos\Model\ResourceModel\Sales\Order\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOrderCollection($searchCriteria)
    {
        Profiler::start('search');
        $collection = $this->collectionFactory->create();
        /** @var \Magestore\Webpos\Api\Data\Sales\OrderSearchResultInterface $searchResult */
        if ($searchCriteria->getCurrentPage()) {
            $collection->setCurPage($searchCriteria->getCurrentPage());
        }
        if ($searchCriteria->getPageSize()) {
            $collection->setPageSize($searchCriteria->getPageSize());
        }

        /**
         * @var \Magestore\Webpos\Model\Config\ConfigRepository $configRepository
         */
        $configRepository = $this->_objectManager->get(\Magestore\Webpos\Model\Config\ConfigRepository::class);
        $permission = $configRepository->getPermissions();
        $locationId = $this->helper->getCurrentLocationId();
        $this->orderHelper->applyPermissionForOrderCollect($permission, $collection, $locationId);

        $isLimit = $searchCriteria->getIsLimit();
        if ($searchCriteria->getIsHold()) {
            $collection->addFieldToFilter('main_table.state', \Magento\Sales\Model\Order::STATE_HOLDED);
            $collection->addFieldToFilter('main_table.pos_location_id', $locationId);
        } else {
            if ($isLimit) {
                $lastTime = $this->getSearchDays();
                $collection->addFieldToFilter('main_table.created_at', ['gteq' => $lastTime]);
            }
            $collection->addFieldToFilter('main_table.state', ['nin' => \Magento\Sales\Model\Order::STATE_HOLDED]);
        }

        $this->_objectManager->get(\Magento\Framework\Event\ManagerInterface::class)
            ->dispatch('webpos_order_collection_load_before', ['collection' => $collection]);

        if ($searchCriteria->getQueryString()) {
            $queryString = '%' . $searchCriteria->getQueryString() . '%';
            $collection->joinToGetSearchString($queryString);
        }
        $collection->addOrder('main_table.created_at', 'DESC');
        return $collection;
    }

    /**
     * Get period search from config
     *
     * @return string
     */
    public function getSearchDays()
    {
        $config = $this->helper->getStoreConfig('webpos/offline/order_since');
        $time = time();
        switch ($config) {
            case Since::SINCE_24H:
                $lastTime = $time - 60 * 60 * 24 * 1;
                return date('Y-m-d H:i:s', $lastTime);
            case Since::SINCE_7DAYS:
                $lastTime = $time - 60 * 60 * 24 * 7;
                return date('Y-m-d H:i:s', $lastTime);
            case Since::SINCE_MONTH:
                return date('Y-m-01 00:00:00');
            case Since::SINCE_YTD:
                return date('Y-01-01 00:00:00');
            case Since::SINCE_2YTD:
                $year = date("Y") - 1;
                return date($year . '-01-01 00:00:00');
            default:
                $lastTime = $time - 60 * 60 * 24 * 7;
                return date('Y-m-d H:i:s', $lastTime);
        }
    }
}
