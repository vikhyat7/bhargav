<?php
namespace Mageants\Orderattachment\Mail\Template;

class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{   
    public function addAttachment($content, $file_name, $fileType)
	{
        $this->message->createAttachment(
        	$content,
        	$fileType,
            \Zend_Mime::DISPOSITION_ATTACHMENT,
            \Zend_Mime::ENCODING_BASE64,
        	$file_name
    	);
    	return $this;
	}
}