/**
  * Mageants GiftCertificate Magento2 Extension                           
*/
require([
		"jquery",
		"giftcertificate",
		"Magento_Checkout/js/model/cart/totals-processor/default",
        "Magento_Checkout/js/model/cart/cache"],
        function($, _,  defaultTotal, cartCache) {
		jQuery(document).on('click', ".template-image", function () {
		 		var img=jQuery(this).attr('src');
		 		jQuery("#giftimage").val(img);
		 		jQuery(".fotorama__img").attr('src',img);
		 		jQuery(".fotorama__img--full").attr('src',img);
		 		jQuery("#template_id").val(jQuery(this).siblings('.temp_id').val())
		 		
		});

			jQuery(document).on('click', "#gift-code", function () {
					if(jQuery('#gift-code-arrow').hasClass("fa-arrow-down")){
						jQuery("#gift-code-arrow").removeClass("fa-arrow-down").addClass("fa-arrow-up");
					}
					else{
						jQuery("#gift-code-arrow").removeClass("fa-arrow-up").addClass("fa-arrow-down");
					}
				jQuery(".gift-code-block").toggle();
			
 			});
 			jQuery(document).on('submit', "#gift-certificate-form", function () {
 				var cat_ids=[];
 				var str='';
 				var button = jQuery("#applyCode").text();
 				var actionUrl = "";
 				if (button == "Apply") {
 					actionUrl = jQuery("#base-gift-url").val()+"giftcertificate/cart/apply";
 				}
 				else{
 					actionUrl = jQuery("#base-gift-url").val()+"giftcertificate/cart/cancel";
 				}
 				jQuery("#gift-certificate-form").prop( "disabled", true );
 				jQuery(".loading").toggle();
 				jQuery(".gift-code-block").css('opacity',0.2);
 				if(jQuery("#gift-certificate-code").val()==''){
 					jQuery(".error-msg").show(500);
 					jQuery(".loading").toggle();
 					jQuery(".gift-code-block").css('opacity',1);
 					jQuery("#gift-certificate-form").prop( "disabled", false );
 					return false;
 				}	
 				jQuery(".categoryids").each(function() {
 					var categoryids=jQuery(this).val();
 					cat_ids.push(jQuery(this).val());
 				});		
 					jQuery.ajax({
					type:'POST',
				  		data:{gift_code:jQuery("#gift-certificate-code").val(),categoryids:cat_ids},
						url:actionUrl,
						success:function(data){
						 var code = data;
						   if (code[0]=='3')
						   {
								jQuery("#message").html(code[1]);
								cartCache.set('totals',null);
                				defaultTotal.estimateTotals();
                				location.reload(true);
						   }
						   else if(code[0]=='5')
							{
							 jQuery("#message").html(code[1]);	
							 jQuery(".loading").hide();
 							jQuery(".gift-code-block").css('opacity',1);
 							jQuery("#gift-certificate-form").prop( "disabled", false );
 							return false;

							}
						   else
						   {
							jQuery("#message").html(code[1]);
						   }
							jQuery(".loading").hide();
 							jQuery(".gift-code-block").css('opacity',1);
 							jQuery("#gift-certificate-form").prop( "disabled", false );
				
						}	
					});		
			
			return false;
 			});

 			jQuery(document).on('click', "#check_status", function(){
 				jQuery("#gift-certificate-form").prop( "disabled", true );
 				jQuery(".loading").toggle();
 				jQuery(".gift-code-block").css('opacity',0.2);

				if(jQuery("#gift-certificate-code").val()==''){
 					jQuery(".error-msg").show(500);
					jQuery(".loading").toggle();
 					jQuery(".gift-code-block").css('opacity',1);
 					jQuery("#gift-certificate-form").prop( "disabled", false );

 					return false;
 				}	
 					jQuery.ajax({
					type:'POST',
				  		data:{gift_code:jQuery("#gift-certificate-code").val()},
						url:jQuery("#base-gift-url").val()+"giftcertificate/cart/check",
						success:function(data){
							jQuery("#message").html(data);
		 					jQuery(".loading").toggle();
		 					jQuery(".gift-code-block").css('opacity',1);
		 					jQuery("#gift-certificate-form").prop( "disabled", false );
						}	
				});		
			return false;
 			});
 			jQuery(".deletequote").click(function(){
 				var quoteid=jQuery(this).siblings('.quote-id').val();
 				var productId=jQuery(this).siblings('#productid').val();
 				jQuery(".loading-cart").toggle();
 				jQuery(".additional-info").css('opacity',0.2);
 				if(quoteid==''){
 					return false;
 				}
 					jQuery.ajax({
 							type:'POST',
				  		data:{quoteid:quoteid, productId:productId},
						url:jQuery("#base-gift-url").val()+"giftcertificate/cart/remove",
						success:function(data){
							location.reload(true);
 						}
 					});
 			});
 			jQuery(document).on('click','.switch-prices', function(){
				  if(jQuery(this).text()=='Buy With Your Price')
				  {
				    jQuery(this).text('Buy With System Price');
				  }
				  else
				  {
				     jQuery(this).text('Buy With Your Price');
				  }
				 	
				  jQuery(".gift-prices").toggle();
				  jQuery(".manual-price ").toggle();
  			});
  			jQuery('.manual-price').keyup(function () { 
   				 this.value = this.value.replace(/[^0-9\.]/g,'');
			});
 		
	});	