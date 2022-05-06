<?php
/**
 * @category Mageants MaintenanceMode
 * @package Mageants_MaintenanceMode
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\MaintenanceMode\Observer;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\App\ViewInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\UrlInterface;
use Mageants\MaintenanceMode\Block\Redirect as BlockRedirect;
use Mageants\MaintenanceMode\Helper\Data as HelperData;
use Mageants\MaintenanceMode\Model\Config\Source\System\RedirectTo;

/**
 * Class Redirect
 * @package Mageants\MaintenanceMode\Observer
 */
class Redirect implements ObserverInterface
{
    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * @var Http
     */
    protected $_response;

    /**
     * @var TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var ViewInterface
     */
    protected $_view;

    /**
     * @var BlockRedirect
     */
    protected $_blockRedirect;

    /**
     * Redirect constructor.
     *
     * @param HelperData $helperData
     * @param Http $response
     * @param TimezoneInterface $localeDate
     * @param RequestInterface $request
     * @param UrlInterface $urlBuilder
     * @param ViewInterface $view
     * @param BlockRedirect $blockRedirect
     */
    public function __construct(
        HelperData $helperData,
        Http $response,
        TimezoneInterface $localeDate,
        RequestInterface $request,
        UrlInterface $urlBuilder,
        ViewInterface $view,
        BlockRedirect $blockRedirect
    ) {
        $this->_helperData    = $helperData;
        $this->_response      = $response;
        $this->_localeDate    = $localeDate;
        $this->_request       = $request;
        $this->_urlBuilder    = $urlBuilder;
        $this->_view          = $view;
        $this->_blockRedirect = $blockRedirect;
    }

    /**
     * @param Observer $observer
     *
     * @return bool|void
     */
    public function execute(Observer $observer)
    {
        $redirectTo = $this->_helperData->getConfigGeneral('redirect_to');
        $currentUrl = $this->_urlBuilder->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
        $currentIp  = $this->_helperData->getClientIp();
        $ctlName    = $this->_request->getControllerName();

        if (!$this->_helperData->isEnabled()) {
            return;
        }

        foreach ($this->_blockRedirect->getWhiteListIp() as $value) {
            if ($this->_helperData->checkIp($currentIp, trim($value))) {
                return;
            }
        }

        foreach ($this->_blockRedirect->getWhiteListPage() as $value) {
            if ($currentUrl === $value) {
                return;
            }
        }

        // if (strtotime($this->_localeDate->date()->format('m/d/Y H:i:s'))
        //     >= strtotime($this->_helperData->getConfigGeneral('end_time')) || strtotime($this->_helperData->getConfigGeneral('start_time')) >= strtotime($this->_helperData->getConfigGeneral('end_time'))
        //     ) {


        //     return;
        // }
          if (strtotime($this->_localeDate->date()->format('m/d/Y H:i:s'))
            >= strtotime($this->_helperData->getConfigGeneral('end_time'))) {
            return;
        }
        if (strtotime($this->_helperData->getConfigGeneral('start_time'))
            >= strtotime($this->_localeDate->date()->format('m/d/Y H:i:s'))) {
            return;
        }
        // if (strtotime($this->_helperData->getConfigGeneral('start_time'))
        //     >= strtotime($this->_localeDate->date()->format('m/d/Y H:i:s'))) {
        //     return;
        // }
        $home = $this->_request->getFullActionName();

        if ($home == 'cms_index_index') {

            $url = $this->_blockRedirect->getUrl($redirectTo);
            return $this->_response->setRedirect($url);

        }else{
            if ($home !== '_noroute_index' && !$this->_view->isLayoutLoaded()) {
                if ($redirectTo === RedirectTo::MAINTENANCE_PAGE && $ctlName !== 'preview' && $this->_request->getControllerName()) {
                    $this->_view->loadLayout(['default', 'maintenancemode_maintenance_index'], true, true, false);
                    $this->_response->setHttpResponseCode(503);
                }                
            }
        }

        $this->_request->setDispatched(true);

        return false;
    }
}
