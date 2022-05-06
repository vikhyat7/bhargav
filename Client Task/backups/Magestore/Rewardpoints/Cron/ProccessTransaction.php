<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
namespace Magestore\Rewardpoints\Cron;

/**
 * RewardPoints Running Cron to process transactions
 */
class ProccessTransaction
{
    protected $_customerFactory;
    protected $_rewardCustomerFactory;
    protected $_transaction;
    protected $_helper;
    protected $_storeManager;
    protected $_logger;

    /**
     * ProccessTransaction constructor.
     *
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magestore\Rewardpoints\Model\CustomerFactory $rewardCustomerFactory
     * @param \Magestore\Rewardpoints\Model\TransactionFactory $transaction
     * @param \Magestore\Rewardpoints\Helper\Data $helper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     * @param \Magento\Framework\Logger\Monolog $monolog
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magestore\Rewardpoints\Model\CustomerFactory $rewardCustomerFactory,
        \Magestore\Rewardpoints\Model\TransactionFactory $transaction,
        \Magestore\Rewardpoints\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Framework\Logger\Monolog $monolog
    ) {
        $this->_storeManager = $storeManagerInterface;
        $this->_customerFactory = $customerFactory;
        $this->_rewardCustomerFactory = $rewardCustomerFactory;
        $this->_transaction = $transaction;
        $this->_helper = $helper;
        $this->_logger = $monolog;
    }
    /**
     * Process transactions (holding, expire) by cron
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        \Magento\Framework\Profiler::start('REWARDPOINTS_CRON::processTransactions');
        $stores     = [];
        $allStores  = true;

        foreach ($this->_storeManager->getStores(true) as $_store) {
            if ($this->_helper->getConfig(\Magestore\Rewardpoints\Helper\Data::XML_PATH_ENABLE, $_store)) {
                $stores[$_store->getId()] = $_store->getId();
            } else {
                $allStores = false;
            }
        }
        // complete holding transactions
        $holdingDays = [];
        foreach ($stores as $_store) {
            $_holdDays = (int)$this->_helper->getConfig(
                \Magestore\Rewardpoints\Helper\Calculation\Earning::XML_PATH_HOLDING_DAYS,
                $_store
            );
            $_holdDays = max(0, $_holdDays);
            $holdingDays[$_holdDays][$_store] = $_store;
        }
        if ($allStores && count($holdingDays) == 1) { // all stores
            reset($holdingDays);
            $_holdDays = key($holdingDays);
            $this->completeHoldingTransaction($_holdDays);
        } else { // each group stores
            foreach ($holdingDays as $_holdDays => $_storeIds) {
                $this->completeHoldingTransaction($_holdDays, $_storeIds);
            }
        }
        
        // expire transactions
        \Magento\Framework\Profiler::start('REWARDPOINTS_CRON::expireTransactions');
        
        $expireTrans = $this->_transaction->create()->getCollection()
            ->addFieldToFilter('status', ['lteq' => \Magestore\Rewardpoints\Model\Transaction::STATUS_COMPLETED])
            ->addFieldToFilter('expiration_date', ['to' => date('Y-m-d H:i:s')])
            ->addFieldToFilter('expiration_date', ['notnull' => true]);
        $expireTrans->getSelect()->where('point_amount > point_used');
        
        if (count($expireTrans)) {
            $rewardAccount  = $this->_rewardCustomerFactory->create();
            $customer       = $this->_customerFactory->create();
            foreach ($expireTrans as $_transaction) {
                try {
                    $_transaction->setData('reward_account', $rewardAccount->load($_transaction->getRewardId()));
                    $_transaction->setData('customer', $customer->load($_transaction->getCustomerId()));
                    $_transaction->expireTransaction();
                } catch (\Exception $e) {
                    $this->_logger->critical($e);
                    //Mage::logException($e);
                }
            }
        }
        
        unset($expireTrans);
        \Magento\Framework\Profiler::stop('REWARDPOINTS_CRON::expireTransactions');
        
        // send before expire email to customer
        $beforeDays = [];
        foreach ($stores as $_store) {
            if (!$this->_helper->getConfig(\Magestore\Rewardpoints\Model\Transaction::XML_PATH_EMAIL_ENABLE, $_store)) {
                $allStores = false;
                continue;
            }
            $_beforeDays = (int)$this->_helper->getConfig(
                \Magestore\Rewardpoints\Model\Transaction::XML_PATH_EMAIL_EXPIRE_DAYS,
                $_store
            );
            if ($_beforeDays <= 0) {
                $allStores = false;
            } else {
                $beforeDays[$_beforeDays][$_store] = $_store;
            }
        }
        if ($allStores && count($beforeDays) == 1) { // all stores
            reset($beforeDays);
            $_beforeDays = key($beforeDays);
            $this->sendBeforeExpireEmail($_beforeDays);
        } elseif (count($beforeDays)) { // each group stores
            foreach ($beforeDays as $_beforeDays => $_storeIds) {
                $this->sendBeforeExpireEmail($_beforeDays, $_storeIds);
            }
        }

        \Magento\Framework\Profiler::stop('REWARDPOINTS_CRON::processTransactions');
    }
    
    /**
     * Send email to customer before transaction is expired
     *
     * @param int $beforeDays
     * @param null|array $storeIds
     */
    public function sendBeforeExpireEmail($beforeDays, $storeIds = null)
    {
        \Magento\Framework\Profiler::start('REWARDPOINTS_CRON::sendBeforeExpireEmail');
        $futureTime   = date('Y-m-d H:i:s', time() + $beforeDays * 86400);
        $nowTime      = date('Y-m-d H:i:s', time() + 86400);
        $transactions = $this->_transaction->create()->getCollection()
            ->addFieldToFilter('status', \Magestore\Rewardpoints\Model\Transaction::STATUS_COMPLETED)
            ->addFieldToFilter('expiration_date', ['from' => $nowTime])
            ->addFieldToFilter('expiration_date', ['to' => $futureTime])
            ->addFieldToFilter('expire_email', '0');
        $transactions->getSelect()->where('point_amount > point_used');
        if (is_array($storeIds) && count($storeIds)) {
            $transactions->addFieldToFilter('store_id', ['in' => $storeIds]);
        }
        if (!count($transactions)) {
            \Magento\Framework\Profiler::stop('REWARDPOINTS_CRON::sendBeforeExpireEmail');
            return ;
        }
        $rewardAccount  = $this->_rewardCustomerFactory->create();
        $customer       = $this->_customerFactory->create();
        $transIds       = [];
        foreach ($transactions as $transaction) {
            try {
                $transaction->setData('reward_account', $rewardAccount->load($transaction->getRewardId()));
                $transaction->setData('customer', $customer->load($transaction->getCustomerId()));
                $transaction->sendBeforeExpireEmail();
                $transIds[] = $transaction->getId();
            } catch (\Exception $e) {
                $this->_logger->critical($e);
            }
        }
        if (count($transIds)) {
            try {
                $this->_transaction->create()->getResource()->increaseExpireEmail($transIds);
            } catch (\Exception $e) {
                $this->_logger->critical($e);
            }
        }
        \Magento\Framework\Profiler::stop('REWARDPOINTS_CRON::sendBeforeExpireEmail');
    }
    
    /**
     * Complete holding transaction for group store
     *
     * @param int $holdDays
     * @param null|array $storeIds
     */
    public function completeHoldingTransaction($holdDays, $storeIds = null)
    {
        \Magento\Framework\Profiler::start('REWARDPOINTS_CRON::completeHoldingTransaction');
        $releaseTime  = date('Y-m-d H:i:s', time() - $holdDays * 86400);
        $holdingTrans = $this->_transaction->create()->getCollection()
            ->addFieldToFilter('status', \Magestore\Rewardpoints\Model\Transaction::STATUS_ON_HOLD)
            ->addFieldToFilter('created_time', ['to' => $releaseTime]);
        if (is_array($storeIds) && count($storeIds)) {
            $holdingTrans->addFieldToFilter('store_id', ['in' => $storeIds]);
        }
        if (!count($holdingTrans)) {
            \Magento\Framework\Profiler::stop('REWARDPOINTS_CRON::completeHoldingTransaction');
            return ;
        }
        $rewardAccount  = $this->_rewardCustomerFactory->create();
        $customer       = $this->_customerFactory->create();
        foreach ($holdingTrans as $transaction) {
            try {
                $transaction->setData('reward_account', $rewardAccount->load($transaction->getRewardId()));
                $transaction->setData('customer', $customer->load($transaction->getCustomerId()));
                $transaction->completeTransaction();
            } catch (\Exception $e) {
                $this->_logger->critical($e);
            }
        }
        \Magento\Framework\Profiler::stop('REWARDPOINTS_CRON::completeHoldingTransaction');
    }
}
