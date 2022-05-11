<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * class \Magestore\Webpos\Ui\Component\Listing\Column\LocationActions
 *
 * Web POS User Actions
 * Methods:
 *  prepareDataSource
 *
 * @category    Magestore
 * @package     Magestore\Webpos\Ui\Component\Listing\Column
 * @module      Webpos
 * @author      Magestore Developer
 */
class LocationActions extends Column
{

    /** @var UrlInterface */
    protected $_urlBuilder;

    /**
     * @var string
     */
    private $_editUrl;

    /**
     * @var string
     */
    private $_deleteUrl;

    /**
     * LocationActions constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->_urlBuilder = $urlBuilder;
        $this->_editUrl = $data['userUrlPathEdit'];
        $this->_deleteUrl = $data['userUrlPathDelete'];
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                $item[$name]['edit'] = [
                    'href' => $this->_urlBuilder->getUrl($this->_editUrl, ['id' => $item['location_id']]),
                    'label' => __('Edit')
                ];
//                $item[$name]['delete'] = [
//                    'href' => $this->_urlBuilder->getUrl($this->_deleteUrl, ['id' => $item['location_id']]),
//                    'label' => __('Delete')
//                ];
            }
        }
        return $dataSource;
    }

}
