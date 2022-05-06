<?php
namespace Mageants\Orderattachment\Controller\Attachment;
use Magento\Framework\App\RequestInterface;

class Upload extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Mageants\Orderattachment\Helper\Attachment
     */
    protected $attachmentHelper;
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Mageants\Orderattachment\Helper\Attachment $attachmentHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Mageants\Orderattachment\Helper\Data $helperData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Mageants\Orderattachment\Helper\Attachment $attachmentHelper
    ) {
        parent::__construct($context);
        $this->_request = $request;
        $this->_transportBuilder = $transportBuilder;
        $this->_storeManager = $storeManager;
        $this->helperData = $helperData;
        $this->resultRawFactory = $resultRawFactory;
        $this->attachmentHelper = $attachmentHelper;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $result = $this->attachmentHelper->saveAttachment($this->getRequest());
        
        // $name = $this->getRequest()->getParam('name');
        // $email = $this->getRequest()->getParam('email');
        // $message = $this->getRequest()->getParam('message');
        // $store = $this->_storeManager->getStore()->getId();
        // $transport = $this->_transportBuilder->setTemplateIdentifier('refer_email_template')
        //     ->setTemplateOptions(['area' => 'frontend', 'store' => $store])
        //     ->setTemplateVars(
        //         [
        //             'store' => $this->_storeManager->getStore(),
        //         ]
        //     )
        //     ->setFrom($this->helperData->EmailSender())
        //     // you can config general email address in Store -> Configuration -> General -> Store Email Addresses
        //     ->addTo($email[0], $name[0])
        //     ->getTransport();
        // $transport->sendMessage();

        /** @var \Magento\Framework\Controller\Result\Raw $response */
        $response = $this->resultRawFactory->create()
            ->setHeader('Content-type', 'text/plain')
            ->setContents(json_encode($result));

        return $response;
    }
}
