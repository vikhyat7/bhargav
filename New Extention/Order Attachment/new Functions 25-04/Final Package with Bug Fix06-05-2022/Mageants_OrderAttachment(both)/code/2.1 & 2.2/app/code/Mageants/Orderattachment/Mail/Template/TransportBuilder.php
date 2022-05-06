<?php
/**
 * @category Mageants_Orderattachment
 * @package Mageants_Orderattachment
 * @copyright Copyright (c) 2022 Mageants
 * @author Mageants Team <support@mageants.com>
 */
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
