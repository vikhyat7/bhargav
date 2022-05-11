<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
 
namespace Mageants\GiftCertificate\Block\Adminhtml\Template\Edit\Renderer;
/**
*  Template field renderer
*/
class Template extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    /**
    * Get the after element html.
    *
    * @return mixed
    */
    public function getAfterElementHtml()
    {
        // here you can write your code.
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $UrlInterface = $objectManager->get('\Magento\Framework\UrlInterface');
        $http = $objectManager->get('\Magento\Framework\App\Request\Http');
        $FormKey = $objectManager->get('\Magento\Framework\Data\Form\FormKey'); 
        $msg = 'Message';
        $left=''; $top='';
        $bgcolor="#f00";
        $color="#fff";
        $bgImage='';
        if($http->getParam('image_id')){
          $tempDataObject = $objectManager->get('\Mageants\GiftCertificate\Model\Templates');
          $tempData = $tempDataObject->load($http->getParam('image_id'));
          $msg = $tempData->getMessage();
          $left = $tempData->getPositionleft();
          $top =  $tempData->getPositiontop();
          $bgcolor = $tempData->getColor();
          $color = $tempData->getForecolor();
          $bgImage = $tempData->getImage();
        }
        if($bgImage!==''){
            $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
            $mediapath = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
            $bgImage = $mediapath.$bgImage;
        }
        if($left!==''){
          $left = $left.'px';

        }
        else{
          $left = '0px';
        }
        if($top !== '' ){
          $top = $top.'px';
        }
        else{
          $top='0px';
        }
        
        $customDiv = '
        <input type="hidden" id="uploadurl" value='.$UrlInterface->getUrl("test/test/index").'/>
        <div style="width:600px; position:relative; height:200px;margin:10px 0;border:2px solid #000;  background-image:url('.$bgImage.'); background-size:cover;" id="customdiv" ondrop="drop(event)" ondragover="allowDrop(event)">
           <div class="draggable" style="bottom:0px; width:100%">'.$msg.'</div>
        </div>
         

        <script>
            
    require(["jquery"],function($){
        
          

        jQuery("#rock_message").keyup(function(){
          jQuery(".draggable").text(jQuery(this).val());
        });
        jQuery("#rock_color").change(function(){
           jQuery(".draggable").css("background", jQuery(this).val());
        });
        jQuery("#rock_forecolor").change(function(){
           jQuery(".draggable").css("color", jQuery(this).val());
        });
        jQuery("#rock_image").change(function(){
            var file_data = jQuery("#rock_image").prop("files")[0]; 
          var form_data = new FormData();                  
          form_data.append("file", file_data);
          form_data.append("form_key","'.$FormKey->getFormKey().'");              
              $.ajax({
              url: "'.$UrlInterface->getUrl("giftcertificate/gcimages/upload").'", 
              type: "POST",            
              data: form_data,
              contentType: false,      
              cache: false,            
              processData:false,       
              success: function(data)   
              {
                  
                  var img  = data["path"] +  data["file"];
                    jQuery("#customdiv").css("background-image", "url("+ img +")");
                 
                 jQuery("#customdiv").css("background-size", "cover");
                 jQuery("#rock_image_title_upoad").val(data["file"]);

              }
          });
        });

    });
            </script>
            <style>
            .draggable {
               width: 250px;
               height: 40px;
               line-height: 35px;
               text-align: center;
               background: '.$bgcolor.';
               border-radius: 3px;
               color: '.$color.';
               position:absolute;

            }
            </style>
        ';
        return $customDiv;
    }
}