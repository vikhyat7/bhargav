<?php
/**
* Mageants_GiftCertificate Magento component
* @category    Mageants
* @package     Mageants_GiftCertificate
* @author     Mageants Team <support@mageants.com>
* @copyright  Mageants (http://www.mageants.com)
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/
namespace Mageants\GiftCertificate\Controller\Adminhtml\Gcaccount;
use Magento\Backend\App\Action\Context;

/**
 * Delete Account controller
 */
class Delete extends \Magento\Backend\App\Action
{
	/**
     * For Model account
     *
     * @var \Mageants\GiftCertificate\Model\Account
     */
    protected $modelAccount;

	/**
	 * @param \Magento\Backend\Block\Template\Context $context
	 * @param \Mageants\GiftCertificate\Model\Account $modelAccount
	 */
    public function __construct(Context $context,\Mageants\GiftCertificate\Model\Account $modelAccount)
    {
        parent::__construct($context);
        $this->modelAccount=$modelAccount;
    }

    /**
     * DeleteAccount controller
     */ 
   	public function execute()
	{
		
		if($this->getRequest()->getParam('account_id')!=''):
			$id = $this->getRequest()->getParam('account_id');
	        $resultRedirect = $this->resultRedirectFactory->create();
			$row = $this->modelAccount->load($id);
			$gift = $row->getGiftCode();
			$row->delete();
			$this->messageManager->addSuccess(__('A total of %1 have been deleted.', $gift));
			$this->_redirect('giftcertificate/gcaccount/');
		endif;
		
	}
}

