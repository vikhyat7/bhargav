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
namespace Mageants\GiftCertificate\Controller\Account;
use Magento\Framework\View\Result\PageFactory;
/**
 * Account index controller
 */
class Index extends \Magento\Framework\App\Action\Action
{

	/**
	 * load layout
	 */
	public function execute(){
		$this->_view->loadLayout();
		$this->_view->renderLayout();
	}
}