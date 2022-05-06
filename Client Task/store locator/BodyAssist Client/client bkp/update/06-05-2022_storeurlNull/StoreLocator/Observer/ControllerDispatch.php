<?php
     
namespace Mageants\StoreLocator\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ControllerDispatch implements ObserverInterface
{
    /**
     * @param \Magento\Framework\App\Request\Http $request,
     */
    protected $request;
    
    /**
     * @param \Mageants\StoreLocator\Helper\Data $helper
     */
    protected $_helper;

    /**
     * @param \Magento\Framework\App\Request\Http $request,
     * @param \Mageants\StoreLocator\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Mageants\StoreLocator\Helper\Data $helper
    ) {
        $this->request = $request;
        $this->_helper = $helper;
    }
    /**
     * Below is the method that will fire whenever the event runs!
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $this->_helper->cachePrograme();
    }
}
