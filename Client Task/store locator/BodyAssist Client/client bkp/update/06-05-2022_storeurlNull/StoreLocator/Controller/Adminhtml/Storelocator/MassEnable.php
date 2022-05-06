<?php
/**
 * @category Mageants StoreLocator
 * @package Mageants_StoreLocator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@Mageants.com>
 */
namespace Mageants\StoreLocator\Controller\Adminhtml\Storelocator;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Mageants\StoreLocator\Model\ResourceModel\ManageStore\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;
use Mageants\StoreLocator\Helper\Data;
use Magento\Framework\Mail\Template\TransportBuilder;
use Psr\Log\LoggerInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\App\Area;

/**
 * MassEnable for Store
 */
class MassEnable extends \Magento\Backend\App\Action
{
    /**
     * Filter
     *
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    public $filter;
    /**
     * store collection factory
     *
     * @var Mageants\StoreLocator\Model\ResourceModel\ManageStore\CollectionFactory
     */
    public $collectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context
     * @param \Magento\Ui\Component\MassAction\Filter
     * @param Mageants\StoreLocator\Model\ResourceModel\ManageStore\CollectionFactory
     */
    public $dataHelper;
    /**
     * Helper
     *
     * @var Mageants\StoreLocator\Helper\Data
     */
    public $logger;
    public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory, Data $dataHelper, TransportBuilder $transportBuilder, LoggerInterface $logger, StateInterface $inlineTranslation)
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->dataHelper = $dataHelper;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $logger;
        $this->inlineTranslation = $inlineTranslation;
        $this->messageManager = $context->getMessageManager();
        parent::__construct($context);
    }
    
    /**
     * perform execute method for massEnable
     *
     * @return $resultRedirect
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();
        $this->sendMassEnableEmail();
        
        foreach ($collection as $item) {
            $item->setData("sstatus", "Enabled");
        }
        $collection->save();
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been updated.', $collectionSize));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
    public function getEmail()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $emails =[];
        $i = 0;
        $EmailCollection = $collection->getData();
        if (!empty($EmailCollection)) {
            foreach ($EmailCollection as $CollectionValue) {
                foreach ($CollectionValue as $EmailKey => $Emailvalue) {
                    if ($CollectionValue['sstatus'] == 'Disabled') {
                        if ($EmailKey == 'email') {
                            $emails[$i]['email'] = $Emailvalue;
                            $emails[$i]['storename'] = $CollectionValue['sname'];
                            $emails[$i]['storeId'] = $CollectionValue['storeId'];
                            $i++;
                        }
                    }
                }
            }
        }
        return $emails;
    }
    public function sendMassEnableEmail()
    {
        $emails = $this->getEmail();
        $sender = $this->dataHelper->getConfigValue('StoreLocator/dealer/sender');
        foreach ($emails as $storeCollection) {
            $vars = [
                'storename' => "Your Store ".$storeCollection['storename']." is now Enable",
                'store' => $storeCollection['storeId']
            ];
            $transport = $this->transportBuilder
            ->setTemplateIdentifier('email_template')
            ->setTemplateOptions(
                [
                    'area' => Area::AREA_FRONTEND,
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID
                ]
            )->setTemplateVars($vars)
            ->setFrom($sender)
            ->addTo($storeCollection['email']);
            try {
                $transport->getTransport()->sendMessage();
            } catch (Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __("The mail functionality not working at the moment."));
            }
        }
        $this->inlineTranslation->resume();
        $this->dataHelper->cachePrograme();
        return $this;
    }
}
