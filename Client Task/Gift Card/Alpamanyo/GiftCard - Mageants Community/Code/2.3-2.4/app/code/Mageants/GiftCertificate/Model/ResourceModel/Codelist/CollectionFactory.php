<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCertificate\Model\ResourceModel\Codelist;

/** 
 * codelist model collection Factory
 */
class CollectionFactory
{
    /**
     * Object Managet
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager = null;
    
    /**
     * Instance name
     *
     * @var \Mageants\GiftCertificate\Model\ResourceModel\codelist\Collection
     */
    protected $_instanceName = null;
    
    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Mageants\GiftCertificate\Model\ResourceModel\Codelist\Collection $instanceName
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = '\\Mageants\\GiftCertificate\\Model\\ResourceModel\\Codelist\\Collection')
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }

    /**
     * @return instance
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create($this->_instanceName, $data);
    }
}