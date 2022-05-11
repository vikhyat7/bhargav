<?php
/**
 * @category Mageants MaintenanceMode
 * @package Mageants_MaintenanceMode
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\MaintenanceMode\Block;

use Magento\Cms\Model\Page as CmsPage;
use Magento\Framework\App\Response\Http;
use Magento\Framework\App\Response\HttpInterface;
use Magento\Framework\View\Element\Template;
use Mageants\MaintenanceMode\Helper\Data as HelperData;
use Mageants\MaintenanceMode\Model\Config\Source\System\RedirectTo;

/**
 * Class Redirect
 *
 * @package Mageants\MaintenanceMode\Block
 */
class Redirect extends Template
{
    protected $_template = 'Mageants_MaintenanceMode::redirect.phtml';

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * @var CmsPage
     */
    protected $_cmsPage;

    /**
     * @var Http
     */
    protected $_response;

    /**
     * Redirect constructor.
     *
     * @param Template\Context $context
     * @param HelperData $helperData
     * @param CmsPage $cmsPage
     * @param Http $response
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        HelperData $helperData,
        CmsPage $cmsPage,
        Http $response,
        array $data = []
    ) {
        $this->_helperData = $helperData;
        $this->_cmsPage    = $cmsPage;
        $this->_response   = $response;

        parent::__construct($context, $data);
    }

    /**
     * @return array[]|false|string[]
     */
    public function getWhiteListPage()
    {
        return preg_split("/(\r\n|\n|\r)/", $this->_helperData->getWhitelistPage());
    }

    /**
     * @return array
     */
    public function getWhiteListIp()
    {
        return explode(',', $this->_helperData->getWhitelistIp());
    }

    /**
     * @return bool|Http|HttpInterface
     */
    public function redirectToUrl()
    {
        if (!$this->_helperData->isEnabled()) {
            return false;
        }

        if (strtotime($this->_localeDate->date()->format('m/d/Y H:i:s'))
            >= strtotime($this->_helperData->getEndTime())) {
            return false;
        }

        $this->_response->setNoCacheHeaders();
        $redirectTo = $this->_helperData->getConfigGeneral('redirect_to');
        $currentUrl = $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
        $currentIp  = $this->_helperData->getClientIp();

        foreach ($this->getWhitelistIp() as $value) {
            if ($this->_helperData->checkIp($currentIp, trim($value))) {
                return false;
            }
        }

        foreach ($this->getWhiteListPage() as $value) {
            if ($currentUrl === $value) {
                return false;
            }
        }

        // if ($redirectTo === RedirectTo::MAINTENANCE_PAGE || $redirectTo === RedirectTo::COMING_SOON_PAGE) {
        if ($redirectTo === RedirectTo::MAINTENANCE_PAGE) {
            return false;
        }

        $route = $redirectTo;

        if ($this->_cmsPage->getIdentifier() === $redirectTo) {
            return false;
        }

        $url = $this->getUrl($route);

        return $this->_response->setRedirect($url);
    }
}
