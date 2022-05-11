<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCertificate\Controller\Adminhtml\Index;
use Magento\Framework\Controller\ResultFactory;
use  Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\RawFactory;

/**
 * CodeList for code
 */
class Codelist extends \Magento\Backend\App\Action
{
	/**
     * result page factory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
	protected $_resultPageFactory;
	
	/**
     * resulkt raw factory
     *
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
	protected $resultRawFactory;
	
	/**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     */
	public function __construct
	(\Magento\Backend\App\Action\Context $context,PageFactory $resultPageFactory,  RawFactory $resultRawFactory)
	{
    	 $this->_resultPageFactory = $resultPageFactory;
    	 $this->resultRawFactory = $resultRawFactory;
    	parent::__construct($context);          
	}

	/**
     * Perform CodeList Action
     */
	public function execute()
	{
		 $resultPage = $this->_resultPageFactory->create();
		 $result = $resultPage->getLayout()->renderElement('content');
		return $this->resultRawFactory->create()->setContents($result);
			      
	}
}