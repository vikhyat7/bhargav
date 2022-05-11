<?php
/**
 * @category Mageants GiftCertificate
 * @package Mageants_GiftCertificate
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\GiftCertificate\Controller\Cart;
/**
 * Check gift code Details
 */
class Check extends \Magento\Framework\App\Action\Action
{
    /**
     * helper object
     *
     * @var \Mageants\GiftCertificate\Helper\Data
     */
    protected $_helper;

	/**
     * category object
     *
     * @var \Magento\Catalog\Model\Category
     */
    protected $_categories;
	
    /**
     * model Account
     *
     * @var \Mageants\GiftCertificate\Model\Account
     */
    protected $modelAccount;


    /**
     * code list
     *
     * @var \Mageants\GiftCertificate\Model\Codelist
     */
    protected $_codelist;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Mageants\GiftCertificate\Helper\Data $helper
     * @param \Magento\Catalog\Model\Category $categories
     * @param \Mageants\GiftCertificate\Model\Account $modelAccount
     * @param \Mageants\GiftCertificate\Model\Codelist $codelist
     */
    public function __construct
	(\Magento\Framework\App\Action\Context $context,\Magento\Framework\Pricing\Helper\Data $helper,\Magento\Catalog\Model\Category $categories,\Mageants\GiftCertificate\Model\Account $modelAccount, \Mageants\GiftCertificate\Model\Codelist $codelist,\Magento\Sales\Model\OrderFactory $orderFactory)
    {
	     $this->_helper=$helper;
         $this->modelAccount=$modelAccount;
         $this->_categories = $categories;
         $this->_codelist = $codelist;
        $this->orderFactory = $orderFactory;
    	parent::__construct($context);          
	}

    /**
     *  chek gift code and return detail of the code
     */
    public function execute()
    {
        $data=$this->getRequest()->getPostValue();
        $availableCode=$this->_codelist->getCollection()->addFieldToFilter('code',trim($data['gift_code']))->addFieldToFilter('allocate','1');
        //print_r($availableCode->getData());exit
         if(empty($availableCode->getData())):
            echo "<span style='color:#f00'>Invalid Gift Certificate</span>";
         	exit;
         else:
            $account = $this->modelAccount->getCollection()->addFieldToFilter('gift_code',trim($data['gift_code']))->addFieldToFilter('status', 1);
            if(!empty($account->getData())):
                $html='';
                    foreach($account as $certifiate){
                        $orderIncrementId = $certifiate['order_increment_id'];
                       // var_dump($orderIncrementId);
                        $order = $this->orderFactory->create()->loadByIncrementId($orderIncrementId);
                        if ($order->getStatus() == "canceled" || $order->getStatus() == "closed") {
                            $error= "<span style='color:#f00'>Invalid Gift Certificate Code</span>";
                            echo $error;
                            exit();
                        }
                        if($certifiate->getExpireAt()!='1970-01-01' && $certifiate->getExpireAt()!='0000-00-00' ):
                            $currentDate= date('Y-m-d');
                            if($currentDate > $certifiate->getExpireAt()):
                                echo  "<span style='color:#f00'>Sorry, This Gift Card Has Been Expired</span>";
                                 exit;   
                            endif;
                        endif;
                        $category_ids=explode(",", $certifiate->getCategories());
                        $category_name='';
                        foreach($category_ids as $id){
                            $cat=$this->_categories->load($id);    
                            $category_name=$category_name.",".$cat->getName();
                            $category_name=substr($category_name,1);
                        }
                       // echo $certifiate->getCurrentBalance();exit;
                        if ($certifiate->getDiscountType() == 'percent') {
                            if($certifiate->getCurrentBalance()==0):$type='used';else:$type='Avalaiable';endif;
                            $html.="<div><span>Status: </span><span style='font-weight:bold'>".$type."</span></div><div><span>Available Discount :</span><span style='font-weight:bold'>".$certifiate->getCurrentBalance()."</span> </div>";
                            echo $html;
                            exit;
                        } else {
                            if($certifiate->getCurrentBalance()==0):$type='used';else:$type='Avalaiable';endif;
                            $html.="<div><span>Status: </span><span style='font-weight:bold'>".$type."</span></div><div><span>Current Balance:</span><span style='font-weight:bold'>".$this->_helper->currency($certifiate->getCurrentBalance(),true,false)."</span> </div>";
                            echo $html;
                            exit;
                        }
                        // if ($certifiate->getDiscountType() == 'percent') {
                        //     if($certifiate->getCurrentBalance()==0):$type='used';else:$type='Avalaiable';endif;
                        //     if($certifiate['avail_bal'] == NULL)
                        //     {
                        //         $html.="<div><span>Status: </span><span style='font-weight:bold'>".$type."</span></div><div><span>Available Discount :</span><span style='font-weight:bold'>".$certifiate['initial_code_value']."%</span> </div>";
                        //         echo $html;
                        //         exit;
                        //     }
                        //     else
                        //     {
                        //         $html.="<div><span>Status: </span><span style='font-weight:bold'>".$type."</span></div><div><span>Available Discount :</span><span style='font-weight:bold'>".$certifiate['avail_bal']."%</span> </div>";
                        //         echo $html;
                        //         exit;
                        //     }  
                        // } 
                        // else {
                        //     if($certifiate->getCurrentBalance()==0):$type='used';else:$type='Avalaiable';endif;
                        //     // var_dump($certifiate['avail_bal']);
                        //     if($certifiate['avail_bal'] == NULL)
                        //     {
                        //         $html.="<div><span>Status: </span><span style='font-weight:bold'>".$type."</span></div><div><span>Current Balance:</span><span style='font-weight:bold'>".$this->_helper->currency($certifiate['initial_code_value'],true,false)."</span> </div>";
                        //         echo $html;
                        //         exit;
                        //     }
                        //     else
                        //     {
                        //         $html.="<div><span>Status: </span><span style='font-weight:bold'>".$type."</span></div><div><span>Current Balance:</span><span style='font-weight:bold'>".$this->_helper->currency($certifiate['avail_bal'],true,false)."</span> </div>";
                        //         echo $html;
                        //         exit;
                        //     }
                        // }
                    }
                return;
			else:
				echo "<span style='color:#f00'>Invalid Gift Certificate</span>";
            endif;
         endif; 
 	}
}
