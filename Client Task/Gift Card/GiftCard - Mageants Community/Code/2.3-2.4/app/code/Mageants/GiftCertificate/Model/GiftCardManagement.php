<?php


namespace Mageants\GiftCertificate\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Coupon management object.
 */
class GiftCardManagement implements \Mageants\GiftCertificate\Api\GiftCardManagementInterface
{
    protected $quoteRepository;

    private $accountFactory;

    private $quoteFactory;

    private $giftQuoteRepository;

    private $quoteResource;

    private $escaper;

    private $codeRepository;

    private $collectionFactory;

    private $codes = [];

    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Mageants\GiftCertificate\Helper\Data $helper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Checkout\Model\Cart $cart
    ) {
        $this->quoteRepository = $quoteRepository;
      
        $this->escaper = $escaper;  
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_checkoutSession=$checkoutSession;
        $this->cart = $cart;
        $this->_helper=$helper;
    }

    /**
     * {@inheritdoc}
     */
    public function set($cartId, $giftCard)
    { 
        
        $result_return = $this->resultJsonFactory->create();
        $this->_checkoutSession->unsGift();

        //$data=$this->getRequest()->getPostValue();

        $catids=$this->getCategories();

        $subtotal=0;

         $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $gifCodes = $objectManager->get('Mageants\GiftCertificate\Model\Codelist');

         $availableCode=$objectManager->get('Mageants\GiftCertificate\Model\Account')->getCollection()->addFieldToFilter('gift_code',trim($giftCard))->addFieldToFilter('status', 1);

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


                    $totals = $quote->getQuote()->getTotals();
                    $cartSubtotal = $totals['subtotal']['value'];
                    if ($subtotal != $cartSubtotal) {
                        $subtotal = $cartSubtotal; 
                    }

                    $gift_value=$subtotal;
                    
                    if($certificate_value < $subtotal) {
                        $gift_value=$certificate_value;
                    }

                    $accund_id=$code->getAccountId();

                    $updateblance=$code->getCurrentBalance() - $gift_value;

                    if ($code->getDiscountType() == "percent") {
                        $certificate_value = $code->getPercentage();

                        if ($certificate_value >= 100) {
                            $discount = 1;
                        } else {
                            $discount = $certificate_value;
                        }
                        $gift_value = ($code['current_balance']*$discount)/100;
                                                
                        if($gift_value > intval($cartSubtotal)){
                            $gift_value = intval($cartSubtotal);
                        } else if($gift_value > $code->getCurrentBalance()){
                            $gift_value = $code->getCurrentBalance();
                        }

                        $updateblance = $code->getCurrentBalance() - $gift_value;
                    }

                $_SESSION['custom_gift'] = $gift_value;
                $result=array(0=>'3',1=>'Gift Certificate Accepted');
                $result_return->setData($result);

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

       // return $giftCard;
    }

    private function updateTotalsInQuote(\Magento\Quote\Model\Quote $quote)
    {
        $quote->getShippingAddress()->setCollectShippingRates(true);
        $quote->collectTotals();
        $quote->setDataChanges(true);
        $this->quoteRepository->save($quote);

        return true;
    }
    public function getCategories()
    {
        $items = $this->cart->getQuote()->getAllVisibleItems();
        $cat_ids = [];
        if ($items) {
            foreach ($items as $item) {
                $cat_id = "";
                foreach ($item->getProduct()->getCategoryIds() as $categoryid) {
                    if ($cat_id == "") {
                        $cat_id = $categoryid;
                    }
                    else{
                        $cat_id = $cat_id.",".$categoryid;                        
                    }
                }
                $cat_ids[] = $cat_id;
            }
        }
        return $cat_ids;
    }
}
