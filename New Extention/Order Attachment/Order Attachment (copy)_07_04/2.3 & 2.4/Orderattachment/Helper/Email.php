<?php
namespace Mageants\Orderattachment\Helper;
use Mageants\Orderattachment\Helper\Data;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;
// use Mageants\Orderattachment\Model\Mail\Template\TransportBuilder;
use Magento\Framework\Controller\ResultFactory;


class Email extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $inlineTranslation;
    protected $escaper;
    protected $transportBuilder;
    protected $logger;
    protected $storeManager;
    protected $helperData;
    protected $directory_list;



    public function __construct(
        Context $context,
        StateInterface $inlineTranslation,
        Escaper $escaper,
        Data $helperData,
        TransportBuilder $transportBuilder,
        \Magento\Framework\App\Filesystem\DirectoryList $directory_list,

        \Magento\Store\Model\StoreManagerInterface $storeManager

    ) {
        parent::__construct($context);
        $this->inlineTranslation = $inlineTranslation;
        $this->escaper = $escaper;
        $this->helperData = $helperData;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $context->getLogger();
        $this->storeManager = $storeManager;
        $this->directory_list = $directory_list;

    }
 public function sendEmail($orderId,$name,$emailVar,$EmailAttachmentData)
    {
        $this->inlineTranslation->suspend();

        try {

            foreach($EmailAttachmentData as $AttachmentData){
             
                $file_name = $AttachmentData;
                $new1 = explode('/',$file_name);
                $file_name = $new1[3];
                $pdfFile = $this->directory_list->getPath("media")."/orderattachment/".$AttachmentData;
                $filetype = mime_content_type($pdfFile);
                
                $transport = $this->transportBuilder
                 ->addAttachment(file_get_contents($pdfFile),$file_name,$filetype);
               
             }


            $order_url1 = $this->storeManager->getStore()
                ->getBaseUrl()."sales/order/view/order_id/".$orderId."/";

            $transport = $this->transportBuilder
                ->setTemplateIdentifier('email_demo_template')
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars([
                    'subject' => 'Order Attachments For Order #'.$orderId,
                    'messageVar' => 'admin has added/updated new product attachment with following details',
                    'orderIdVar'  => $orderId,
                    'nameVar' => $name,
                    'emailVar' => $emailVar,
                    'orderLink' => $order_url1,
                    'store' => $this->storeManager->getStore(),

                ])
                ->setFrom($this->helperData->EmailSender())
                ->addTo($emailVar)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    
    }
   
}
