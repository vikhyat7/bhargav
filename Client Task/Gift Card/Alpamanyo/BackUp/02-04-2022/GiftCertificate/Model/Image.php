<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCertificate\Model;
use Magento\Framework\UrlInterface;
/**
 * Image class
 */
class Image
{
    /**
     * sub Directory
     *
     * @var String
     */
    protected $subDir = 'templates/'; 
    
    /**
     * Url builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;
    
    /**
     * @param \Magento\Framework\UrlInterface
     */
    public function __construct(
        UrlInterface $urlBuilder
    )
    {
        $this->urlBuilder = $urlBuilder;
    }
    
    /**
     * @return $this
     */
    public function getBaseUrl()
    {
        return $this->urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]).$this->subDir;
    }
}
