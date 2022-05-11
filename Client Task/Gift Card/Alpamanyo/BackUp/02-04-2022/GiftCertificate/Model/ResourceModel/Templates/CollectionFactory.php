<?php
/**
* Mageants_GiftCertificate Magento component
*
* @category    Mageants
* @package     Mageants_GiftCertificate
* @author     Mageants Team <support@mageants.com>
* @copyright   Mageants (http://www.mageants.com)
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/
namespace Mageants\GiftCertificate\Model\ResourceModel\Templates;
/** 
 * Templates model collection Factory
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
     * @var \Mageants\GiftCertificate\Model\ResourceModel\Templates\Collection
     */
    protected $_instanceName = null;
    
    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Mageants\GiftCertificate\Model\ResourceModel\Templates\Collection $instanceName
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = '\\Mageants\\GiftCertificate\\Model\\ResourceModel\\Templates\\Collection')
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