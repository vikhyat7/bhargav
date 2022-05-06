<?php

/**

 * @category Mageants GiftCertificate

 * @package Mageants_GiftCertificate

 * @copyright Copyright (c) 2016 Mageants

 * @author Mageants Team <support@mageants.com>

 */



namespace Mageants\GiftCertificate\Controller\Cart;

use Magento\Framework\View\Result\PageFactory;



/**

 * Apply the Gift code in checkout page

 */

class Apply extends \Magento\Framework\App\Action\Action

{

    /**

     * helper object

     *

     * @var \Mageants\GiftCertificate\Helper\Data

     */

    protected $_helper;

    /**

     * checkout session

     *

     * @var \Magento\Checkout\Model\Session

     */

    protected $_checkoutSession;
    
    protected $resultJsonFactory;
    protected $orderFactory; 
    

    /**

     * @param \Magento\Backend\Block\Template\Context $context

     * @param \Mageants\GiftCertificate\Helper\Data $helper

     * @param \Magento\Checkout\Model\Session $checkoutSession

     */

    public function __construct

    (\Magento\Framework\App\Action\Context $context,\Magento\Checkout\Model\Session $checkoutSession,\Mageants\GiftCertificate\Helper\Data $helper,\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,\Magento\Sales\Model\OrderFactory $orderFactory,\Magento\Framework\App\Request\Http $request
        // \Magento\Framework\Message\ManagerInterface $messageManager
    )

    {    
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_checkoutSession=$checkoutSession;
        // $this->_messageManager = $messageManager;
        $this->_helper=$helper;
        $this->request = $request;

        $this->orderFactory = $orderFactory;
        parent::__construct($context);          

    }



    /**

     * Perform Apply Action

     */ 

    public function execute()

    {
        $result_return = $this->resultJsonFactory->create();
        $this->_checkoutSession->unsGift();

        $data=$this->getRequest()->getPostValue();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $catids=$data['categoryids'];
        $quote = $objectManager->get('\Magento\Checkout\Model\Cart');

        $gift_card_subtotal = '0';
        if($quote->getItems()->count() > 1){
            foreach ($quote->getItems()->getData() as $key => $value) {
                if($value['product_type'] == 'giftcertificate'){
                    $gift_card_subtotal += $value['row_total'];
                }
            }
        }
       // $totals = $quote->getQuote()->getTotals();
        //$cartSubtotal = $totals['subtotal']['value'];

        $subtotal=0;

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->get('Magento\Checkout\Model\Cart');
        $totals = $cart->getQuote()->getTotals();

        $cart_subtotal = $totals['subtotal']['value'] - $gift_card_subtotal;

        $gifCodes = $objectManager->get('Mageants\GiftCertificate\Model\Codelist');

         $availableCode=$objectManager->get('Mageants\GiftCertificate\Model\Account')->getCollection()->addFieldToFilter('gift_code',trim($data['gift_code']))->addFieldToFilter('status', 1);

         if(empty($availableCode->getData())):

            $error= "<span style='color:#f00'>Invalid Gift Certificate</span>";

            $result=array(0=>'1',1=>$error);
            
            
            //echo json_encode($result);

            return $result_return->setData($result);

         else:

         $cat_array=array();

         foreach($availableCode as $catlist){

             $cat_array=explode(",",$catlist->getCategories());

         }

         foreach ($availableCode->getData() as $code) {
            $orderIncrementId = $code['order_increment_id'];
            $order = $this->orderFactory->create()->loadByIncrementId($orderIncrementId);
            if ($order->getStatus() == "canceled" || $order->getStatus() == "closed") {
                $error= "<span style='color:#f00'>Invalid Gift Certificate Code</span>";

                    $result=array(0=>'5',1=>$error);
                    
                   // echo json_encode($result);

                return $result_return->setData($result);
                # code...
            }
         }


         if(!empty($cat_array)):

            if(!is_array($catids)):

                 $key=array_search($catids, $cat_array);

                  if(!$key):

                        $error= "<span style='color:#f00'>Sorry, Gift Certificate not available for this category/Categories</span>";

                            $result=array(0=>'5',1=>$error);
                            
                           // echo json_encode($result);

                        return $result_return->setData($result);

                    endif;

            else:

        $key_val=0; 
        $check_flag=false;
        foreach($catids as $catid){

            $id=explode(",",$catid);

            $size=sizeof($id);

            foreach($id as $i)

            {

                foreach ($cat_array as $cat) 

                {

                    if($cat==$i)

                    {
                        $check_flag=true;
                        $key_val=1;

                    }

                }

            }
            if($check_flag==true){

                $subtotal += intval($id[$size-1]);

                $check_flag=false;
             } 


        }
        

        if($key_val==0):

        if(empty($key) || $key==0):

                    $error= "<span style='color:#f00'>Sorry,Gift Certificate not available for this category/Categories</span>";

                        $result=array(0=>'5',1=>$error);

                        //echo json_encode($result);

                    return $result_return->setData($result);

                endif;

                endif;

        endif;

        endif;

         $certificate_value=0;

            foreach($availableCode as $code){
               
                if($code->getCurrentBalance()==0):

                    $error= "<span style='color:black'>You Don't have enough balance.</span>";

                    $result=array(0=>'2',1=>$error);

                    return $result_return->setData($result);

                endif;

                if(!$this->_helper->allowSelfUse()):

                    if($code->getCustomerId()==$this->_helper->getCustomerId()):

                        $error= "<span style='color:#f00'>Sorry, You cannot use certificate for yourself</span>";

                        $result=array(0=>'4',1=>$error);

                        return $result_return->setData($result);

                    endif;

                endif;

                if($code->getExpireAt()!='0000-00-00' && $code->getExpireAt()!='1970-01-01'):

                    $currentDate= date('Y-m-d');

                    if($currentDate > $code->getExpireAt()):

                         $error= "<span style='color:#f00'>Sorry, This Gift Card Has Been Expired</span>";

                        $result=array(0=>'4',1=>$error);

                            

                         return $result_return->setData($result);

                    endif;

                endif;

                $certificate_value=  $code->getCurrentBalance();
                
                    $quote = $objectManager->get('\Magento\Checkout\Model\Cart');

                    $gift_value=$subtotal;
                    /*if ($subtotal != $cartSubtotal) {
                        $subtotal = $cartSubtotal; 
                    }*/

                    $gift_value=$subtotal;
                    // var_dump($certificate_value);
                    if($certificate_value < $subtotal) {
                        $gift_value=$certificate_value;
                    }
                    $action = $this->request->getFullActionName();
                    /*echo $action;
                    var_dump($gift_value);exit();*/
                    $accund_id=$code->getAccountId();

                    $updateblance=$code->getCurrentBalance() - $gift_value;

                    $result=array(0=>'3',1=>'Gift Certificate Accepted');

                    //echo json_encode($result);
                  
                    $result_return->setData($result);
                    $_SESSION['custom_gift'] = $gift_value;
                    // var_dump($code->getDiscountType());
                    // exit();
                    if ($code->getDiscountType() == "percent") {

                        if ($certificate_value >= 100) {
                            $discount = 1;
                        } else {
                            $discount = '0.'.intval($certificate_value);
                        }
                        $gift_value = intval($cart_subtotal) * $discount;
                        $sub_total = intval($cart_subtotal);
                        $formula = $sub_total/100*$code['initial_code_value'];
                        
                        if($formula > $code['initial_code_value'])
                        {
                           $gift_value = $code['initial_code_value'];
                        }
                        else
                        {
                            $gift_value = intval($cart_subtotal) * $discount;
                        }
                    }
                    
                $this->_checkoutSession->setGift($gift_value); 

                $this->_checkoutSession->setGiftCertificateCode($code->getGiftCode());  

                $this->_checkoutSession->setAccountid($accund_id);  

                $this->_checkoutSession->setGiftbalance($updateblance);

                $this->_checkoutSession->getQuote()->collectTotals()->save();
                $cartQuote = $quote->getQuote();
                $cartQuote->getShippingAddress()->setCollectShippingRates(true);
                return $result_return;  

            }

        endif;

    }

}