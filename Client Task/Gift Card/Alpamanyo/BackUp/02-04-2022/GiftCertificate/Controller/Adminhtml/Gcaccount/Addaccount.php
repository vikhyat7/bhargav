<?php
/**
* Mageants_GiftCertificate Magento component
* @category    Mageants
* @package     Mageants_GiftCertificate
* @author     Mageants Team <support@mageants.com>
* @copyright   Mageants (http://www.mageants.com)
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/
namespace Mageants\GiftCertificate\Controller\Adminhtml\Gcaccount;
use Magento\Framework\Controller\ResultFactory;
/**
 * AddAccount controller
 */
class Addaccount extends \Magento\Backend\App\Action
{
    /**
     * For get Order Id
     *
     * @var \Magento\Backend\Model\Session
     */
    protected $_sessionId;

    /**
     * For Model account
     *
     * @var \Mageants\GiftCertificate\Model\Account
     */
    protected $modelAccount;
	
    /**
     * For Model Customer
     *
     * @var \Mageants\GiftCertificate\Model\Customer
     */
    protected $modelCustomer;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param \Magento\Backend\Model\Session $session
     * @param \Mageants\GiftCertificate\Model\Account $modelAccount
     * @param \Mageants\GiftCertificate\Model\Customer $modelCustomer
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Mageants\GiftCertificate\Model\Account $modelAccount,
        \Mageants\GiftCertificate\Model\Customer $modelCustomer
    ) 
    {
        parent::__construct($context);
        $this->modelAccount=$modelAccount;
        $this->modelCustomer=$modelCustomer;
        $this->_coreRegistry = $coreRegistry;
    }
	
    /**
     * Execute method for perform addAccount controller
     */ 
    public function execute()
    {
         $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $_sessionId=$objectManager->create("\Magento\Backend\Model\Session");    
        $accountid = (int) $this->getRequest()->getParam('account_id');
        $accountData = $this->modelAccount;
        if ($accountid) {
            $accountData = $accountData->load($accountid);
            $templateTitle = $accountData->getGiftCode();
                $customerData = $this->modelCustomer->load($accountData->getOrderId());
                    $accountData['recipient_name']=$customerData->getRecipientName();
                    $accountData['recipient_email']=$customerData->getRecipientEmail();
                    $_sessionId->setOrderId($customerData->getOrderId());
            if (!$accountData->getAccountId()) {
                $this->messageManager->addError(__('Template no longer exist.'));
                $this->_redirect('giftcertificate/gcaccounts/');
                return;
            }
        }
        
        $this->_coreRegistry->register('account_data', $accountData);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $title = $accountid ? __('Edit Account').$templateTitle : __('Add Account');
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }

}