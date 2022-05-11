# Gift Card

	Current version number: 2.0.4
	Date :- 12/05/2021	
	************************************************************
    Bug fixed
	************************************************************
	--> Display gift card type in sales order.

	--> Shipping method should be disable when we choose virtual gift card type from frontend.

	--> Add gift card preview functinality in frontend, so now customer can preview gift card before purchase.

	--> Zoom in zoom out button not display for default gift card image. Extension working fine in all magento version.

	************************************************************
    File list
	************************************************************
	Mageants/GiftCertificate/view/adminhtml/templates/Custom.phtml
	Mageants/GiftCertificate/Controller/Cart/Apply.php
	Mageants/GiftCertificate/view/frontend/web/js/giftcertificate.js
	Mageants/GiftCertificate/view/frontend/web/js/view/checkout/payment/checkout-gift-code.js
	Mageants/GiftCertificate/Controller/Cart/Check.php
	Mageants/GiftCertificate/Controller/Cart/ApplyCheckOut.php
	Mageants/GiftCertificate/view/frontend/templates/giftcertificate.phtml
	Mageants/GiftCertificate/Model/Product/Type/Gift.php
	Mageants/GiftCertificate/Setup/InstallSchema.php
	Mageants/GiftCertificate/Setup/InstallData.php
	Mageants/GiftCertificate/view/frontend/templates/product/list.phtml
	Mageants/GiftCertificate/etc/di.xml
	Mageants/GiftCertificate/Block/Adminhtml/Account/Edit/Tabs/Resend.php
	Mageants/GiftCertificate/Controller/Adminhtml/Gcaccount/Save.php
	Mageants/GiftCertificate/Observer/SendGiftcertificateMali.php
	Mageants/GiftCertificate/view/frontend/templates/order/totals.phtml
	Mageants/GiftCertificate/composer.json
	Mageants/GiftCertificate/etc/module.xml

************************************************************************************************************************************************************************************

	Current version number: 2.0.3
	Date :- 25/02/2020	
	************************************************************
    Bug fixed
	************************************************************
	--> When user add template without image then it's display error.
	--> When user remove particular code then it's display error --> https://nimb.ws/AnwSlJ Mageants solved bugs and working fine in all magento version.

	************************************************************
    File list
	************************************************************
	--> app/code/Mageants/GiftCertificate/Controller/Adminhtml/Gcimages/Save.php
	--> app/code/Mageants/GiftCertificate/Controller/Adminhtml/Gcimages/Delete.php
	--> app/code/Mageants/GiftCertificate/Controller/Adminhtml/Index/Delete.php
	--> app/code/Mageants/GiftCertificate/composer.json
	--> app/code/Mageants/GiftCertificate/etc/module.xml

************************************************************************************************************************************************************************************

	Current version number: 2.0.2
	Date :- 09/02/2019	
	************************************************************
    Bug fixed
	************************************************************
	--> We add new functionality in Gift card extension now customer also apply gift certificate code in checkout page.
	--> Add functionality to cancel gift card code in view and edit cart and checkout page.
	--> Send gift card code in mail on invoice generate.
	--> Disable ajax call when gift card product add to cart.
	--> Change "Add to cart" to "View Gift Cart" on search gift card.


************************************************************************************************************************************************************************************

	Current version number: 2.0.1
	Date :- 16/01/2019	
	************************************************************
    Bug fixed
	************************************************************
	--> Update module version name in composer.json file same as module.xml file.

************************************************************************************************************************************************************************************

	Current version number: 2.0.1
	Date :- 23/7/2018	
	************************************************************
    Bug fixed
	************************************************************
	--> When user click on resend email link in backend then resend email not send to user and give error now resend email send and 
	    it's working fine. --> http://nimb.ws/FAKpXB
	
	--> Once we purchase gift card product then receiver will get a gift code via mail but when user apply this code on cart page 
	    at that time order total display = 0, now this issue solve and working fine. --> http://nimb.ws/acdRnB
