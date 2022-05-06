<?php
/**
 * @category Mageants Richsnippets
 * @package Mageants_Richsnippets
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\Richsnippets\Block;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\UrlInterface;
use Magento\Catalog\Helper\Data;
use Magento\Framework\View\Page\Config;

class Richsnippets extends \Magento\Framework\View\Element\Template
{
    protected $catalogData;
    protected $ratingFactory;
    protected $mostViewedCollection;

    public function __construct(
        Context $context,
        Data $catalogData,
        \Magento\Framework\Registry $registry,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Review\Model\Rating $ratingFactory,
        \Magento\Customer\Model\Session $session,
        \Magento\Directory\Model\Currency $currency,
        \Mageants\Richsnippets\Helper\Data $richsnippethelper,
        \Magento\Catalog\Model\ProductFactory $productfactory,
        \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $mostViewedCollectionFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory
    ) {
        $this->_storeManager = $context->getStoreManager();
        $this->session = $session;
        $this->_urlInterface = $context->getUrlBuilder();
        $this->registry = $registry;
        $this->catalogData = $catalogData;
        $this->reviewFactory = $reviewFactory;
        $this->ratingFactory = $ratingFactory;
        $this->currency = $currency;
        $this->richsnippethelper = $richsnippethelper;
        $this->productfactory = $productfactory;
        $this->metainformation = $context->getPageConfig();
        $this->mostViewedCollection = $mostViewedCollectionFactory->create();
        $this->categoryFactory = $categoryFactory;
        parent::__construct($context);
    }
    public function getConfigrations()
    {
        return $this->richsnippethelper;
    }
    public function getPropductCollection()
    {
        return $this->registry->registry('current_product');
    }
    public function getPropductDescription()
    {
        if ($this->getConfigrations()->getDescriptionType() == 1) {
            $id = $this->getPropductCollection()->getId();
            $product = $this->productfactory->create()->load($id);
            $description = preg_replace('#<[^>]+>#', ' ', $product->getShortDescription());
        } elseif ($this->getConfigrations()->getDescriptionType() == 2) {
            $id = $this->getPropductCollection()->getId();
            $product = $this->productfactory->create()->load($id);
            $description = preg_replace('#<[^>]+>#', ' ', $product->getDescription());
        } elseif ($this->getConfigrations()->getDescriptionType() == 3) {
            $description = $this->metainformation->getDescription();
        } else {
            $description = '';
        }
        $limit = $this->getConfigrations()->getOrganizationDescLength();
        if (strlen($description) > $limit) {
            $description = substr($description, 0, strrpos(substr($description, 0, $limit), ' ')) . '.';
        }
        return $description;
    }
    public function getCategoryPropductDescription($id)
    {
        if ($this->getConfigrations()->getDescriptionType() == 1) {
            $product = $this->productfactory->create()->load($id);
            $description = preg_replace('#<[^>]+>#', ' ', $product->getShortDescription());
        } elseif ($this->getConfigrations()->getDescriptionType() == 2) {
            $product = $this->productfactory->create()->load($id);
            $description = preg_replace('#<[^>]+>#', ' ', $product->getDescription());
        } elseif ($this->getConfigrations()->getDescriptionType() == 3) {
            $description = $this->metainformation->getDescription();
        } else {
            $description = '';
        }
        $limit = $this->getConfigrations()->getOrganizationDescLength();
        if (strlen($description) > $limit) {
            $description = substr($description, 0, strrpos(substr($description, 0, $limit), ' ')) . '.';
        }
        return $description;
    }
    public function getProductStatus()
    {
        return $this->getPropductCollection()->isInStock();
    }
    public function getMostViewed()
    {
        $storeId = $this->_storeManager->getStore()->getId();
        return $this->mostViewedCollection;
    }
    public function getCrumbs($product, $currentproduct)
    {
        $evercrumbs = [];
        $getlastcrums = [];
        if ($this->getConfigrations()->isSiteIncludeInSearch()) {
            $evercrumbs[] = [
                'label' => $this->getConfigrations()->getWebsiteName(),
                'title' => 'Go to Main Page',
                'link' => $this->_storeManager->getStore()->getBaseUrl()
            ];
        } else {
            $evercrumbs[] = [
                'label' => $this->_storeManager->getStore()->getBaseUrl(),
                'title' => 'Go to Main Page',
                'link' => $this->_storeManager->getStore()->getBaseUrl()
            ];
        }
        if ($this->getConfigrations()->getCategoryPath()) {
            $mostviewd = [];
            $mostviewd = $this->getMostViewed()->addFieldToSelect('views')->getData();
            $productId = $mostviewd[0]['entity_id'];
            $path = $this->catalogData->getBreadcrumbPath();
            $product->load($productId);
            $categoryCollection = clone $product->getCategoryCollection();
            $categoryCollection->clear();
            $categoryCollection->addAttributeToSort('level', $categoryCollection::SORT_ORDER_DESC)->addAttributeToFilter('path', ['like' => "1/" . $this->_storeManager->getStore()->getRootCategoryId() . "/%"]);
            $categoryCollection->setPageSize(1);
            $breadcrumbCategories = $categoryCollection->getFirstItem()->getParentCategories();
            if ($this->getConfigrations()->getCategoryType()) {
                foreach ($breadcrumbCategories as $category) {
                    $evercrumbs[] = [
                            'label' => $category->getName(),
                            'title' => $category->getName(),
                            'link' => $category->getUrl()
                        ];
                }
            }
        } else {
            if (!empty($product->getAvailableInCategories())) {
                $path = $this->catalogData->getBreadcrumbPath();
                $categoryCollection = clone $product->getCategoryCollection();
                $categoryCollection->clear();
                $categoryCollection->addAttributeToSort('level', $categoryCollection::SORT_ORDER_DESC)->addAttributeToFilter('path', ['like' => "1/" . $this->_storeManager->getStore()->getRootCategoryId() . "/%"]);
                $categoryCollection->setPageSize(1);
                $breadcrumbCategories = $categoryCollection->getFirstItem()->getParentCategories();
                if ($this->getConfigrations()->getCategoryType()) {
                    foreach ($breadcrumbCategories as $category) {
                        $evercrumbs[] = [
                            'label' => $category->getName(),
                            'title' => $category->getName(),
                            'link' => $category->getUrl()
                        ];
                    }
                }
            }
        }
        $product->load($currentproduct);
        $evercrumbs[] = [
                'label' => $product->getName(),
                'title' => $product->getName(),
                'link' => $this->_storeManager->getStore()->getCurrentUrl()
            ];
        return $evercrumbs;
    }
    public function getRatingSummary($product)
    {
        $this->reviewFactory->create()->getEntitySummary($product, $this->_storeManager->getStore()->getId());
        $ratingSummary = $product->getRatingSummary()->getRatingSummary();
        return $ratingSummary;
    }
    public function getTotalReviews($product)
    {
        $product_id = $product->getId();
        /*$_ratingSummary = $this->ratingFactory->getEntitySummary($product_id);*/
        $ratingCollection = $this->reviewFactory->create()->getResourceCollection()->addStoreFilter(
            $this->_storeManager->getStore()->getId()
        )->addStatusFilter(\Magento\Review\Model\Review::STATUS_APPROVED)->addEntityFilter('product', $product_id);
        $review_count = count($ratingCollection);
        return $review_count;
    }
    public function getCurrencySymbol()
    {
        return $this->currency->getCurrencySymbol();
    }
    public function getCurrencyCode()
    {
        return $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
    }
}
